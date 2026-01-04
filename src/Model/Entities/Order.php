<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Order extends AbstractModel
{
    public function getType(): string
    {
        return 'orders';
    }
}
