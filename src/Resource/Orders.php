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
     * Generates a new invoice PDF for the specified order. This operation creates
     * a formal invoice document that can be downloaded and sent to customers.
     *
     * @param string $orderId The unique identifier of the order to generate an invoice for
     * @param array  $data    Optional invoice customization data with structure:
     *     - invoice_number: string Optional. Custom invoice number to use
     *     - invoice_date: string Optional. Invoice date in ISO 8601 format
     * @return array<string, mixed> The generated invoice data containing:
     *     - id: string The unique invoice identifier
     *     - url: string The publicly accessible invoice PDF URL
     *     - created_at: string Invoice creation timestamp in ISO 8601 format
     *     - status: string The invoice status (e.g., 'completed', 'pending')
     * @throws ClientException If the API request fails (e.g., invalid order ID, rate limit exceeded)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/orders/generate-order-invoice
     *
     * @since 1.0.0
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
     * Creates a refund transaction for the specified order. The refund amount can be
     * partial or full, and may include a reason for customer records.
     *
     * @param string $orderId The unique identifier of the order to refund
     * @param array  $data    Optional refund configuration data with structure:
     *     - refund_reason: string Optional. Reason for the refund (e.g., 'customer_request', 'duplicate', 'fraud')
     *     - refund_reason_comment: string Optional. Additional comments about the refund
     * @return array<string, mixed> The refund transaction data containing:
     *     - id: string The unique refund identifier
     *     - order_id: string The associated order ID
     *     - amount: float The refunded amount in the order's currency
     *     - reason: string The refund reason code
     *     - created_at: string Refund creation timestamp in ISO 8601 format
     * @throws ClientException If the API request fails (e.g., invalid order ID, refund not allowed)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/orders/issue-refund
     *
     * @since 1.0.0
     */
    public function issueRefund(string $orderId, array $data = []): array
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($orderId) . '/refunds';
        $response = $this->client->request('POST', $endpoint, !empty($data) ? ['data' => $data] : []);

        return $response['data'] ?? $response;
    }
}
