<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class Files extends AbstractModel
{
    public function getType(): string
    {
        return 'files';
    }
}
