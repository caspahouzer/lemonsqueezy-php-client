<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Variants as VariantsEntity;

class Variants extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'variants';
    }

    public function getModelClass(): string
    {
        return VariantsEntity::class;
    }
}