<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 19:07
 */

namespace App\Http\Controllers\Api\V1\Construct;


trait ControllersHelper
{

    /** rejected resource
     * @param string $message
     * @param mixed $data
     * @return array
     * */
    protected function rejected($message, $data = null)
    {
        return [
            'status' => false,
            'error' => $message,
            'data' => $data ?? [],
        ];
    }

    /** with resource
     * @param string $type
     * @return array
     * */
    protected function getSuccessAdditional($type)
    {
        return [
            'status' => true,
            'operation' => $type,
        ];
    }
}
