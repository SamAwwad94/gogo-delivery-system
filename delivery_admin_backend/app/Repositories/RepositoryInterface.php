<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface RepositoryInterface
 * @package App\Repositories
 */
interface RepositoryInterface
{
    /**
     * Get all records
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get all records with pagination
     *
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Find model by id
     *
     * @param int $modelId
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model|null
     */
    public function findById(
        int $modelId,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model;

    /**
     * Find model by custom field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model|null
     */
    public function findByField(
        string $field,
        $value,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model;

    /**
     * Create a new model
     *
     * @param array $payload
     * @return Model|null
     */
    public function create(array $payload): ?Model;

    /**
     * Update existing model
     *
     * @param int $modelId
     * @param array $payload
     * @return Model|null
     */
    public function update(int $modelId, array $payload): ?Model;

    /**
     * Delete model by id
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool;

    /**
     * Permanently delete model by id
     *
     * @param int $modelId
     * @return bool
     */
    public function forceDeleteById(int $modelId): bool;

    /**
     * Restore soft deleted model
     *
     * @param int $modelId
     * @return bool
     */
    public function restoreById(int $modelId): bool;
}
