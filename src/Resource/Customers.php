<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Customer;

class Customers extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'customers';
    }

    public function getModelClass(): string
    {
        return Customer::class;
    }
}
