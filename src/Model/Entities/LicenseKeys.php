<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class LicenseKeys extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('license-keys')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'license-keys';
    }
}
