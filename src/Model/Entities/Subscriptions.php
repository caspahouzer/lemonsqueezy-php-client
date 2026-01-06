<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Subscriptions extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('subscriptions')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'subscriptions';
    }
}
