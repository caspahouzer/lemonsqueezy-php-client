<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class UsageRecords extends AbstractModel
{
    public function getType(): string
    {
        return 'usage-records';
    }
}
