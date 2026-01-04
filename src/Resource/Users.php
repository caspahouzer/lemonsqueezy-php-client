<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\User;
use LemonSqueezy\Model\AbstractModel;

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

    /**
     * Get the current authenticated user
     *
     * @return AbstractModel The current user
     */
    public function me(): AbstractModel
    {
        $response = $this->client->request('GET', 'users/me');

        return $this->hydrateModel($response['data'] ?? $response);
    }
}
