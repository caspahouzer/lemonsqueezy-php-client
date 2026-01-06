<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class SubscriptionInvoices extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('subscription-invoices')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'subscription-invoices';
    }
}
