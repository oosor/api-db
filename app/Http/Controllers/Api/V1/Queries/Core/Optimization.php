<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 21:51
 */

namespace App\Http\Controllers\Api\V1\Queries\Core;


use App\Http\Controllers\Api\V1\Queries\Contracts\{
    QueryBuilder as Builder
};
use App\Http\Controllers\Api\V1\Queries\Core\Optimization\OptimizeWith;

class Optimization
{
    use OptimizeWith;

    /**
     * @var Builder $builder
     * */
    private $builder;

    /**
     * @param Builder $queryBuilder
     * @return void
     * */
    public function __construct(Builder $queryBuilder)
    {
        $this->builder = $queryBuilder;
    }

    /** get finished prepare resource data
     * @return mixed
     * */
    public function getData()
    {
        $queryData = $this->builder->getQueryData();

        switch (strtoupper($queryData['query'])) {
            case 'SELECT':
                return $this->resource('select', $this->optimizeRelationship($this->builder->selectRows()));
            case 'INSERT':
                return $this->resource('insert', ['inserting' => $this->builder->insertRows()]);
            case 'UPDATE':
                return $this->resource('update', ['updated_rows' => $this->builder->updateRows()]);
            case 'DELETE':
                return $this->resource('delete', ['deleted_rows' => $this->builder->deleteRows()]);
        }

        return null;
    }

    /** prepare data for resource
     * @param string $type
     * @param mixed $data
     * @return array
     * */
    protected function resource($type, $data)
    {
        return [
            'status' => !is_null($data),
            'operation' => $type,
            'data' => $data,
        ];
    }

}
