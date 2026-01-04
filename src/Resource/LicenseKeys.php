<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\LicenseKeys as LicenseKeysEntity;
use LemonSqueezy\Query\QueryBuilder;

class LicenseKeys extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'license-keys';
    }

    public function getModelClass(): string
    {
        return LicenseKeysEntity::class;
    }

    /**
     * Activate a license key
     *
     * @param string $licenseKeyId The license key ID
     * @param ?QueryBuilder $query Optional query parameters
     * @return mixed The activated license key
     */
    public function activate(string $licenseKeyId, ?QueryBuilder $query = null): mixed
    {
        return $this->request(
            'POST',
            "{$this->getEndpoint()}/{$licenseKeyId}/actions/activate",
            null,
            $query
        );
    }

    /**
     * Validate a license key
     *
     * @param string $licenseKeyId The license key ID
     * @param ?QueryBuilder $query Optional query parameters
     * @return mixed The validation result
     */
    public function validate(string $licenseKeyId, ?QueryBuilder $query = null): mixed
    {
        return $this->request(
            'POST',
            "{$this->getEndpoint()}/{$licenseKeyId}/actions/validate",
            null,
            $query
        );
    }

    /**
     * Deactivate a license key
     *
     * @param string $licenseKeyId The license key ID
     * @param ?QueryBuilder $query Optional query parameters
     * @return mixed The deactivated license key
     */
    public function deactivate(string $licenseKeyId, ?QueryBuilder $query = null): mixed
    {
        return $this->request(
            'POST',
            "{$this->getEndpoint()}/{$licenseKeyId}/actions/deactivate",
            null,
            $query
        );
    }
}