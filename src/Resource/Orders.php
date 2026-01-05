<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Orders as OrdersEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Orders extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'orders';
    }

    public function getModelClass(): string
    {
        return OrdersEntity::class;
    }

    /**
     * Create operation not supported for Orders resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('orders', 'create');
    }

    /**
     * Update operation not supported for Orders resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('orders', 'update');
    }

    /**
     * Delete operation not supported for Orders resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('orders', 'delete');
    }

    /**
     * Generate an invoice for an order
     *
     * @param string $orderId The order ID
     * @param array $data Additional data for invoice generation
     * @return array The generated invoice data
     *
     * @see https://docs.lemonsqueezy.com/api/orders/generate-order-invoice
     */
    public function generateInvoice(string $orderId, array $data = []): array
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($orderId) . '/invoices';
        $response = $this->client->request('POST', $endpoint, !empty($data) ? ['data' => $data] : []);

        return $response['data'] ?? $response;
    }

    /**
     * Issue a refund for an order
     *
     * @param string $orderId The order ID
     * @param array $data Refund data (reason, refund_reason, etc.)
     * @return array The refund data
     *
     * @see https://docs.lemonsqueezy.com/api/orders/issue-refund
     */
    public function issueRefund(string $orderId, array $data = []): array
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($orderId) . '/refunds';
        $response = $this->client->request('POST', $endpoint, !empty($data) ? ['data' => $data] : []);

        return $response['data'] ?? $response;
    }
}