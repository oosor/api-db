<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 22:07
 */

namespace App\Http\Controllers\Api\V1\Queries\Contracts;


interface Validation
{
    public function validation();
    public function isValid(): bool;
    public function getMessage();
}
