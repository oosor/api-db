<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 10:23
 */

namespace App\Http\Controllers\Api\V1\Construct\Models;


use App\Http\Controllers\Api\V1\Construct\Contracts\Model;
use App\Http\Controllers\Api\V1\Construct\Core\Column;
use App\Http\Controllers\Api\V1\Construct\Exceptions\ValidationException;

/** Universal model
 * Constructor for Migration
 * Construct array $data =>
 * [
 *  'table'     => 'string',
 *  'new_name'  => 'string',
 *  'columns'   => [
 *      [
 *          'name' => 'string',
 *          'type' => 'string' in enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::TYPES),
 *          'options' => 'array<mixed> | integer | null' in enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::TYPE_OPTIONS),
 *          'modifier' => 'string' in enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::MODIFIER),
 *          'modifier_options' => enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::TYPE_MODIFIER),
 *          'patch' => [
 *              'action' => 'string', // 'push', 'change', 'drop', 'rename'
 *              'new_name' => 'string', // only 'action' => 'rename'
 *          ],
 *      ],
 *  ],
 * ],
 *
 * */
class Universal implements Model
{
    private $constructData;
    private $validationConstructData = [];
    private $valid = false;
    protected $error;
    protected $columnsError = [];

    /**
     * @param array $data
     * @return void
     * */
    public function __construct(array $data)
    {
        $this->constructData = $data;
        $this->validation();
    }

    /** name table model
     * @return string
     * */
    public function getTable()
    {
        return $this->validationConstructData['table'] ?? null;
    }

    /** new name table model
     * @return string
     * */
    public function getRenamedTable()
    {
        return $this->validationConstructData['new_name'] ?? null;
    }

    /**
     * @return Column[]
     * */
    public function getCols()
    {
        return $this->validationConstructData['columns'] ?? [];
    }

    /** handler validation
     * */
    public function validation()
    {
        try {
            if (empty($this->constructData['table']) || !is_string($this->constructData['table']) || !$this->validationTableName($this->constructData['table'])) {
                throw new ValidationException('Construct data[table] does\'t valid');
            }
            $this->validationConstructData['table'] = $this->constructData['table'];

            if (isset($this->constructData['new_name']) && is_string($this->constructData['new_name']) && $this->validationTableName($this->constructData['table'])) {
                $this->validationConstructData['new_name'] = $this->constructData['new_name'];
                $this->valid = true;
                return;
            }

            if (empty($this->constructData['columns']) || !is_array($this->constructData['columns'])) {
                throw new ValidationException('Construct data[columns] does\'t valid, array required');
            }
            $this->validationConstructData['columns'] = array_map(function ($column) {
                if (is_array($column)) {
                    $model = new Column($column);
                    if (!$model->isValid()) {
                        $this->injectColumnError($model);
                        return null;
                    }
                    return $model;
                } else {
                    throw new ValidationException('Construct data[columns] -> {column} does\'t valid, array required');
                }
            }, $this->constructData['columns']);

            $this->valid = array_reduce($this->validationConstructData['columns'], function ($status, $column) {
                if (is_null($column)) {
                    return false;
                }
                return $status;
            }, true);
        } catch (ValidationException $validationException) {
            $validationException->write();
            $this->error = $validationException->getMessage();
        }
    }

    /** valid model
     * @return bool
     * */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /** message error
     * @return array
     * */
    public function getMessage()
    {
        return [
            'table' => $this->getTable(),
            'error' => $this->error ?? null,
            'detail' => $this->columnsError,
        ];
    }


    /** validate table name
     * @param string $name
     * @return bool
     * */
    protected function validationTableName(string $name): bool
    {
        return preg_match('/^\w+$/', $name);
    }

    /** inject inner Column error in collect
     * @param Column $model
     * */
    protected function injectColumnError(Column $model)
    {
        $this->columnsError[] = [
            'column' => $model->getName(),
            'error' => $model->getMessage(),
        ];
    }

}
