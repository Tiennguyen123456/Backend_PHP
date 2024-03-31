<?php
namespace App\Repositories;

use App\Helpers\Helper;
use App\Repositories\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    public function getAll($searches = [], $filters = [], $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0)
    {
        $query = $this->model;

        $query = $this->addFilterCompanyQuery($query);

        if (Helper::tableHasColumn($this->getModelTable(), 'status')) {
            $query = $query->where('status', '!=', $query::STATUS_DELETED);
        }

        if (count($searches)) {
            $query = $this->addSearchQuery($query, $searches);
        }

        if (count($filters)) {
            $query = $this->addFilterQuery($query, $filters);
        }

        if ($orderByDesc) {
            $query = $query->orderBy($orderByColumn, 'desc');
        } else {
            $query = $query->orderBy($orderByColumn, 'asc');
        }
        if ($limit) {
            $query = $query->limit($limit);
        }

        return $query->get();
    }

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    abstract public function getModel();

    public function getFillable()
    {
        return $this->model->getFillable();
    }

    public function getInstanceModel()
    {
        return $this->model;
    }

    public function getModelTable()
    {
        return $this->getInstanceModel()->getTable();
    }

    public function getItem($id, $status = null)
    {
        $query = $this->model->where([
            ['id', '=', $id],
            ['status', '!=', $this->model::STATUS_DELETED],
        ]);

        $query = $this->addFilterCompanyQuery($query);

        if (!empty($status)) {
            if (is_array($status)) {
                $query = $query->whereIn('status', $status);
            } else {
                $query = $query->where(['status' => $status]);
            }
        }

        $item = $query->first();
        return $item;
    }

    public function getItems($status = null, $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0, $paginate = 50)
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED);

        if (!empty($status)) {
            if (is_array($status)) {
                $query = $query->whereIn('status', $status);
            } else {
                $query = $query->where(['status' => $status]);
            }
        }

        if ($orderByDesc) {
            $query = $query->orderBy($orderByColumn, 'desc');
        } else {
            $query = $query->orderBy($orderByColumn, 'asc');
        }

        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        if ($paginate > 0) {
            return $query->paginate($paginate);
        }

        return $query->get();
    }

    public function getList(
        $searches = [],
        $filters = [],
        $orderByColumn = 'updated_at',
        $orderByDesc = true,
        $limit = 0,
        $paginate = 50,
        $page = 1
    ) {
        $query = $this->model;

        $query = $this->addFilterCompanyQuery($query);

        if (Helper::tableHasColumn($this->getModelTable(), 'status')) {
            $query = $query->where('status', '!=', $this->model::STATUS_DELETED);
        }

        if (count($searches)) {
            $query = $this->addSearchQuery($query, $searches);
        }

        if (count($filters)) {
            $query = $this->addFilterQuery($query, $filters);
        }

        if ($orderByDesc) {
            $query = $query->orderBy($orderByColumn, 'desc');
        } else {
            $query = $query->orderBy($orderByColumn, 'asc');
        }

        if ($limit) {
            $query = $query->limit($limit);
        }

        if ($paginate) {
            return $query->paginate($paginate, ['*'], 'page', $page);
        }

        return $query->get();
    }

    public function addSearchQuery($query, $searches = [])
    {
        if (count($searches)) {
            foreach ($searches as $column => $value) {
                $query = $query->where($column, 'LIKE', "%{$value}%");
            }
        }

        return $query;
    }

    public function addFilterQuery($query, $filters = [])
    {
        if (count($filters)) {
            foreach ($filters as $column => $value) {
                if (is_array($value)) {
                    $query = $query->whereIn($column, $value);
                } else {
                    $query = $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    public function addFilterCompanyQuery($query)
    {
        $user = $this->user();

        if ($user && !$user->is_admin) {
            if (Helper::tableHasColumn($this->getModelTable(), 'company_id')) {
                $query = $query->where('company_id', $user->company_id);
            }
        }

        return $query;
    }

    public function find($id, $status = null)
    {
        if (Helper::tableHasColumn($this->getModelTable(), 'status')) {
            return $this->getItem($id, $status);
        } else {
            $query = $this->model;
            $query = $this->addFilterCompanyQuery($query);

            return $query->where([
                'id' => $id
            ])->first();
        }
    }

    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($id, $attributes = [])
    {
        $result = $this->find($id);

        if (!empty($result)) {
            $result->update($attributes);
            return $result;
        }

        return null;
    }

    public function upsert($attributes = [], $id = null)
    {
        $result = null;

        if (!empty($id)) {
            $result = $this->update($id, $attributes);
        } else {
            $result = $this->create($attributes);
        }

        return $result;
    }

    public function delete($id)
    {
        $result = $this->find($id);

        if (!empty($result)) {
            $result->delete();
            return true;
        }

        return false;
    }

    public function user()
    {
        $user = auth()->user();
        return $user;
    }

    public function userApi()
    {
        $user = auth('api')->user();
        return $user;
    }

    public function count($searches = [], $filters = []): int
    {
        $query = $this->model;

        if (Helper::tableHasColumn($this->getModelTable(), 'status')) {
            $query = $query->where('status', '!=', $query::STATUS_DELETED);
        }

        if (count($searches)) {
            $query = $this->addSearchQuery($query, $searches);
        }

        if (count($filters)) {
            $query = $this->addFilterQuery($query, $filters);
        }

        return $query->count();
    }
}
