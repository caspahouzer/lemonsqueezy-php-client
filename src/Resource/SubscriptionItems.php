<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\SubscriptionItems as SubscriptionItemsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class SubscriptionItems extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'subscription-items';
    }

    public function getModelClass(): string
    {
        return SubscriptionItemsEntity::class;
    }

    /**
     * Create operation not supported for Subscription Items resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('subscription-items', 'create');
    }

    /**
     * Delete operation not supported for Subscription Items resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('subscription-items', 'delete');
    }

    /**
     * Get current usage data for a subscription item
     *
     * Retrieves the current usage metrics for a metered subscription item. This endpoint
     * returns the accumulated usage for the current billing period, which is essential
     * for usage-based billing models.
     *
     * @param string $subscriptionItemId The unique identifier of the subscription item to fetch usage data for
     * @return array<string, mixed> The current usage data containing:
     *     - subscription_item_id: string The subscription item ID
     *     - quantity: float|int The accumulated usage quantity for the current period
     *     - unit_name: string The unit of measurement (e.g., 'requests', 'gigabytes', 'seats')
     *     - billing_period_start: string Start date of the current billing period in ISO 8601 format
     *     - billing_period_end: string End date of the current billing period in ISO 8601 format
     *     - reset_date: string|null When the usage counter will reset, or null if manual reset
     * @throws ClientException If the API request fails (e.g., subscription item not found, not a metered item)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/subscription-items/retrieve-subscription-item-current-usage
     *
     * @since 1.0.0
     */
    public function getCurrentUsage(string $subscriptionItemId): array
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($subscriptionItemId) . '/current-usage';
        $response = $this->client->request('GET', $endpoint);

        return $response['data'] ?? $response;
    }
}
