<?php

namespace Webkul\Support\Services;

use Closure;
use Filament\Schemas\Schema;
use InvalidArgumentException;

class SchemaRegistry
{
    /**
     * Store schema modifiers grouped by resource class and package
     * Structure: [ResourceClass => [package => [modifier, priority]]]
     */
    protected static array $modifiers = [];

    /**
     * Store global modifiers that apply to all schemas
     * Structure: [package => [modifier, priority]]
     */
    protected static array $globalModifiers = [];

    /**
     * Resolve a modifier to a callable
     */
    protected static function resolveModifier(mixed $modifier): callable
    {
        // If it's already a Closure, return it
        if ($modifier instanceof Closure) {
            return $modifier;
        }

        // Handle string syntax: 'Class@method'
        if (is_string($modifier) && str_contains($modifier, '@')) {
            [$class, $method] = explode('@', $modifier, 2);

            return [$class, $method];
        }

        // Handle invokable class string
        if (is_string($modifier) && class_exists($modifier)) {
            return new $modifier;
        }

        // Handle array callable [Class::class, 'method'] or already callable
        if (is_callable($modifier)) {
            return $modifier;
        }

        throw new InvalidArgumentException(
            'Modifier must be a Closure, callable, array [Class::class, \'method\'], or string \'Class@method\''
        );
    }

    /**
     * Register a schema modifier for a specific resource
     *
     * @param  string  $resourceClass  Full class name of the resource (e.g., App\Filament\Resources\ProductResource)
     * @param  string  $package  Package identifier
     * @param  Closure|callable|string|array  $modifier  Callback that receives Schema instance and returns modified Schema
     *                                                   Can be: Closure, [Class::class, 'method'], 'Class@method', or invokable class
     * @param  int  $priority  Lower number = higher priority (default: 100)
     * @param  string  $type  Schema type: 'form' or 'infolist' (default: 'form')
     */
    public static function register(string $resourceClass, string $package, Closure|callable|string|array $modifier, int $priority = 100, string $type = 'form'): void
    {
        if (! isset(static::$modifiers[$resourceClass])) {
            static::$modifiers[$resourceClass] = [];
        }

        static::$modifiers[$resourceClass][$package] = [
            'modifier' => $modifier,
            'priority' => $priority,
            'type'     => $type,
        ];
    }

    /**
     * Register a global modifier that applies to all schemas
     *
     * @param  string  $package  Package identifier
     * @param  Closure|callable|string|array  $modifier  Callback that receives Schema instance and resource class
     *                                                   Can be: Closure, [Class::class, 'method'], 'Class@method', or invokable class
     * @param  int  $priority  Lower number = higher priority (default: 100)
     * @param  string  $type  Schema type: 'form', 'infolist', or 'both' (default: 'both')
     */
    public static function registerGlobal(string $package, Closure|callable|string|array $modifier, int $priority = 100, string $type = 'both'): void
    {
        static::$globalModifiers[$package] = [
            'modifier' => $modifier,
            'priority' => $priority,
            'type'     => $type,
        ];
    }

    /**
     * Apply all registered modifiers to a schema instance
     *
     * @param  Schema  $schema  The schema instance to modify
     * @param  string  $resourceClass  The resource class name
     * @param  string  $schemaType  The type of schema: 'form' or 'infolist'
     * @return Schema Modified schema instance
     */
    public static function applyModifiers(Schema $schema, string $resourceClass, string $schemaType = 'form'): Schema
    {
        $allModifiers = [];

        // Collect global modifiers
        foreach (static::$globalModifiers as $package => $data) {
            // Check if this modifier applies to the current schema type
            if ($data['type'] === 'both' || $data['type'] === $schemaType) {
                $allModifiers[] = [
                    'package'  => $package,
                    'modifier' => $data['modifier'],
                    'priority' => $data['priority'],
                    'type'     => 'global',
                ];
            }
        }

        // Collect resource-specific modifiers
        if (isset(static::$modifiers[$resourceClass])) {
            foreach (static::$modifiers[$resourceClass] as $package => $data) {
                // Check if this modifier applies to the current schema type
                if ($data['type'] === $schemaType) {
                    $allModifiers[] = [
                        'package'  => $package,
                        'modifier' => $data['modifier'],
                        'priority' => $data['priority'],
                        'type'     => 'specific',
                    ];
                }
            }
        }

        // Sort by priority (lower number = higher priority)
        usort($allModifiers, fn ($a, $b) => $a['priority'] <=> $b['priority']);

        // Apply each modifier
        foreach ($allModifiers as $modifierData) {
            $modifier = static::resolveModifier($modifierData['modifier']);

            if ($modifierData['type'] === 'global') {
                // Global modifiers receive both schema and resource class
                $schema = $modifier($schema, $resourceClass);
            } else {
                // Specific modifiers receive only schema
                $schema = $modifier($schema);
            }
        }

        return $schema;
    }

    /**
     * Apply modifiers only from specific packages
     *
     * @param  Schema  $schema  The schema instance to modify
     * @param  string  $resourceClass  The resource class name
     * @param  array  $packages  Array of package identifiers to include
     * @param  string  $schemaType  The type of schema: 'form' or 'infolist'
     * @return Schema Modified schema instance
     */
    public static function applyModifiersForPackages(Schema $schema, string $resourceClass, array $packages, string $schemaType = 'form'): Schema
    {
        $allModifiers = [];

        // Collect global modifiers for allowed packages
        foreach (static::$globalModifiers as $package => $data) {
            if (in_array($package, $packages) && ($data['type'] === 'both' || $data['type'] === $schemaType)) {
                $allModifiers[] = [
                    'package'  => $package,
                    'modifier' => $data['modifier'],
                    'priority' => $data['priority'],
                    'type'     => 'global',
                ];
            }
        }

        // Collect resource-specific modifiers for allowed packages
        if (isset(static::$modifiers[$resourceClass])) {
            foreach (static::$modifiers[$resourceClass] as $package => $data) {
                if (in_array($package, $packages) && $data['type'] === $schemaType) {
                    $allModifiers[] = [
                        'package'  => $package,
                        'modifier' => $data['modifier'],
                        'priority' => $data['priority'],
                        'type'     => 'specific',
                    ];
                }
            }
        }

        // Sort by priority
        usort($allModifiers, fn ($a, $b) => $a['priority'] <=> $b['priority']);

        // Apply each modifier
        foreach ($allModifiers as $modifierData) {
            $modifier = static::resolveModifier($modifierData['modifier']);

            if ($modifierData['type'] === 'global') {
                $schema = $modifier($schema, $resourceClass);
            } else {
                $schema = $modifier($schema);
            }
        }

        return $schema;
    }

    /**
     * Remove a package's modifier for a specific resource
     */
    public static function unregister(string $resourceClass, string $package): void
    {
        if (isset(static::$modifiers[$resourceClass][$package])) {
            unset(static::$modifiers[$resourceClass][$package]);
        }
    }

    /**
     * Remove a global modifier
     */
    public static function unregisterGlobal(string $package): void
    {
        unset(static::$globalModifiers[$package]);
    }

    /**
     * Clear all modifiers for a specific resource
     */
    public static function clearResource(string $resourceClass): void
    {
        unset(static::$modifiers[$resourceClass]);
    }

    /**
     * Clear all modifiers (useful for testing)
     */
    public static function clear(): void
    {
        static::$modifiers = [];
        static::$globalModifiers = [];
    }

    /**
     * Get registered packages for a specific resource
     */
    public static function getRegisteredPackages(string $resourceClass): array
    {
        return array_keys(static::$modifiers[$resourceClass] ?? []);
    }

    /**
     * Get all registered global packages
     */
    public static function getGlobalPackages(): array
    {
        return array_keys(static::$globalModifiers);
    }

    /**
     * Get all resources that have modifiers registered
     */
    public static function getRegisteredResources(): array
    {
        return array_keys(static::$modifiers);
    }
}
