<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 09.07.19
 * Time: 21:58
 */

namespace App\Http\Controllers\Api\V1\Construct\Core;


use App\Http\Controllers\Api\V1\Construct\Contracts\Model;
use App\Http\Controllers\Api\V1\Construct\Exceptions\{
    BuilderException, ConstructException
};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\{
    Collection, Facades\DB, Facades\Schema
};

trait Prototype
{
    use Helpers;

    /**
     * @var Model $model
     * */
    private $model;

    /**
     * @param Model $model
     * @return void
     * */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /** store table
     * @throws ConstructException
     * */
    public function up()
    {
        if (!$this->hasTable()) {
            Schema::connection(static::getConnection())->create($this->model->getTable(), function (Blueprint $table) {
                $this->getSchema($table);
            });
        } else {
            throw new ConstructException('Not created table `' . $this->model->getTable() . '`, this table exist');
        }
    }

    /** patch (update) table
     * @throws ConstructException
     * */
    public function patch()
    {
        if ($this->hasTable()) {
            Schema::connection(static::getConnection())->table($this->model->getTable(), function (Blueprint $table) {
                $this->getSchema($table, true);
            });
        } else {
            throw new ConstructException('Not updated table `' . $this->model->getTable() . '`, this table not exist');
        }
    }

    /** drop table
     * @param string $table
     * */
    public static function down(string $table)
    {
        Schema::connection(static::getConnection())->dropIfExists($table);
    }

    /** list tables
     * @return Collection
     * */
    public static function listTables()
    {
        try {
            $list = DB::connection(static::getConnection())->getDoctrineSchemaManager()->listTables();
            if (!empty($list)) {

                return new Collection(array_reduce($list, function ($collect, $table) {
                    $collect[] = [
                        'table' => $table->getName(),
                        'columns' => array_map(function ($column) {
                            return $column->toArray();
                        }, $table->getColumns()),
                    ];
                    return $collect;
                }, []));
            }

            return new Collection();
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());
        }

        return null;
    }

    /** detail table for name
     * @param string $name
     * @return array
     * */
    public static function table(string $name)
    {
        if (Schema::connection(static::getConnection())->hasTable($name)) {
            try {
                $table = DB::connection(static::getConnection())->getDoctrineSchemaManager()->listTableDetails($name);
                return [
                    'table' => $table->getName(),
                    'columns' => array_map(function ($column) {
                        return $column->toArray();
                    }, $table->getColumns()),
                ];
            } catch (\Exception $exception) {
                logger()->error($exception->getMessage());
            }
        }

        return null;
    }

    /** rename table
     * @param string $newName
     * @throws ConstructException
     * */
    public function rename(string $newName = '')
    {
        if (empty($newName)) {
            $newName = $this->model->getRenamedTable();
        }
        if (empty($newName)) {
            throw new ConstructException('Not renamed table `' . $this->model->getTable() . '`, new name is empty or not valid');
        }
        if ($this->hasTable($newName)) {
            throw new ConstructException('Not renamed table `' . $this->model->getTable() . '`, new table name `' . $newName . '` is already exists');
        }
        if ($this->hasTable()) {
            Schema::connection(static::getConnection())->rename($this->model->getTable(), $newName);
        } else {
            throw new ConstructException('Not renamed table `' . $this->model->getTable() . '`, this table not exist');
        }
    }

    /** smart constructing table
     * @param Blueprint $table
     * @param bool $isPatching
     * */
    protected function getSchema(Blueprint $table, bool $isPatching = false)
    {
        try {
            foreach ($this->model->getCols() as $column) {
                if ($isPatching) {
                    $patching = $column->getPatchActions();
                    if (is_null($patching)) {
                        continue;
                    }
                    switch ($patching['action']) {
                        case 'push':
                            if (!$this->hasColumn($column->getName())) {
                                $this->constructing($table, $column);
                            }
                            break;
                        case 'change':
                            if ($this->hasColumn($column->getName())) {
                                $this->constructing($table, $column)->change();
                            }
                            break;
                        case 'drop':
                            if ($this->hasColumn($column->getName())) {
                                $table->dropColumn($column->getName());
                            }
                            break;
                        case 'rename':
                            if ($this->hasColumn($column->getName())) {
                                $table->renameColumn($column->getName(), $patching['new_name']);
                            }
                    }
                } else {
                    $this->constructing($table, $column);
                }
            }
        } catch (BuilderException $builderException) {
            $builderException->write();
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());
        }
    }
}
