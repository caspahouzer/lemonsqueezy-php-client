<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Orders extends AbstractModel
{
    public function getType(): string
    {
        return 'orders';
    }
}
