<?php

namespace LemonSqueezy\Webhook\Deserializer;

/**
 * Deserializes webhook payloads
 *
 * Webhook payloads are already in JSON:API format with full data structures.
 * This deserializer returns the raw data as-is, allowing flexibility in how
 * applications want to consume the webhook event information.
 */
class EventPayloadDeserializer
{
    /**
     * Deserialize webhook payload data
     *
     * Returns the raw webhook data as-is. The data is already properly
     * formatted from the LemonSqueezy API in JSON:API format.
     *
     * @param array $data The webhook data segment (with 'type', 'id', 'attributes')
     * @return mixed The webhook data (unchanged)
     */
    public function deserialize(array $data): mixed
    {
        // Return raw data - webhook payloads are already properly formatted
        return $data;
    }

    /**
     * Register a custom type mapping
     *
     * This method is kept for backward compatibility but is no longer used.
     * The deserializer returns raw data for all types.
     *
     * @param string $type The JSON:API type
     * @param string $entityClass Unused (kept for compatibility)
     * @return self
     */
    public function registerType(string $type, string $entityClass): self
    {
        // Type mapping no longer needed - we return raw data for all types
        return $this;
    }
}
