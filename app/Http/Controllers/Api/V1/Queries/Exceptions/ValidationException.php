<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 22:01
 */

namespace App\Http\Controllers\Api\V1\Queries\Exceptions;


class ValidationException extends \Exception
{
    public function write()
    {
        logger()->error($this->getCustomMessage());
    }

    protected function getCustomMessage()
    {
        return $this->getFile() . ' | '
            . $this->getLine() . ' | '
            . $this->getCode() . ' | '
            . $this->getMessage();
    }
}
