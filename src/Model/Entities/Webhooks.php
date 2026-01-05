<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Webhooks extends AbstractModel
{
    public function getType(): string
    {
        return 'webhooks';
    }
}
