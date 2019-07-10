<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 14:56
 */

namespace App\Http\Controllers\Api\V1\Construct\Exceptions;


class ConstructException extends \Exception
{

    public function write()
    {
        logger()->error($this->getCustomMessage());
    }

    protected function getCustomMessage()
    {
        return 'Error construct: In '
            . $this->getFile() . ' | '
            . $this->getLine() . ' | '
            . $this->getCode() . ' | '
            . $this->getMessage();
    }
}
