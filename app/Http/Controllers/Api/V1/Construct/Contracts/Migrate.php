<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 10:14
 */

namespace App\Http\Controllers\Api\V1\Construct\Contracts;


interface Migrate
{
    public function up();
    public function patch();
    public static function down(string $table);
}
