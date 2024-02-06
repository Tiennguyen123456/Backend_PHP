<?php
namespace App\Services;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Exception;
use ZipArchive;

class BaseService
{
    public $searches = [];
    public $filters = [];
    public $attributes;
    public $repo;

    public function init()
    {
        $model = $this->repo->getModel();
        return new $model;
    }

    public function getFillable()
    {
        return $this->repo->getFillable();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function getItem($id)
    {
        return $this->repo->getItem($id);
    }

    public function getList()
    {
        return $this->repo->getList(
            $this->attributes['status'] ?? null,
            $this->attributes['orderBy'] ?? 'updated_at',
            $this->attributes['orderDesc'] ?? true,
            $this->attributes['limit'] ?? null,
            $this->attributes['pageSize'] ?? 50
        );
    }

    public function storeAs($attrForms, $attrMores = [])
    {
        $attributes = [];
        $attrPermits = $this->getFillable();

        foreach ($attrPermits as $attrKey) {
            if (isset($attrForms[$attrKey])) {
                $attributes[$attrKey] = $attrForms[$attrKey];
            }
        }

        if (isset($attrMores) && count($attrMores)) {
            $attributes = array_merge($attributes, $attrMores);
        }

        if ($model = $this->repo->upsert($attributes, isset($attributes['id']) ? $attributes['id'] : null)) {
            return $model;
        }

        return null;
    }

    public function store()
    {
        $id = isset($this->attributes['id']) ? (int)($this->attributes['id']) : null;

        if (empty($id)) {
            $model = $this->repo->create($this->attributes);

            if (!empty($model)) {
                return $model;
            }
        } else {
            $model = $this->repo->find($id);

            if (!empty($model)) {
                $model->update($this->attributes);
                return $model;
            }
        }

        return false;
    }

    public function remove($id)
    {
        $this->attributes = [
            'status' => $this->repo->getModel()::STATUS_DELETED
        ];

        if ($this->repo->update($id, $this->attributes)){
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        if ($this->repo->delete($id)){
            return true;
        }

        return false;
    }

    /**
     * Search LIKE in columns
     */
    public function getSearch()
    {
        $table = $this->repo->getModelTable();

        if (isset($this->attributes['search']) && count($this->attributes['search'])) {
            foreach ($this->attributes['search'] as $key => $value) {
                if (Helper::tableHasColumn($table, $key) && !empty($value)) {
                    $this->searches[$key] = $value;
                }
            }
        }

        return $this->searches;
    }

    /**
     * Filter WHERE in columns
     */
    public function getFilters($filterMores = [])
    {
        $table = $this->repo->getModelTable();

        if (isset($this->attributes['filters']) && count($this->attributes['filters'])) {
            foreach ($this->attributes['filters'] as $key => $value) {
                if (Helper::tableHasColumn($table, $key) && !empty($value)) {
                    $this->filters[$key] = $value;
                }
            }

            if (count($filterMores)) {
                foreach ($filterMores as $key) {
                    if (!empty($value = $this->attributes['filters'][$key] ?? null)) {
                        $this->filters[$key] = $value;
                    }
                }
            }
        }

        return $this->filters;
    }
}
