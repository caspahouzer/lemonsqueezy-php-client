<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Subscriptions as SubscriptionsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Subscriptions extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'subscriptions';
    }

    public function getModelClass(): string
    {
        return SubscriptionsEntity::class;
    }

    /**
     * Create operation not supported for Subscriptions resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('subscriptions', 'create');
    }

    /**
     * Delete operation not supported for Subscriptions resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('subscriptions', 'delete');
    }

    /**
     * Cancel a subscription
     *
     * @param string $subscriptionId The subscription ID
     * @param array $data Cancellation data (reason, etc.)
     * @return AbstractModel The updated subscription
     *
     * @see https://docs.lemonsqueezy.com/api/subscriptions/cancel-subscription
     */
    public function cancelSubscription(string $subscriptionId, array $data = []): AbstractModel
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($subscriptionId) . '/cancel';
        $response = $this->client->request('POST', $endpoint, !empty($data) ? ['data' => $data] : []);

        return $this->hydrateModel($response['data'] ?? $response);
    }
}
