<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 11.07.19
 * Time: 15:31
 */

namespace App\Http\Controllers\Api\V1\Queries\Core\Builders;


use App\Http\Controllers\Api\V1\Queries\Core\QueryBuilder;
use Illuminate\Database\Query\Builder;

/**
 * init($withData)
 * $withData:
 * [
 *  [
 *      'type' => 'leftJoin',
 *      'table' => 'table_2',
 *      'foreign_key' => 'key1',
 *      'other_key' => 'key2',
 *      'closure' => [
 *          `Validation::validationWhere` expressions
 *      ],
 *  ]
 * ]
 * */
class BuilderWith
{
    private $query;
    private $table;

    public function __construct(Builder $query, string $table)
    {
        $this->query = $query;
        $this->table = $table;
    }

    public function init(array $withData)
    {
        foreach ($withData as $datum) {
            $this->addQuery($datum);
        }
    }

    protected function addQuery($item)
    {
        switch ($item['type']) {
            case 'leftJoin':
                $this->leftJoin($item);
        }
    }

    protected function leftJoin($item)
    {
        $query = $this->query->addSelect($this->selectJoinedCol($item['table']));

        if (!empty($item['closure'])) {
            $this->query->leftJoin($item['table'], function ($join) use ($item) {
                $join->on($this->table . '.' . $item['foreign_key'], '=', $item['table'] . '.' . $item['other_key']);
                (new BuilderWhere($join, $item['table']))->init($item['closure']);
            });
        } else {
            $query->leftJoin($item['table'], $this->table . '.' . $item['foreign_key'], '=', $item['table'] . '.' . $item['other_key']);
        }
    }

    protected function selectJoinedCol($table)
    {
        return array_map(function ($item) use ($table) {
            return $table . '.' . $item . ' as ' . $table . '.' . $item;
        }, QueryBuilder::getColumnsListing($table));
    }
}
