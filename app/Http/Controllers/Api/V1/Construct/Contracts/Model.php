<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 10:24
 */

namespace App\Http\Controllers\Api\V1\Construct\Contracts;


interface Model extends Validation
{
    /**
     * @return string
     * */
    public function getTable();

    /**
     * @return string
     * */
    public function getRenamedTable();

    /**
     * @return Column[]
     * */
    public function getCols();
}
