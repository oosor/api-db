<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 21:53
 */

namespace App\Http\Controllers\Api\V1\Queries\Core;


use App\Http\Controllers\Api\V1\Queries\Contracts\{
    Builder, Validation, QueryBuilder as RequestBuilder
};
use Illuminate\Support\Facades\DB;

class QueryBuilder implements RequestBuilder, Builder, Validation
{
    use QueryHelper;

    private $srcData;
    private $validatedData = [];

    public function __construct(array $data)
    {
        $this->srcData = $data;
        $this->validation();
    }

    public function getQueryData(): array
    {
        return $this->validatedData;
    }


    /**
     * @return \Illuminate\Support\Collection
     * */
    public function selectRows()
    {
        return DB::connection(static::getConnection())
            ->table($this->validatedData['table'])
            ->select($this->validatedData['table'] . '.*')
            ->when($this->validatedData['columns'], function ($query) {
                $this->buildColumns($query);
            })
            ->when($this->validatedData['with'], function ($query) {
                $this->buildWith($query);
            })
            ->when($this->validatedData['where'], function ($query) {
                $this->buildWhere($query);
            })
            ->when($this->validatedData['order'], function ($query) {
                $this->buildOrder($query);
            })
            ->when($this->validatedData['limit'], function ($query) {
                $this->buildLimit($query);
            })
            ->get();
    }

    /**
     * @return bool is inserted
     * */
    public function insertRows()
    {
        return DB::connection(static::getConnection())
            ->table($this->validatedData['table'])
            ->insert($this->validatedData['data']);
    }

    /**
     * @return integer count rows updated
     * */
    public function updateRows()
    {
        return DB::connection(static::getConnection())
            ->table($this->validatedData['table'])
            ->when($this->validatedData['where'], function ($query) {
                $this->buildWhere($query);
            })
            ->update($this->validatedData['data']);
    }

    /**
     * @return integer count rows deleted
     * */
    public function deleteRows()
    {
        return DB::connection(static::getConnection())
            ->table($this->validatedData['table'])
            ->when($this->validatedData['where'], function ($query) {
                $this->buildWhere($query);
            })
            ->delete();
    }

    public function getQuery()
    {
        switch (strtoupper($this->validatedData['query'])) {
            case 'SELECT':
                return $this->selectRows();
            case 'INSERT':
                return $this->insertRows();
            case 'UPDATE':
                return $this->updateRows();
            case 'DELETE':
                return $this->deleteRows();
        }

        return null;
    }

}
