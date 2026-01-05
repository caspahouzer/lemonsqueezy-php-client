<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class SubscriptionInvoices extends AbstractModel
{
    public function getType(): string
    {
        return 'subscription-invoices';
    }
}
