<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 11:13
 */

namespace App\Http\Controllers\Api\V1\Construct\Core;


use App\Http\Controllers\Api\V1\Construct\Contracts\{
    Column as ColumnContract
};
use App\Http\Controllers\Api\V1\Construct\Exceptions\ValidationException;

/**
 * Item column for models
 * Construct array $data => [
 *  'name' => 'string',
 *  'type' => 'string' in enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::TYPES),
 *  'options' => 'array<mixed> | integer | null' in enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::TYPE_OPTIONS),
 *  'modifier' => 'string' in enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::MODIFIER),
 *  'modifier_options' => enum(App\Http\Controllers\Api\V1\Construct\Contracts\Model::TYPE_MODIFIER),
 *  'patch' => [
 *      'action' => 'string', // 'push', 'change', 'drop', 'rename'
 *      'new_name' => 'string', // only 'action' => 'rename'
 *  ],
 * ],
 * */
class Column implements ColumnContract
{
    private $column;
    private $validationColumn = [];
    private $valid = false;
    protected $error;

    /**
     * @param array $data
     * @return void
     * */
    public function __construct(array $data)
    {
        $this->column = $data;
        $this->validation();
    }

    /** handler validation
     * */
    public function validation()
    {
        try {
            if (!is_array($this->column)) {
                throw new ValidationException('Column data does\'t valid array');
            }

            if (empty($this->column['name']) || !is_string($this->column['name'])) {
                throw new ValidationException('Column data[name] does\'t valid string');
            }
            $this->validationColumn['name'] = $this->column['name'];

            if (empty($this->column['type']) || !is_string($this->column['name']) || !in_array($this->column['type'], static::TYPES)) {
                throw new ValidationException('Column data[type] does\'t valid TYPES enum(string)');
            }
            $this->validationColumn['type'] = $this->column['type'];

            $this->validationTypeOptions();

            $this->validationModifier();

            $this->validationPatchActions();

            $this->valid = true;
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
     * @return string
     * */
    public function getMessage()
    {
        return $this->error ?? null;
    }

    public function getName()
    {
        return $this->validationColumn['name'] ?? null;
    }

    public function getType()
    {
        return $this->validationColumn['type'] ?? null;
    }

    public function getOptions()
    {
        return $this->validationColumn['options'] ?? null;
    }

    public function getModifier()
    {
        return $this->validationColumn['modifier'] ?? null;
    }

    public function getModifierValue()
    {
        return $this->validationColumn['modifier_options'] ?? null;
    }

    public function getPatchActions()
    {
        return $this->validationColumn['patch'] ?? null;
    }


    /**
     * @throws ValidationException
     * */
    protected function validationTypeOptions()
    {
        if (isset($this->column['options']) && array_key_exists($this->column['type'], static::TYPE_OPTIONS)) {
            if (is_array($this->column['options'])) {
                $requiring = explode(':', static::TYPE_OPTIONS[$this->column['type']]);

                if (count($requiring) == 2) { // ex.: 'array:integer'
                    foreach ($this->column['options'] as $option) {
                        switch ($requiring[1]) {
                            case 'integer':
                                if (!is_numeric($option)) {
                                    throw new ValidationException('Column data[options] does\'t valid TYPE_OPTIONS enum(integer)');
                                }
                                $this->validationColumn['options'] = $this->column['options'];
                                break;
                            case 'string':
                                if (!is_string($option)) {
                                    throw new ValidationException('Column data[options] does\'t valid TYPE_OPTIONS enum(string)');
                                }
                                $this->validationColumn['options'] = $this->column['options'];
                                break;
                        }
                    }
                    return;
                }
            } else if (is_numeric($this->column['options']) && static::TYPE_OPTIONS[$this->column['type']] == 'integer') {
                $this->validationColumn['options'] = [$this->column['options']];
                return;
            }

            throw new ValidationException('Column data[options] does\'t valid TYPE_OPTIONS');
        }

        $this->validationColumn['options'] = null;
    }

    /**
     * @throws ValidationException
     * */
    protected function validationModifier()
    {
        if (!empty($this->column['modifier'])) {
            if (!is_string($this->column['modifier']) || !in_array($this->column['modifier'], static::MODIFIER)) {
                throw new ValidationException('Column data[modifier] does\'t valid MODIFIER enum(string)');
            }

            if (array_key_exists($this->column['modifier'], static::TYPE_MODIFIER)) {
                $accepted = explode('|', static::TYPE_MODIFIER[$this->column['modifier']]);
                $status = array_reduce($accepted, function ($status, $item) {
                    switch ($item) {
                        case 'string':
                            return isset($this->column['modifier_options']) && is_string($this->column['modifier_options']) ? true : $status;
                        case 'mixed':
                            return true;
                        case 'boolean':
                            return isset($this->column['modifier_options']) && is_bool($this->column['modifier_options']) ? true : $status;
                        case 'null':
                            return !isset($this->column['modifier_options']) || is_null($this->column['modifier_options']) ? true : $status;
                    }
                    return $status;
                }, false);

                if ($status) {
                    $this->validationColumn['modifier_options'] = $this->column['modifier_options'] ?? null;
                } else {
                    throw new ValidationException('Column data[modifier_options] does\'t valid TYPE_MODIFIER');
                }
            }
            $this->validationColumn['modifier'] = $this->column['modifier'];
            return;
        }

        $this->validationColumn['modifier'] = null;
    }

    /**
     * @throws ValidationException
     * */
    protected function validationPatchActions()
    {
        if (!empty($this->column['patch'])) {
            if (!is_array($this->column['patch']) || empty($this->column['patch']['action']) || !is_string($this->column['patch']['action'])) {
                throw new ValidationException('Column data[patch] does\'t valid');
            }
            switch ($this->column['patch']['action']) {
                case 'push':
                case 'change':
                case 'drop':
                    $this->validationColumn['patch'] = $this->column['patch'];
                    break;
                case 'rename':
                    if (empty($this->column['patch']['new_name']) || !is_string($this->column['patch']['new_name'])) {
                        throw new ValidationException('Column data[patch][new_name] does\'t valid');
                    }
                    $this->validationColumn['patch'] = $this->column['patch'];
            }
            if (empty($this->validationColumn['patch'])) {
                throw new ValidationException('Column data[patch][action] does\'t valid');
            }
            return;
        }

        $this->validationColumn['patch'] = null;
    }
}
