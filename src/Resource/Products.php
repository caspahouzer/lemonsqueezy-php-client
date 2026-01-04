<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Products as ProductsEntity;

class Products extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'products';
    }

    public function getModelClass(): string
    {
        return ProductsEntity::class;
    }
}