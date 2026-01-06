<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Files extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('files')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'files';
    }
}
