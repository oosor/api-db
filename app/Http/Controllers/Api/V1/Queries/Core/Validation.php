<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 23:12
 */

namespace App\Http\Controllers\Api\V1\Queries\Core;


use App\Http\Controllers\Api\V1\Queries\Exceptions\ValidationException;

trait Validation
{
    /** available ['where', 'orWhere', 'whereIn', 'orWhereIn', 'whereNull', 'whereNotNull', 'whereDate']
     * [
     *  ['type' => 'where', 'column' => 'name_col', 'is' => '=', 'value' => 'free_val',
     *      'closure' => [
     *          `validationWhere` expressions
     *      ],
     *  ],
     *  ['type' => 'orWhere', 'column' => 'id', 'is' => '>', 'value' => '3'],
     *  ['type' => 'whereIn', 'column' => 'id', 'value' => ['3', '4', '6']],
     *  ['type' => 'orWhereIn', 'column' => 'id', 'value' => ['3', '4', '6']],
     *  ['type' => 'whereNull', 'column' => 'name_col'],
     *  ['type' => 'whereNotNull', 'column' => 'name_col'],
     *  ['type' => 'whereDate', 'column' => 'created_at', 'value' => '2016-12-31'],
     *  ['type' => 'whereDate', 'column' => 'created_at', 'is' => '>', 'value' => '2016-12-31'],
     * ]
     *
     * @param array $where
     * @return bool
     * @throws ValidationException
     * */
    protected function validationWhere($where = null)
    {
        $where = $where ?? $this->srcData['where'] ?? null;
        if (!empty($where)) {
            if (!is_array($where)) {
                throw new ValidationException('Param columns does\'t valid array');
            }

            return array_reduce($where, function ($status, $item) {
                if (isset($item['type']) && is_string($item['type'])) {

                    // only where and orWhere support the closure
                    if ($item['type'] == 'where' || $item['type'] == 'orWhere') {
                        if (isset($item['closure'])) {
                            if (is_array($item['closure'])) {
                                return $status && $this->validationWhere($item['closure']);
                            } else {
                                // closure not valid
                                return false;
                            }
                        }
                    }

                    $is = false;
                    switch ($item['type']) {
                        case 'where':
                        case 'orWhere':
                        case 'whereDate':
                            $is = isset($item['column'], $item['value']) && is_string($item['column']);
                            break;
                        case 'whereIn':
                        case 'orWhereIn':
                            $is = isset($item['column'], $item['value']) && is_string($item['column']) && is_array($item['value']);
                            break;
                        case 'whereNull':
                        case 'whereNotNull':
                            $is = isset($item['column']) && is_string($item['column']);
                    }

                    // where expressions not valid
                    // or is ok
                    return $is ? $status : false;
                }
                // not valid $item['type']
                return false;
            }, true);
        }
        // where expressions not found
        return true;
    }

    /** available ['leftJoin']
     * [
     *  [
     *      'type' => 'leftJoin',
     *      'table' => 'table_2',
     *      'foreign_key' => 'key1',
     *      'other_key' => 'key2',
     *      'closure' => [
     *          `validationWhere` expressions
     *      ],
     *  ]
     * ]
     *
     * @return bool
     * @throws ValidationException
     * */
    protected function validationWith()
    {
        if (!empty($this->srcData['with'])) {
            if (!is_array($this->srcData['with'])) {
                throw new ValidationException('Param `with` does\'t valid array');
            }

            return array_reduce($this->srcData['with'], function ($status, $item) {
                if (isset($item['type']) && is_string($item['type'])) {

                    $is = false;
                    switch ($item['type']) {
                        case 'leftJoin':
                            $is = isset($item['table'], $item['foreign_key'], $item['other_key'])
                                && is_string($item['table'])
                                && is_string($item['foreign_key'])
                                && is_string($item['other_key']);
                    }

                    // join params not valid
                    if (!$is) {
                        return false;
                    }

                    if (isset($item['closure'])) {
                        if (is_array($item['closure'])) {
                            return $this->validationWhere($item['closure']);
                        } else {
                            // closure not valid
                            return false;
                        }
                    }
                    // is ok
                    return $status;
                }
                // not valid $item['type']
                return false;
            }, true);
        }
        // with expressions not found
        return true;
    }

    /**
     * @throws ValidationException
     * */
    protected function validationQuery()
    {
        if (!empty($this->srcData['query'])) {
            if (!is_string($this->srcData['query'])) {
                throw new ValidationException('Param `query` is required and should be string, `query` does\'t valid');
            }
            if (strtoupper($this->srcData['query']) != 'SELECT'
                && strtoupper($this->srcData['query']) != 'UPDATE'
                && strtoupper($this->srcData['query']) != 'DELETE'
                && strtoupper($this->srcData['query']) != 'INSERT'
            ) {
                throw new ValidationException('Param `query` should be in enum(select, insert, update, delete), `query` does\'t valid');
            }
            $this->validatedData['query'] = $this->srcData['query'];
            return;
        }
        throw new ValidationException('Param `query` is required');
    }

    /**
     * @throws ValidationException
     * */
    protected function validationColumns()
    {
        if (!empty($this->srcData['columns'])) {
            if (is_array($this->srcData['columns'])) {
                $status = array_reduce($this->srcData['columns'], function ($status, $item) {
                    return $status && is_string($item);
                }, true);

                if ($status) {
                    $this->validatedData['columns'] = $this->srcData['columns'];
                    return;
                }
                throw new ValidationException('Param columns does\'t valid array, items should be type of string');
            }
            throw new ValidationException('Param columns does\'t valid array');
        }
        $this->validatedData['columns'] = null;
    }

    /**
     * @throws ValidationException
     * */
    protected function validationOrder()
    {
        if (!empty($this->srcData['order'])) {
            if (is_array($this->srcData['order'])) {
                if (is_string($this->srcData['order'][0])) {
                    if (isset($this->srcData['order'][1])) {
                        if (strtoupper($this->srcData['order'][1]) == 'ASC' || strtoupper($this->srcData['order'][1]) == 'DESC') {
                            $this->validatedData['order'] = $this->srcData['order'];
                            return;
                        }
                    } else {
                        $this->validatedData['order'] = $this->srcData['order'];
                        return;
                    }
                }
                throw new ValidationException('Param order does\'t valid array, items should be `[string[, "ASC"|"DESC"]]`');
            }
            throw new ValidationException('Param order does\'t valid array');
        }
        $this->validatedData['order'] = null;
    }

    /**
     * @throws ValidationException
     * */
    protected function validationLimit()
    {
        if (!empty($this->srcData['limit'])) {
            if (is_array($this->srcData['limit'])) {
                $status = array_reduce($this->srcData['limit'], function ($status, $item) {
                    return $status && is_integer($item);
                }, true);

                if ($status) {
                    $this->validatedData['limit'] = $this->srcData['limit'];
                    return;
                }
                throw new ValidationException('Param limit does\'t valid array, items should be `[integer[, integer]]`');
            }
            throw new ValidationException('Param limit does\'t valid array');
        }
        $this->validatedData['limit'] = null;
    }


    /**
     * @throws ValidationException
     * */
    protected function validationInsertData()
    {
        if (!empty($this->srcData['data'])) {
            if (is_array($this->srcData['data'])) {
                if (array_reduce($this->srcData['data'], function ($status, $item) {
                    return $status && is_array($item);
                }, true)) {
                    $this->validatedData['data'] = $this->srcData['data'];
                    return;
                }
                $this->validatedData['data'] = null;
            }
            throw new ValidationException('Param `data` should be array of object(s) or of associative array');
        }
        throw new ValidationException('Param `data` for insert query is required');
    }

    /**
     * @throws ValidationException
     * */
    protected function validationUpdateData()
    {
        if (!empty($this->srcData['data'])) {
            if (is_array($this->srcData['data'])) {
                $this->validatedData['data'] = $this->srcData['data'];
                return;
            }
            $this->validatedData['data'] = null;
            throw new ValidationException('Param `data` should be object or associative array');
        }
        throw new ValidationException('Param `data` for update query is required');
    }
}
