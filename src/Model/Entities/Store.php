<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Store extends AbstractModel
{
    public function getType(): string
    {
        return 'stores';
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }
}
