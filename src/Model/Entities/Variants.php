<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Variants extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('variants')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'variants';
    }
}
