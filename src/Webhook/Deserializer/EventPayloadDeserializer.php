<?php

namespace LemonSqueezy\Webhook\Deserializer;

use LemonSqueezy\Model\Entities\{
    Users,
    Stores,
    Products,
    Variants,
    Prices,
    Files,
    Customers,
    Orders,
    OrderItems,
    Subscriptions,
    SubscriptionInvoices,
    SubscriptionItems,
    Discounts,
    DiscountRedemptions,
    LicenseKeys,
    Webhooks,
    Checkouts,
    Affiliates,
    UsageRecords
};
use LemonSqueezy\Exception\LemonSqueezyException;

/**
 * Deserializes webhook payloads to typed entity models
 *
 * Maps JSON:API type fields to corresponding Model entity classes,
 * then hydrates the data into proper model instances.
 */
class EventPayloadDeserializer
{
    /**
     * Mapping of JSON:API types to entity classes
     *
     * @var array<string, string>
     */
    private array $typeMap = [
        'users' => Users::class,
        'stores' => Stores::class,
        'products' => Products::class,
        'variants' => Variants::class,
        'prices' => Prices::class,
        'files' => Files::class,
        'customers' => Customers::class,
        'orders' => Orders::class,
        'order-items' => OrderItems::class,
        'subscriptions' => Subscriptions::class,
        'subscription-invoices' => SubscriptionInvoices::class,
        'subscription-items' => SubscriptionItems::class,
        'discounts' => Discounts::class,
        'discount-redemptions' => DiscountRedemptions::class,
        'license-keys' => LicenseKeys::class,
        'webhooks' => Webhooks::class,
        'checkouts' => Checkouts::class,
        'affiliates' => Affiliates::class,
        'usage-records' => UsageRecords::class,
    ];

    /**
     * Deserialize webhook payload data to a model entity
     *
     * @param array $data The webhook data segment (with 'type', 'id', 'attributes')
     * @return mixed The deserialized model entity, or raw data if type is unknown
     */
    public function deserialize(array $data): mixed
    {
        if (!isset($data['type'])) {
            // No type field, return raw data
            return $data;
        }

        $type = $data['type'];
        $entityClass = $this->typeMap[$type] ?? null;

        if ($entityClass === null) {
            // Unknown type, return raw data
            return $data;
        }

        // Instantiate the entity with the data
        return new $entityClass($data);
    }

    /**
     * Register a custom type mapping
     *
     * Useful for extending the deserializer with custom entity types.
     *
     * @param string $type The JSON:API type
     * @param string $entityClass The entity class to instantiate
     * @return self
     */
    public function registerType(string $type, string $entityClass): self
    {
        $this->typeMap[$type] = $entityClass;
        return $this;
    }

    /**
     * Get the type mapping
     *
     * @return array<string, string>
     */
    public function getTypeMap(): array
    {
        return $this->typeMap;
    }

    /**
     * Check if a type has a registered entity class
     *
     * @param string $type The JSON:API type
     * @return bool
     */
    public function hasType(string $type): bool
    {
        return isset($this->typeMap[$type]);
    }
}
