<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base Repository class for common database operations
 */
class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * Get all records with pagination
     *
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }

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
    ): ?Model {
        return $this->model->select($columns)->with($relations)->findOrFail($modelId)->append($appends);
    }

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
    ): ?Model {
        return $this->model->select($columns)->with($relations)->where($field, '=', $value)->first()->append($appends);
    }

    /**
     * Create a new model
     *
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): ?Model
    {
        $model = $this->model->create($payload);
        return $model->fresh();
    }

    /**
     * Update existing model
     *
     * @param int $modelId
     * @param array $payload
     * @return Model
     */
    public function update(int $modelId, array $payload): ?Model
    {
        $model = $this->findById($modelId);
        $model->update($payload);
        return $model->fresh();
    }

    /**
     * Delete model by id
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool
    {
        return $this->findById($modelId)->delete();
    }

    /**
     * Permanently delete model by id
     *
     * @param int $modelId
     * @return bool
     */
    public function forceDeleteById(int $modelId): bool
    {
        return $this->findById($modelId)->forceDelete();
    }

    /**
     * Restore soft deleted model
     *
     * @param int $modelId
     * @return bool
     */
    public function restoreById(int $modelId): bool
    {
        return $this->model->withTrashed()->findOrFail($modelId)->restore();
    }
}
