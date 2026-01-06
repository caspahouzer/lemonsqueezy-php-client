<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

/**
 * Generic entity class for any API resource
 */
class Entity extends AbstractModel
{
    private string $type = 'entity';

    /**
     * Create a new generic entity instance
     *
     * @param array  $data The entity data from API response
     * @param string $type The entity type identifier (default: 'entity')
     * @since 1.0.0
     */
    public function __construct(array $data = [], string $type = 'entity')
    {
        parent::__construct($data);
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
