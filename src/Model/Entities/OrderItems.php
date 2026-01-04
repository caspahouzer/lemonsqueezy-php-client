<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class OrderItems extends AbstractModel
{
    public function getType(): string
    {
        return 'order-items';
    }
}