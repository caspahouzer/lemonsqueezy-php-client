<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Affiliates extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('affiliates')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'affiliates';
    }
}
