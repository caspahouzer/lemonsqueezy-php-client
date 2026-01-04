<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Prices extends AbstractModel
{
    public function getType(): string
    {
        return 'prices';
    }
}