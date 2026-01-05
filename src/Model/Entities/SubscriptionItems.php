<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class SubscriptionItems extends AbstractModel
{
    public function getType(): string
    {
        return 'subscription-items';
    }
}
