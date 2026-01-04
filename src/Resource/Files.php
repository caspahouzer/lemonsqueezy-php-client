<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Files as FilesEntity;

class Files extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'files';
    }

    public function getModelClass(): string
    {
        return FilesEntity::class;
    }
}