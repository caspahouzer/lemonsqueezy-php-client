<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Product extends AbstractModel
{
    public function getType(): string
    {
        return 'products';
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }
}
