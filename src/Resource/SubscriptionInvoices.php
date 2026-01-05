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
}