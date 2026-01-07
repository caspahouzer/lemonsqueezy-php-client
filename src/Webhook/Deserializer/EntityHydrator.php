<?php

namespace LemonSqueezy\Webhook\Deserializer;

use LemonSqueezy\Model\AbstractModel;

/**
 * Hydrates raw data into model entities
 *
 * Provides utilities for converting deserialized data into
 * properly initialized Model instances with all their methods.
 */
class EntityHydrator
{
    /**
     * Hydrate raw data into a model instance
     *
     * @param array $data The raw entity data
     * @param string $modelClass The model class to instantiate
     * @return AbstractModel The hydrated model instance
     */
    public function hydrate(array $data, string $modelClass): AbstractModel
    {
        return new $modelClass($data);
    }

    /**
     * Hydrate multiple data items into model instances
     *
     * @param array<array> $items Array of raw data items
     * @param string $modelClass The model class to instantiate
     * @return array<AbstractModel>
     */
    public function hydrateMultiple(array $items, string $modelClass): array
    {
        return array_map(
            fn (array $data) => $this->hydrate($data, $modelClass),
            $items
        );
    }

    /**
     * Extract attributes from a model
     *
     * @param AbstractModel $model The model to extract from
     * @return array
     */
    public function extractAttributes(AbstractModel $model): array
    {
        return $model->getAttributes();
    }

    /**
     * Check if data is already a model instance
     *
     * @param mixed $data The data to check
     * @return bool
     */
    public function isModel(mixed $data): bool
    {
        return $data instanceof AbstractModel;
    }

    /**
     * Ensure data is a model (hydrate if needed)
     *
     * @param array|AbstractModel $data The data to ensure is a model
     * @param ?string $modelClass Optional model class if hydration needed
     * @return AbstractModel
     */
    public function ensureModel(array|AbstractModel $data, ?string $modelClass = null): AbstractModel
    {
        if ($this->isModel($data)) {
            return $data;
        }

        if ($modelClass === null) {
            throw new \InvalidArgumentException(
                'Model class is required when hydrating from array data'
            );
        }

        return $this->hydrate($data, $modelClass);
    }
}
