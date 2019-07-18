<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 11.07.19
 * Time: 14:49
 */

namespace App\Http\Controllers\Api\V1\Queries\Contracts;


interface QueryBuilder
{
    public function getQueryData(): array;

    /**
     * @return \Illuminate\Support\Collection
     * */
    public function selectRows();

    /**
     * @return bool
     * */
    public function insertRows();

    /**
     * @return integer
     * */
    public function updateRows();

    /**
     * @return integer
     * */
    public function deleteRows();

    /** entry query
     * @return mixed
     * */
    public function getQuery();
}
