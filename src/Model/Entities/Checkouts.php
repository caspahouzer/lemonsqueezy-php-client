<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Checkouts extends AbstractModel
{
    public function getType(): string
    {
        return 'checkouts';
    }
}
