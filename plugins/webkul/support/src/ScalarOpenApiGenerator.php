<?php

declare(strict_types=1);

namespace Webkul\Support;

use Knuckles\Camel\Extraction\Metadata;
use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\SecurityGenerator;

final class ScalarOpenApiGenerator extends SecurityGenerator
{
    public function root(array $root, array $groupedEndpoints): array
    {
        // First, let parent handle security schemes
        $root = parent::root($root, $groupedEndpoints);

        /** @see https://github.com/scalar/scalar/blob/main/packages/types/src/api-reference/api-reference-configuration.ts#L225 */
        $scalarConfig = [
            'data-configuration' => htmlspecialchars(
                json_encode([
                    'theme'             => 'kepler',
                    'defaultHttpClient' => [
                        'targetKey' => 'js',
                        'clientKey' => 'fetch',
                    ],
                    'hiddenClients'      => [],
                    'customCss'          => '',
                    'hideModels'         => false,
                    'hideDownloadButton' => false,
                    'defaultOpenAllTags' => false,
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ENT_QUOTES
            ),
        ];
        $this->config->data['external']['html_attributes'] = $scalarConfig;

        // Fix security scheme if it got duplicated by array_merge_recursive
        if (isset($root['components']['securitySchemes']['default'])) {
            $scheme = $root['components']['securitySchemes']['default'];
            if (isset($scheme['type']) && is_array($scheme['type'])) {
                // Take the first value from arrays created by array_merge_recursive
                $root['components']['securitySchemes']['default'] = [
                    'type'        => $scheme['type'][0],
                    'scheme'      => $scheme['scheme'][0],
                    'description' => $scheme['description'][0],
                ];
            }
        }

        // Fix global security if it got duplicated
        if (isset($root['security']) && is_array($root['security'])) {
            // Remove duplicates from security array
            $root['security'] = array_values(array_unique($root['security'], SORT_REGULAR));
        }

        $tags = [];
        $tagsHashmap = [];

        foreach ($groupedEndpoints as $groupedEndpoint) {
            $currentGroupTags = [
                'name' => $groupedEndpoint['name'],
            ];
            $grouped = [];
            $hasEndpointsWithoutSubgroup = false;

            foreach ($groupedEndpoint['endpoints'] as $endpoint) {
                /** @var Metadata $metadata */
                $metadata = $endpoint['metadata'];

                if (! $metadata->subgroup) {
                    $hasEndpointsWithoutSubgroup = true;

                    continue;
                }

                $tagName = self::generateTagNameFromMetadata($metadata);

                if (isset($tagsHashmap[$tagName])) {
                    continue;
                }

                $tagsHashmap[$tagName] = 1;
                $tagGroup = [
                    'name'          => $tagName,
                    'x-displayName' => $metadata->subgroup,
                    'description'   => $metadata->subgroupDescription,
                ];

                $tags[] = $tagGroup;
                $grouped[] = $tagGroup['name'];
            }

            // Don't sort - maintain the order endpoints are defined in routes
            // Only add default tag if there are actually endpoints without a subgroup
            if ($hasEndpointsWithoutSubgroup) {
                $currentGroupTags['tags'] = array_merge(
                    [$currentGroupTags['name'].config('scribe.groups.default')],
                    $grouped
                );
            } else {
                $currentGroupTags['tags'] = $grouped;
            }

            $root['x-tagGroups'][] = $currentGroupTags;
        }

        // set default(_UNGROUPED) tag
        $tags[] = [
            'name' => config('scribe.groups.default'),
        ];

        $root['tags'] = $tags;

        return $root;
    }

    public function pathItem(array $pathItem, array $groupedEndpoints, OutputEndpointData $endpoint): array
    {
        // Call parent to handle security and other default OpenAPI generation
        $pathItem = parent::pathItem($pathItem, $groupedEndpoints, $endpoint);

        // Add our custom tag generation for Scalar grouping
        /** @var Metadata $metadata */
        $metadata = $endpoint['metadata'];
        $tagName = self::generateTagNameFromMetadata($metadata);

        $pathItem['tags'] = [$tagName];

        // Ensure Accept header is documented as application/json
        if (! isset($pathItem['parameters'])) {
            $pathItem['parameters'] = [];
        }

        $pathItem['parameters'][] = [
            'name'     => 'Accept',
            'in'       => 'header',
            'required' => true,
            'schema'   => [
                'type'    => 'string',
                'default' => 'application/json',
            ],
            'description' => 'Must be application/json',
        ];

        return $pathItem;
    }

    private static function generateTagNameFromMetadata(Metadata $metadata): string
    {
        $name = $metadata->groupName;
        $name .= $metadata->subgroup ? "_{$metadata->subgroup}" : config('scribe.groups.default');

        return str_replace(' ', '_', $name);
    }
}
