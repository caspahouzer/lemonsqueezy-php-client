<?php

namespace LemonSqueezy\Serializer;

/**
 * Serializer for JSON:API formatted responses
 */
class JsonApiSerializer
{
    /**
     * Deserialize a JSON:API response to data array
     */
    public static function deserialize(array $response): array
    {
        $result = [];

        if (isset($response['data'])) {
            $result['data'] = $response['data'];
        }

        if (isset($response['meta'])) {
            $result['meta'] = $response['meta'];
        }

        if (isset($response['included'])) {
            $result['included'] = $response['included'];
        }

        if (isset($response['links'])) {
            $result['links'] = $response['links'];
        }

        return $result;
    }

    /**
     * Serialize data to JSON:API format
     */
    public static function serialize(array $data, string $type): array
    {
        return [
            'data' => [
                'type' => $type,
                'attributes' => $data,
            ],
        ];
    }
}
