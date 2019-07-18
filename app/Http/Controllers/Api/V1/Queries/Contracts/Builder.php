<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 21:52
 */

namespace App\Http\Controllers\Api\V1\Queries\Contracts;


use Illuminate\Database\Query\{
    Builder as IlluminateBuilder
};

interface Builder
{
    public function buildColumns(IlluminateBuilder $query);

    public function buildWhere(IlluminateBuilder $query);

    public function buildWith(IlluminateBuilder $query);

    public function buildOrder(IlluminateBuilder $query);

    public function buildLimit(IlluminateBuilder $query);

}
