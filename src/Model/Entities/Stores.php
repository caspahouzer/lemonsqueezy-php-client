<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Stores extends AbstractModel
{
    public function getType(): string
    {
        return 'stores';
    }
}
