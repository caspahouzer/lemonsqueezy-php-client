<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Customer extends AbstractModel
{
    public function getType(): string
    {
        return 'customers';
    }

    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }
}
