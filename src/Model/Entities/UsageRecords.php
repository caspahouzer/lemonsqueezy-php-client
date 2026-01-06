<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class UsageRecords extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('usage-records')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'usage-records';
    }
}
