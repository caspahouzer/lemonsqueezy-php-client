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
     * Activates a license key and creates a new instance for the specified installation.
     * This operation registers the license key on a device and returns a unique instance ID
     * that can be used for validation and deactivation.
     *
     * @param string $licenseKey   The license key string to activate (usually in format XXXX-XXXX-XXXX-XXXX)
     * @param string $instanceName A human-readable label for the new instance (e.g., device name, domain, installation identifier)
     * @return array<string, mixed> The activation response containing:
     *     - instance_id: string The unique identifier for this license activation instance
     *     - license_key: string The activated license key
     *     - activated_at: string|null The activation timestamp in ISO 8601 format
     *     - status: string The instance status (e.g., 'active', 'deactivated')
     *     - metadata: array Additional metadata about the activation
     * @throws ClientException If the API request fails (e.g., invalid license key, license not found)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/licenses/activate-license
     *
     * @since 1.0.0
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
     * Validates a license key and checks its current status and activation state.
     * Optionally validates a specific instance activation. Use this to verify
     * license validity before allowing feature access.
     *
     * @param string       $licenseKey The license key string to validate
     * @param string|null  $instanceId Optional. The instance ID to validate a specific activation.
     *                                  If omitted, validates the license key globally.
     * @return array<string, mixed> The validation response containing:
     *     - valid: bool Whether the license key is valid and active
     *     - license_key: string The validated license key
     *     - instance_id: string|null The instance ID if validating specific instance
     *     - status: string The license status (e.g., 'active', 'expired', 'suspended')
     *     - expires_at: string|null Expiration date in ISO 8601 format or null if perpetual
     *     - activated_instances: int Number of active instances for this license
     *     - max_instances: int|null Maximum allowed instances or null for unlimited
     * @throws ClientException If the API request fails (e.g., invalid license key)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/licenses/validate-license
     *
     * @since 1.0.0
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
     * Deactivates a specific instance of a license key, removing it from active use.
     * This allows the license key to be reactivated on a different device or installation.
     * After deactivation, the license will no longer validate for this instance.
     *
     * @param string $licenseKey The license key string to deactivate
     * @param string $instanceId The unique identifier of the specific instance to deactivate
     * @return array<string, mixed> The deactivation response containing:
     *     - instance_id: string The deactivated instance ID
     *     - license_key: string The license key that was deactivated
     *     - deactivated_at: string The deactivation timestamp in ISO 8601 format
     *     - status: string The updated instance status (e.g., 'deactivated')
     *     - remaining_activations: int|null Number of remaining available activations
     * @throws ClientException If the API request fails (e.g., invalid license key or instance ID, instance not found)
     * @throws HttpException If a network or HTTP protocol error occurs
     *
     * @see https://docs.lemonsqueezy.com/api/licenses/deactivate-license
     *
     * @since 1.0.0
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
