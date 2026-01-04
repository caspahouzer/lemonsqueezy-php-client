<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\SubscriptionInvoices as SubscriptionInvoicesEntity;

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
}