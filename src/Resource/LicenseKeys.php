<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\LicenseKeys as LicenseKeysEntity;

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
     * Activate a license key (Public License API)
     *
     * Activates a license key and creates a new instance.
     *
     * @param string $licenseKey The license key to activate
     * @param string $instanceName A label for the new instance
     * @return mixed The activation response with instance_id
     */
    public function activate(string $licenseKey, string $instanceName): mixed
    {
        $payload = [
            'license_key' => $licenseKey,
            'instance_name' => $instanceName,
        ];

        return $this->client->request('POST', 'licenses/activate', $payload);
    }

    /**
     * Validate a license key (Public License API)
     *
     * Validates a license key and checks its status.
     *
     * @param string $licenseKey The license key to validate
     * @param ?string $instanceId Optional instance ID to validate specific instance
     * @return mixed The validation response
     */
    public function validate(string $licenseKey, ?string $instanceId = null): mixed
    {
        $payload = ['license_key' => $licenseKey];

        if ($instanceId) {
            $payload['instance_id'] = $instanceId;
        }

        return $this->client->request('POST', 'licenses/validate', $payload);
    }

    /**
     * Deactivate a license key (Public License API)
     *
     * Deactivates an instance of a license key.
     *
     * @param string $licenseKey The license key to deactivate
     * @param string $instanceId The instance ID to deactivate
     * @return mixed The deactivation response
     */
    public function deactivate(string $licenseKey, string $instanceId): mixed
    {
        $payload = [
            'license_key' => $licenseKey,
            'instance_id' => $instanceId,
        ];

        return $this->client->request('POST', 'licenses/deactivate', $payload);
    }
}
