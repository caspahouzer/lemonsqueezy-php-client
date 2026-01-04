<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Checkouts as CheckoutsEntity;

class Checkouts extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'checkouts';
    }

    public function getModelClass(): string
    {
        return CheckoutsEntity::class;
    }
}