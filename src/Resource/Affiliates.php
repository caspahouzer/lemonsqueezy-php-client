<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Affiliates as AffiliatesEntity;

class Affiliates extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'affiliates';
    }

    public function getModelClass(): string
    {
        return AffiliatesEntity::class;
    }
}