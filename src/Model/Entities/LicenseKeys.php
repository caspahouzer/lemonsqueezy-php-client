<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class LicenseKeys extends AbstractModel
{
    public function getType(): string
    {
        return 'license-keys';
    }
}