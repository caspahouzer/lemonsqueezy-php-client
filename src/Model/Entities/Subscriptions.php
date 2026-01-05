<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Subscriptions extends AbstractModel
{
    public function getType(): string
    {
        return 'subscriptions';
    }
}
