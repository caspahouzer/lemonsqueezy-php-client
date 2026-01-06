<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\SubscriptionInvoices as SubscriptionInvoicesEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class SubscriptionInvoices extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'subscription-invoices';
    }

    public function getModelClass(): string
    {
        return SubscriptionInvoicesEntity::class;
    }

    /**
     * Create operation not supported for SubscriptionInvoices resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('subscription-invoices', 'create');
    }

    /**
     * Update operation not supported for SubscriptionInvoices resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('subscription-invoices', 'update');
    }

    /**
     * Delete operation not supported for SubscriptionInvoices resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('subscription-invoices', 'delete');
    }

    /**
     * Generate an invoice for a subscription invoice
     *
     * Generates a formal invoice PDF from an existing subscription invoice. This operation
     * creates a downloadable document representing the subscription billing period.
     *
     * @param string $subscriptionInvoiceId The unique identifier of the subscription invoice to generate
     * @param array  $data                  Optional invoice generation parameters with structure:
     *     - custom_number: string Optional. Custom invoice number override
     *     - custom_date: string Optional. Custom invoice date in ISO 8601 format
     * @return array<string, mixed> The generated invoice data containing:
     *     - id: string The unique invoice identifier
     *     - url: string The publicly accessible invoice PDF URL
     *     - pdf_url: string Direct URL to the invoice PDF file
     *     - created_at: string Invoice generation timestamp in ISO 8601 format
     *     - status: string The invoice generation status
     * @throws ClientException If the API request fails (e.g., invalid subscription invoice ID)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/subscription-invoices/generate-subscription-invoice
     *
     * @since 1.0.0
     */
    public function generateInvoice(string $subscriptionInvoiceId, array $data = []): array
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($subscriptionInvoiceId) . '/generate';
        $response = $this->client->request('POST', $endpoint, !empty($data) ? ['data' => $data] : []);

        return $response['data'] ?? $response;
    }
}
