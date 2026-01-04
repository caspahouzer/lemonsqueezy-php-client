<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Variants extends AbstractModel
{
    public function getType(): string
    {
        return 'variants';
    }
}