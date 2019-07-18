<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 11.07.19
 * Time: 15:53
 */

namespace App\Http\Controllers\Api\V1\Queries\Core\Builders;


use Illuminate\Database\Query\Builder;

class BuilderWhere
{
    private $query;
    private $table;

    public function __construct(Builder $query, string $table)
    {
        $this->query = $query;
        $this->table = $table;
    }

    public function init(array $whereData, Builder $query = null)
    {
        $query = $query ?? $this->query;

        foreach ($whereData as $datum) {
            $this->addQuery($datum, $query);
        }
    }

    protected function addQuery($item, Builder $query = null)
    {
        $query = $query ?? $this->query;
        $sep = $item['is'] ?? '=';

        switch ($item['type']) {
            case 'where':
            case 'orWhere':
                if (!empty($item['closure'])) {
                    $query->{$item['type']}($this->getClosureHelper($item['closure']));
                } else {
                    $query->{$item['type']}($this->table . '.' . $item['column'], $sep, $item['value']);
                }
                break;
            case 'whereIn':
            case 'orWhereIn':
                $query->{$item['type']}($this->table . '.' . $item['column'], $item['value']);
                break;
            case 'whereNull':
            case 'whereNotNull':
                $query->{$item['type']}($this->table . '.' . $item['column']);
                break;
            case 'whereDate':
                $query->{$item['type']}($this->table . '.' . $item['column'], $sep, $item['value']);
        }
    }

    protected function getClosureHelper($closureItems)
    {
        return function ($query) use ($closureItems) {
            $this->init($closureItems, $query);
        };
    }
}

