<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\LicenseKeys as LicenseKeysEntity;

class LicenseKeys extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'license-keys';
    }

    public function getModelClass(): string
    {
        return LicenseKeysEntity::class;
    }
}