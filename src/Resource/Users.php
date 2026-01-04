<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\User;

class Users extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'users';
    }

    public function getModelClass(): string
    {
        return User::class;
    }
}
