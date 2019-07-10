<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 13:44
 */

namespace App\Http\Controllers\Api\V1\Construct\Core;


use App\Http\Controllers\Api\V1\Construct\Exceptions\BuilderException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Api\V1\Construct\Contracts\{
    Column as ColumnContract
};

trait Helpers
{
    protected static $connections;

    /** has table
     * @param string $table
     * @return bool
     * */
    public function hasTable(string $table = ''): bool
    {
        if (empty($table)) {
            $table = $this->model->getTable();
        }
        return Schema::connection(static::getConnection())->hasTable($table);
    }

    /** has column in table
     * @param string $column
     * @return bool
     * */
    public function hasColumn(string $column): bool
    {
        return Schema::connection(static::getConnection())->hasColumn($this->model->getTable(), $column);
    }

    /** constructing query builder table
     * @param Blueprint $table
     * @param ColumnContract $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     *
     * @throws BuilderException
     * */
    public function constructing(Blueprint $table, ColumnContract $column)
    {
        $options = $column->getOptions();
        $modifier = $column->getModifier();
        $modifierValue = $column->getModifierValue();
        $query = null;

        if (isset($options)) {
            $query = $table->{$column->getType()}($column->getName(), ...$options);
        } else {
            $query = $table->{$column->getType()}($column->getName());
        }

        if (is_null($query)) {
            throw new BuilderException('Error builder, $query is null');
        }

        if (isset($modifier)) {
            if (isset($modifierValue)) {
                $query->{$modifier}($modifierValue);
            } else {
                $query->{$modifier}();
            }
        }

        return $query;
    }

    /** get connection
     * @return string
     * */
    public static function getConnection()
    {
        if (!isset(static::$connections)) {
            static::$connections = env('DB_CONNECTION_STORAGE', 'mysql-storage');
        }
        return static::$connections;
    }
}
