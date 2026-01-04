<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Affiliates extends AbstractModel
{
    public function getType(): string
    {
        return 'affiliates';
    }
}