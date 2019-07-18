<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 11.07.19
 * Time: 15:16
 */

namespace App\Http\Controllers\Api\V1\Queries\Core;


use App\Http\Controllers\Api\V1\Queries\Core\Builders\{
    BuilderWhere, BuilderWith
};
use Illuminate\Database\Query\Builder;

trait BuilderHelper
{
    public function buildColumns(Builder $query)
    {
        $query->select(
            array_map(function ($item) {
                return $this->validatedData['table'] . '.' . $item;
            }, $this->validatedData['columns'])
        );
        // id is required
        if (!in_array('*', $this->validatedData['columns']) && !in_array('id', $this->validatedData['columns'])) {
            $query->addSelect($this->validatedData['table'] . '.id');
        }
    }

    public function buildWhere(Builder $query)
    {
        (new BuilderWhere($query, $this->validatedData['table']))->init($this->validatedData['where']);
    }

    public function buildWith(Builder $query)
    {
        (new BuilderWith($query, $this->validatedData['table']))->init($this->validatedData['with']);
    }

    public function buildOrder(Builder $query)
    {
        if (count($this->validatedData['order']) < 2) {
            [$order] = $this->validatedData['order'];
            $query->orderBy($order);
        } else {
            [$order, $direction] = $this->validatedData['order'];
            $query->orderBy($order, $direction);
        }
    }

    public function buildLimit(Builder $query)
    {
        if (count($this->validatedData['limit']) < 2) {
            [$limit] = $this->validatedData['limit'];
            $query->limit($limit);
        } else {
            [$offset, $limit] = $this->validatedData['limit'];
            $query->offset($offset)->limit($limit);
        }
    }
}
