<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Stores as StoresEntity;

class Stores extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'stores';
    }

    public function getModelClass(): string
    {
        return StoresEntity::class;
    }
}