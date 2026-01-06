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
     * Initiates cancellation of an active subscription. The subscription will be canceled
     * at the end of the current billing period unless immediately cancelled. Customers will
     * be notified of the cancellation.
     *
     * @param string $subscriptionId The unique identifier of the subscription to cancel
     * @param array  $data          Optional cancellation configuration data with structure:
     *     - reason: string Optional. Reason for cancellation (e.g., 'refund_requested', 'customer_request', 'product_unsatisfactory')
     *     - comment: string Optional. Internal notes about the cancellation
     *     - cancel_immediately: bool Optional. Whether to cancel immediately (default: false, cancels at period end)
     * @return AbstractModel The updated subscription model with status set to 'cancelled' or 'cancelling'
     *     containing the subscription data including:
     *     - id: string The subscription ID
     *     - status: string Updated subscription status
     *     - cancelled_at: string|null Cancellation timestamp if cancelled
     *     - ends_at: string When the subscription will end
     * @throws ClientException If the API request fails (e.g., subscription not found, already cancelled)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/subscriptions/cancel-subscription
     *
     * @since 1.0.0
     */
    public function cancelSubscription(string $subscriptionId, array $data = []): AbstractModel
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($subscriptionId) . '/cancel';
        $response = $this->client->request('POST', $endpoint, !empty($data) ? ['data' => $data] : []);

        return $this->hydrateModel($response['data'] ?? $response);
    }
}
