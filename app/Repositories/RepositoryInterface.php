<?php
namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all
     * @return mixed
     */
    public function getAll($searches = [], $filters = [], $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0);

    public function setModel();

    public function getModel();

    public function getFillable();

    public function getInstanceModel();

    public function getModelTable();

    public function getItem($id, $status = null);

    public function getItems($status = null, $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0, $paginate = 0);

    public function getList($searches = [], $filters = [], $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0, $paginate = 50, $page = 1);

    public function addSearchQuery($query, $searches = []);

    public function addFilterQuery($query, $filters = []);

    /**
     * Get one
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Create
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = []);

    /**
     * Update
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, $attributes = []);

    /**
     * Upsert: Create and Update
     * @param array $attributes
     * @param $id
     * @return mixed
     */
    public function upsert($attributes = [], $id = null);

    /**
     * Delete
     * @param $id
     * @return mixed
     */
    public function delete($id);

    public function user();

    /**
     * Count
     * @return int
     */
    public function count($searches = [], $filters = []): int;
}
