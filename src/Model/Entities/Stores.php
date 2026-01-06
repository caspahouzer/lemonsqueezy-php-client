<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Stores extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('stores')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'stores';
    }
}
