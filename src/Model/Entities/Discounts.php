<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Discounts extends AbstractModel
{
    public function getType(): string
    {
        return 'discounts';
    }
}
