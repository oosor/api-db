<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 11:22
 */

namespace App\Http\Controllers\Api\V1\Construct\Contracts;


interface Column extends Validation
{
    const TYPES = [
        'bigIncrements',
        'bigInteger',
        'binary',
        'boolean',
        'char',
        'date',
        'dateTime',
        'dateTimeTz',
        'decimal',
        'double',
        'enum',
        'float',
        'geometry',
        'geometryCollection',
        'increments',
        'integer',
        'ipAddress',
        'json',
        'jsonb',
        'lineString',
        'longText',
        'macAddress',
        'mediumIncrements',
        'mediumInteger',
        'mediumText',
        'morphs',
        'multiLineString',
        'multiPoint',
        'multiPolygon',
        'nullableMorphs',
        'nullableTimestamps',
        'point',
        'polygon',
        'rememberToken',
        'set',
        'smallIncrements',
        'smallInteger',
        'softDeletes',
        'softDeletesTz',
        'string',
        'text',
        'time',
        'timeTz',
        'timestamp',
        'timestampTz',
        'timestamps',
        'timestampsTz',
        'tinyIncrements',
        'tinyInteger',
        'unsignedBigInteger',
        'unsignedDecimal',
        'unsignedInteger',
        'unsignedMediumInteger',
        'unsignedSmallInteger',
        'unsignedTinyInteger',
        'uuid',
        'year',
    ];

    const TYPE_OPTIONS = [
        'char' => 'integer',
        'decimal' => 'array:integer',
        'double' => 'array:integer',
        'enum' => 'array:string',
        'float' => 'array:integer',
        'set' => 'array:string',
        'string' => 'integer',
        'unsignedDecimal' => 'array:integer',
    ];

    const MODIFIER = [
        'charset',
        'collation',
        'comment',
        'default',
        'nullable',
        'unsigned',
    ];

    const TYPE_MODIFIER = [
        'charset' => 'string',
        'collation' => 'string',
        'comment' => 'string',
        'default' => 'mixed',
        'nullable' => 'boolean|null',
        'unsigned' => 'null',
    ];

    /**
     * @return string
     * */
    public function getName();

    /**
     * @return string
     * */
    public function getType();

    /**
     * @return array
     * */
    public function getOptions();

    /**
     * @return string
     * */
    public function getModifier();

    /**
     * @return mixed
     * */
    public function getModifierValue();

    /**
     * @return array
     * */
    public function getPatchActions();
}
