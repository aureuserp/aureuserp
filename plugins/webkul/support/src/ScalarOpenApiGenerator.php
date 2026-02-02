<?php

declare(strict_types=1);

namespace Webkul\Support;

use Knuckles\Camel\Extraction\Metadata;
use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\OpenApiGenerator;

final class ScalarOpenApiGenerator extends OpenApiGenerator
{
    public function root(array $root, array $groupedEndpoints): array
    {
        /** @see https://github.com/scalar/scalar/blob/main/packages/types/src/api-reference/api-reference-configuration.ts#L225 */
        $scalarConfig = [
            'data-configuration' => htmlspecialchars(
                json_encode([
                    'theme' => 'kepler',
                    'defaultHttpClient' => [
                        'targetKey' => 'js',
                        'clientKey' => 'fetch',
                    ]
                ], JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ENT_QUOTES
            ),
        ];
        $this->config->data['external']['html_attributes'] = $scalarConfig;

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

                if (!$metadata->subgroup) {
                    $hasEndpointsWithoutSubgroup = true;
                    continue;
                }

                $tagName = self::generateTagNameFromMetadata($metadata);

                if (isset($tagsHashmap[$tagName])){
                    continue;
                }

                $tagsHashmap[$tagName] = 1;
                $tagGroup = [
                    'name' => $tagName,
                    'x-displayName' => $metadata->subgroup,
                    'description' => $metadata->subgroupDescription
                ];

                $tags[] = $tagGroup;
                $grouped[] = $tagGroup['name'];
            }

            // Don't sort - maintain the order endpoints are defined in routes
            // Only add default tag if there are actually endpoints without a subgroup
            if ($hasEndpointsWithoutSubgroup) {
                $currentGroupTags['tags'] = array_merge(
                    [$currentGroupTags['name'] . config('scribe.groups.default')],
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
        /** @var Metadata $metadata */
        $metadata = $endpoint['metadata'];
        $tagName = self::generateTagNameFromMetadata($metadata);

        $pathItem['tags'] = [$tagName];

        return $pathItem;
    }
    
    private static function generateTagNameFromMetadata(Metadata $metadata): string
    {
        $name = $metadata->groupName;
        $name .= $metadata->subgroup ? "_{$metadata->subgroup}" : config('scribe.groups.default');

        return str_replace(' ','_', $name);
    }
}