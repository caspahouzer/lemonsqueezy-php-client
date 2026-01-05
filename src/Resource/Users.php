<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\User;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

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

    /**
     * Create operation not supported for Users resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('users', 'create');
    }

    /**
     * Update operation not supported for Users resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('users', 'update');
    }

    /**
     * Delete operation not supported for Users resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('users', 'delete');
    }
}
