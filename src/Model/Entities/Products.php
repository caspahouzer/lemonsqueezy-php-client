<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Products extends AbstractModel
{
    public function getType(): string
    {
        return 'products';
    }
}