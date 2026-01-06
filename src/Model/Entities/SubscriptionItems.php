<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class SubscriptionItems extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('subscription-items')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'subscription-items';
    }
}
