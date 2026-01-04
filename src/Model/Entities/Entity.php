<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

/**
 * Generic entity class for any API resource
 */
class Entity extends AbstractModel
{
    private string $type = 'entity';

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
