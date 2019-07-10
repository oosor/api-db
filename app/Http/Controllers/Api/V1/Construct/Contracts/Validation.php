<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 11:19
 */

namespace App\Http\Controllers\Api\V1\Construct\Contracts;


interface Validation
{
    public function validation();
    public function isValid(): bool;
    public function getMessage();
}
