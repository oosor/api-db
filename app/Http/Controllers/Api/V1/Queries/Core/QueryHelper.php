<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 21:58
 */

namespace App\Http\Controllers\Api\V1\Queries\Core;


use App\Http\Controllers\Api\V1\Queries\Exceptions\ValidationException;
use Illuminate\Support\Facades\Schema;

trait QueryHelper
{
    use Validation, BuilderHelper;

    private $valid = false;
    protected $errorMessage;
    private static $connections;

    public function validation()
    {
        try {
            // @required validate select, update, insert, delete
            $this->validationQuery();

            // @required validate table name
            if (empty($this->srcData['table']) || !is_string($this->srcData['table'])) {
                throw new ValidationException('Param `table` name does\'t valid');
            }
            $this->validatedData['table'] = $this->srcData['table'];

            // validate specific for query data
            switch (strtoupper($this->validatedData['query'])) {
                case 'INSERT':
                    $this->validationInsertData();
                    break;
                case 'UPDATE':
                    $this->validationUpdateData();
                    break;
            }

            // validate table columns
            $this->validationColumns();

            // validate table where closure
            if (!$this->validationWhere()) {
                throw new ValidationException('Param `where` does\'t valid');
            }
            $this->validatedData['where'] = $this->srcData['where'] ?? null;

            // validate table relationship closure
            if (!$this->validationWith()) {
                throw new ValidationException('Param `with` does\'t valid');
            }
            $this->validatedData['with'] = $this->srcData['with'] ?? null;

            // validate table ordering
            $this->validationOrder();

            // validate table limit
            $this->validationLimit();

            $this->valid = true;
        } catch (ValidationException $validationException) {
            $validationException->write();
            $this->errorMessage = $validationException->getMessage();
        }
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getMessage()
    {
        return $this->errorMessage;
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

    /** get list columns
     * @param string $table
     * @return array
     * */
    public static function getColumnsListing(string $table)
    {
        return Schema::connection(static::getConnection())->getColumnListing($table);
    }
}
