<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Webhooks extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('webhooks')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'webhooks';
    }
}
