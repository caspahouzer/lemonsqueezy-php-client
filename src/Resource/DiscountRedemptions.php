<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\DiscountRedemptions as DiscountRedemptionsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class DiscountRedemptions extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'discount-redemptions';
    }

    public function getModelClass(): string
    {
        return DiscountRedemptionsEntity::class;
    }

    /**
     * Create operation not supported for DiscountRedemptions resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('discount-redemptions', 'create');
    }

    /**
     * Update operation not supported for DiscountRedemptions resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('discount-redemptions', 'update');
    }

    /**
     * Delete operation not supported for DiscountRedemptions resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('discount-redemptions', 'delete');
    }
}