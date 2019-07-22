# Сервер взаимодействия с хранилищем MySQL (MariaDB, PostgreSQL)

Сервис удаленной Базы данных с простым управлением структуры и данных БД.<br>
Предоставляет в себе механизмы сильной безопасности и надежной авторизации доступа к сервису базы данных.

## Возможности

1. Имеется конструктор таблиц БД, который имеет в свеем арсенале: 
    - Смотреть список таблиц или одной конкретной таблицы (с детальным описанием всех столбцов);
    - Создавать таблицы определенной структуры, столбцами, типами столбцов, и еще много чего;
    - Обновлять имя таблицы;
    - Обновлять структуру таблицы (имя столбцов, тип столбцов, добавлять новые и удалять ненужные столбцы и ...);
    - Удалять ненужные таблицы.
2. Гибкий конструктор запросов в Базу данных:
    - Получать строки данных с БД:
        - необходимые колонки таблицы;
        - с разными условиями фильтрации (см. ниже);
        - дополнительной связкой с другими таблицами, которые могут иметь вложенные запросы условия фильтрации;
        - сортировка строк данных;
        - срез строк данных (пропуск, лимит).
    - Добавлять новые строки данных в БД;
    - Обновлять строки данных в БД с условиями;
    - Удалять строки данных в БД с условиями;
3. Сложный механизм авторизации (достаточно простой в использовании).

## Установка и настройка

1. Скачать репозиторий [Сервера](https://gitlab.com/api-db/server) и установить все пакеты composer.
2. Настроить все подключения к базам данных:
    - **DB_CONNECTION_CONTROL**=mysql-control - системная База данных (используется для данных контроля подключений к серверу);
    - **DB_CONNECTION_STORAGE**=mysql-storage - База данных для клиентских таблиц и данных.
3. Установить все миграции `php artisan migrate`
4. Создать клиентов для работы OAuth2 `php artisan passport:install` и `php artisan passport:client --client`
в ответ сервер вернет `clientID` и `secret` для `Laravel Personal Access Client` и `Laravel Password Grant Client`
, эти данные стоить где нибудь сохранить для дальнейшего использования при получении токенов доступа
 (при утере их можно посмотреть в Базе данных в таблице `oauth_clients`)

## Управление таблицами Базы данных

Для простой работы с конструктором таблиц Базы данных существует отдельная библиотека [oosor/client-php-construct](https://gitlab.com/api-db/client-php)

#### Список всех таблиц Базы данных

- Request: GET `/api/v1/construct`;
- Response:<br>

success:
```json
{
    "status": true,
    "operation": "index",
    "data": [
        {
            "table": "table",
            "columns": {
                "id": {
                    "name": "id",
                    "type": {},
                    "default": null,
                    "notnull": true,
                    "length": null,
                    "precision": 10,
                    "scale": 0,
                    "fixed": false,
                    "unsigned": true,
                    "autoincrement": true,
                    "columnDefinition": null,
                    "comment": null
                },
                "field_1": {
                    "name": "field_1",
                    "type": {},
                    "default": null,
                    "notnull": false,
                    "length": 100,
                    "precision": 10,
                    "scale": 0,
                    "fixed": false,
                    "unsigned": false,
                    "autoincrement": false,
                    "columnDefinition": null,
                    "comment": null,
                    "collation": "utf8mb4_unicode_ci"
                }
            }
        },
        {
            "table": "table_2",
            "columns": {
                "id": {
                    "name": "id",
                    "type": {},
                    "default": null,
                    "notnull": true,
                    "length": null,
                    "precision": 10,
                    "scale": 0,
                    "fixed": false,
                    "unsigned": true,
                    "autoincrement": true,
                    "columnDefinition": null,
                    "comment": null
                },
                "field_other": {
                    "name": "field_other",
                    "type": {},
                    "default": null,
                    "notnull": false,
                    "length": 255,
                    "precision": 10,
                    "scale": 0,
                    "fixed": false,
                    "unsigned": false,
                    "autoincrement": false,
                    "columnDefinition": null,
                    "comment": null,
                    "collation": "utf8mb4_unicode_ci"
                }
            }
        }
    ]
}
```

error:
```json
{
    "status": false,
    "error": "message error",
    "data": ["detail data error"]
}
```

#### Просмотр одной таблицы Базы данных

- Request: GET `/api/v1/construct/{name_table}`;
- Response:<br>

success:
```json
{
    "status": true,
    "operation": "show",
    "data": {
        "table": "table",
        "columns": {
            "id": {
                "name": "id",
                "type": {},
                "default": null,
                "notnull": true,
                "length": null,
                "precision": 10,
                "scale": 0,
                "fixed": false,
                "unsigned": true,
                "autoincrement": true,
                "columnDefinition": null,
                "comment": null
            },
            "field_1": {
                "name": "field_1",
                "type": {},
                "default": null,
                "notnull": false,
                "length": 100,
                "precision": 10,
                "scale": 0,
                "fixed": false,
                "unsigned": false,
                "autoincrement": false,
                "columnDefinition": null,
                "comment": null,
                "collation": "utf8mb4_unicode_ci"
            }
        }
    }
}
```

error:
```json
{
    "status": false,
    "error": "message error",
    "data": ["detail data error"]
}
```

#### Создать таблицу в Базе данных

- Request: POST `/api/v1/construct`;

Request data:
```json
{
    "table": "table_insert",
    "columns": [
        {
            "name": "id",
            "type": "bigIncrements"
        }, {
            "name": "col_name_2",
            "type": "double",
            "options": [6, 2],
            "modifier": "default",
            "modifier_options": "default value"
        }, {
            "name": "col_name_3",
            "type": "date",
            "modifier": "nullable"
        }
    ]
}
```

Description:
> - `table: string` - имя новой таблицы<br>
> - `columns: array` - масив данных форматирования столбцов таблицы<br>
> - `columns[].name: string` - имя столбца<br>
> - `columns[].type: string` - тип столбца (`bigIncrements`, `bigInteger`, `binary`, `boolean`, `char`, `date`, `dateTime`, `dateTimeTz`, `decimal`, `double`, `enum`, `float`, `geometry`, `geometryCollection`, `increments`, `integer`, `ipAddress`, `json`, `jsonb`, `lineString`, `longText`, `macAddress`, `mediumIncrements`, `mediumInteger`, `mediumText`, `morphs`, `multiLineString`, `multiPoint`, `multiPolygon`, `nullableMorphs`, `nullableTimestamps`, `point`, `polygon`, `rememberToken`, `set`, `smallIncrements`, `smallInteger`, `softDeletes`, `softDeletesTz`, `string`, `text`, `time`, `timeTz`, `timestamp`, `timestampTz`, `timestamps`, `timestampsTz`, `tinyIncrements`, `tinyInteger`, `unsignedBigInteger`, `unsignedDecimal`, `unsignedInteger`, `unsignedMediumInteger`, `unsignedSmallInteger`, `unsignedTinyInteger`, `uuid`, `year`)<br>
> - `columns[].options: any` - значение типа столбца (только для type (слева) `char: integer`, `decimal: integer[]`, `double: integer[]`, `enum: string[]`, `float: integer[]`, `set: string[]`, `string: integer`, `unsignedDecimal: integer[]`)<br>
> - `columns[].modifier: string` - модификатор столбца (`charset`, `collation`, `comment`, `default`, `nullable`, `unsigned`)<br>
> - `columns[].modifier_options: any` - значение модификатора столбца (`charset: string`, `collation: string`, `comment: string`, `default: any`, `nullable: boolean|null`, `unsigned: null`)<br>

- Response:

success:
```json
{
    "status": true,
    "operation": "store"
}
```

error:
```json
{
    "status": false,
    "error": "Not created table `table_insert`, this table exist",
    "data": ["other detail error"]
}
```

#### Обновить таблицу в Базе данных

- Request: PATCH `/api/v1/construct/{table_for_update}`;

Request data:
```json
{
    "table": "table_for_update",
    "new_name": "table_for_update_new_name",
    "columns": [
        {
            "name": "col_name_2",
            "type": "double",
            "options": [6, 2],
            "modifier": "default",
            "modifier_options": "default value",
            "patch": {
                "action": "rename",
                "new_name": "col_name_2_new_name"
            }
        }, {
            "name": "col_name_3",
            "type": "dateTime",
            "modifier": "nullable",
            "patch": {
                "action": "change"
            }
        }, {
            "name": "new_col",
            "type": "json",
            "patch": {
                "action": "push"
            }
        }
    ]
}
```

Description:
> - `table: string` - см. "Создать таблицу в Базе данных"<br>
> - `new_name: string` - новое имя таблицы, (обратите внимание: этот параметр имеет приритет в сравнении с параметром `columns`, `columns` будет проигнорирован)<br>
> - `columns: array` - см. "Создать таблицу в Базе данных"<br>
> - `columns[].name: string` - см. "Создать таблицу в Базе данных"<br>
> - `columns[].type: string` - см. "Создать таблицу в Базе данных"<br>
> - `columns[].options: any` - см. "Создать таблицу в Базе данных"<br>
> - `columns[].modifier: string` - см. "Создать таблицу в Базе данных"<br>
> - `columns[].modifier_options: any` - см. "Создать таблицу в Базе данных"<br>
> - `columns[].patch: object` - действия при обновлении таблицы<br>
> - `columns[].patch.action: string` - тип действи (`push`, `change`, `drop`, `rename`)<br>
> - `columns[].patch.new_name: string` - только для action rename, новое имя столбца<br>

- Response:

success:
```json
{
    "status": true,
    "operation": "update"
}
```

error:
```json
{
    "status": false,
    "error": "Not updated table `new_table2_11_new`, this table not exist",
    "data": ["other detail error"]
}
```

#### Удалить таблицу в Базе данных

- Request: DELETE `/api/v1/construct/{table_for_update}`;

- Response:

success:
```json
{
    "status": true,
    "operation": "destroy"
}
```

error:
```json
{
    "status": false,
    "error": "error",
    "data": ["other detail error"]
}
```

## Работа с данными Базы данных

Для простой работы с данными Базы данных существует отдельная библиотека [oosor/client-php-query](https://gitlab.com/api-db/client-php-query)

#### Получение данных Базы данных

- Request: POST `/api/v1/query`;

Request data (сложный пример для описания):
```json
{
    "query": "select",
    "table": "table_name_1",
    "columns": ["*"],
    "with": [
        {
            "type": "leftJoin",
            "table": "relation_table",
            "foreign_key": "first_table_col",
            "other_key": "relation_table_col"
        }, {
            "type": "leftJoin",
            "table": "relation_table_2",
            "foreign_key": "first_table_col_2",
            "other_key": "relation_table_col_2",
            "closure": [
                {
                    "type": "where",
                    "column": "relation_table_2_id",
                    "value": "value"
                }, {
                    "type": "whereDate",
                    "column": "date_col",
                    "is": "<",
                    "value": "2010-01-02"
                }
            ]
        }
    ],
    "where": [
        {
            "type": "where",
            "closure": [
                {
                    "type": "where",
                    "column": "text_col",
                    "is": "=",
                    "value": "value_text"
                }, {
                    "type": "whereDate",
                    "column": "col_date",
                    "value": "2012-12-05"
                }, {
                "type": "where",
                    "closure": [
                        {
                            "type": "orWhere",
                            "column": "col_or_where",
                            "value": "value_33"
                        }, {
                            "type": "orWhere",
                            "closure": [
                                {
                                    "type": "orWhereIn",
                                    "column": "col_in",
                                    "value": [1, 3, 5]
                                }, {
                                    "type": "whereDate",
                                    "column": "date_col",
                                    "is": ">",
                                    "value": "2018-02-19"
                                }
                            ]
                        }
                    ]
                }, {
                    "type": "whereNotNull",
                    "column": "not_null_col"
                }
            ]
        }, {
            "type": "whereNull",
            "column": "be_null_col"
        }
    ],
    "order": ["date_col", "DESC"],
    "limit": [5, 10]
}
```

Description:
> - `query: string` - **(required)** тип запроса. `select` - получение данных с БД<br>
> - `table: string` - **(required)** название запрашиваемой таблицы в БД<br>
> - `columns: string[]` - массив названий колонок (столбцов, полей) таблицы, которые хотите извлечь (столбец идентификатора `id` всегда присутствует по умолчанию (хотите вы того или нет), отсутствие этого поля равнозначно что вы указали единственный параметр `*` - извлекает все поля)<br>
> - `with: array` - масив обьектов для запроса связаных таблиц<br>
> - `with[].type: string` - **(required)** тип отношения (пока доступен только `leftJoin` - этого вполне достаточно 90% случаев, подразумевает связь SQL `LEFT JOIN`)<br>
> - `with[].table: string` - **(required)** название таблицы с которой нужно связать основную таблицу<br>
> - `with[].foreign_key: string` - **(required)** поле (столбец) для связи в главной таблице (один ко многим - обычно поле `id`)<br>
> - `with[].other_key: string` - **(required)** поле (столбец) для связи в таблице которую хотите привязать<br>
> - `with[].other_key: string` - **(required)** поле (столбец) для связи в таблице которую хотите привязать<br>
> - `with[].closure: array` - массив дополнительных условий (фильтра) связываемых таблиц<br>
> - `with[].closure[].type: string` - см. ниже обьекты параметра `where`<br>
> - `with[].closure[].column: string` - см. ниже обьекты параметра `where`<br>
> - `with[].closure[].is: string` - см. ниже обьекты параметра `where`<br>
> - `with[].closure[].value: any` - см. ниже обьекты параметра `where`<br>
> - `with[].closure[].closure: array` - см. ниже обьекты параметра `where` (даный запрос можно рекурсивно углублять до бесконечности)<br>
> - `where: array` - массив дополнительных условий (фильтра)<br>
> - `where[].type: string` - **(required)** тип условия (поддерживается `where`, `orWhere`, `whereIn`, `orWhereIn`, `whereNull`, `whereNotNull`, `whereDate`)<br>
> - `where[].column: string` - **(required)** названия столбца к которому применяется условие<br>
> - `where[].is: string` - оператор логической операции над значением столбца, по умолчанию `=` (`=`, `<`, `>`, `<=`, `>=`, `<>`, `!=`, `<=>`, `like`, `like binary`, `not like`, `ilike`, `&`, `|`, `^`, `<<`, `>>`, `rlike`, `regexp`, `not regexp`, `~`, `~*`, `!~`, `!~*`, `similar to`, `not similar to`, `not ilike`, `~~*`, `!~~*`)<br>
> - `where[].value: any` - значение для сравнения (обязательное для type `where`, `orWhere`, `whereIn`, `orWhereIn`, `whereDate`)<br>
> - `where[].closure: array` - массив вложенных условий (содержит тоже что where), (даный запрос можно рекурсивно углублять до бесконечности), (обратите внимание что при использовании параметра `closure` соседние параметры (`column`, `is`, `value`) будут проигнорированы, а будут использоваться вложенные условия в `closure`)<br>
> - `order: string[]` - массив с двух (или одного) элементов 1. - название столбца, 2. - (опционально) тип сортировки (только `ASC` (по умолчанию) и `DESC`)<br>
> - `limit: integer[]` - массив с двух (или одного) элементов 1. - если в массиве один элемент -> количество елементов сколько надо извлечь начиная с первого элемента; если в массиве два елемента -> количество элементов сколько нужно пропустить, 2. - (опционально) количество елементов сколько надо извлечь после пропущеных элементов<br>

- Response:

success:
```json
{
    "status": true,
    "operation": "select",
    "data": [
        {
            "id": 4,
            "name": "name1",
            "new_name": "default_data val",
            "resource": "resource12",
            "structure": "data text 1",
            "info": "2019-07-11",
            "user_id": 1,
            "relation_table": [
                {
                    "id": 3,
                    "status": "ok",
                    "renamed3_id": 4,
                    "data": "data value update1"
                }, {
                    "id": 4,
                    "status": "no",
                    "renamed3_id": 4,
                    "data": "data value update2"
                }, {
                    "id": 6,
                    "status": "no",
                    "renamed3_id": 4,
                    "data": "data value 3"
                }
            ],
            "relation_table_2": []
        }, {
            "id": 5,
            "name": "name 2",
            "new_name": "def data",
            "resource": "structure data",
            "structure": "any",
            "info": "2015-07-12",
            "user_id": 3,
            "relation_table": [
                {
                    "id": 5,
                    "status": "ok",
                    "renamed3_id": 5,
                    "data": "data value 2"
                }
            ],
            "relation_table_2": []
        }, {
            "id": 3,
            "name": "name3",
            "new_name": "default_data3",
            "resource": "resource1",
            "structure": "more text",
            "info": "2019-07-11",
            "user_id": 0,
            "relation_table": [],
            "relation_table_2": []
        }, {
            "id": 6,
            "name": "name4",
            "new_name": "xyz",
            "resource": "res data",
            "structure": "pattern",
            "info": "2015-07-12",
            "user_id": 3,
            "relation_table": [],
            "relation_table_2": [
                {
                    "id": 4,
                    "status": false
                }
            ]
        }
    ]
}
```

error:
```json
{
    "status": false,
    "error": "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'table_name_1.ids' in 'where clause' ....",
    "data": ["other detail error"]
}
```

#### Добавление данных в Базу данных

- Request: POST `/api/v1/query`;

Request data:
```json
{
    "query": "insert",
    "table": "table_name",
    "data": [
        {
            "name": "name1",
            "new_name": "surname",
            "resource": "res data1",
            "structure": "facade",
            "info": "2011-12-02",
            "user_id": "3"
        }, {
            "name": "name2",
            "new_name": "surname2",
            "resource": "res data2",
            "structure": "prototype",
            "info": "2011-04-02",
            "user_id": "6"
        }, {
            "name": "name3",
            "new_name": "surname3",
            "resource": "res data3",
            "structure": "abstract",
            "info": "2011-07-02",
            "user_id": "12"
        }
    ]
}
```

Description:
> - `query: string` - **(required)** тип запроса. `insert` - добавить новые данные в БД<br>
> - `table: string` - **(required)** название таблицы в БД куда добавляются данные<br>
> - `data: array` - **(required)** массив обьектов данных (соответстующих структуре таблицы в БД) которые нужно добавить<br>

- Response:

success:
```json
{
    "status": true,
    "operation": "insert",
    "data": {
        "inserting": true
    }
}
```

error:
```json
{
    "status": false,
    "error": "SQLSTATE[42S02]: Base table or view not found: 1146 Table 'renamed_table2_not_found' doesn't exist ...",
    "data": ["other detail error"]
}
```

#### Обновление данных в Базе данных

- Request: POST `/api/v1/query`;

Request data:
```json
{
    "query": "update",
    "table": "table_name",
    "where": [
        {
            "type": "whereIn",
            "column": "id",
            "value": [3, 4, 7]
        }
    ],
    "data": {
        "name": "name1",
        "info": "2011-12-02"
    }
}
```

Description:
> - `query: string` - **(required)** тип запроса. `update` - обновить данные в БД<br>
> - `table: string` - **(required)** название таблицы в БД в которой обновлятся данные<br>
> - `data: object` - **(required)** обьект данных (соответстующих структуре таблицы в БД) которые нужно обновить<br>
> - `where: array` - см. описание этого параметра "Получение данных Базы данных"<br>

- Response:

success:
```json
{
    "status": true,
    "operation": "update",
    "data": {
        "updated_rows": 3
    }
}
```

error:
```json
{
    "status": false,
    "error": "error",
    "data": ["other detail error"]
}
```

#### Удаление данных в Базе данных

- Request: POST `/api/v1/query`;

Request data:
```json
{
    "query": "delete",
    "table": "table_name",
    "where": [
        {
            "type": "whereNull",
            "column": "structure"
        }
    ]
}
```

Description:
> - `query: string` - **(required)** тип запроса. `delete` - удалить данные в БД<br>
> - `table: string` - **(required)** название таблицы в БД в которой удалить данные<br>
> - `where: array` - см. описание этого параметра "Получение данных Базы данных"<br>

- Response:

success:
```json
{
    "status": true,
    "operation": "delete",
    "data": {
        "deleted_rows": 2
    }
}
```

error:
```json
{
    "status": false,
    "error": "error",
    "data": ["other detail error"]
}
```

## Авторизация в сервисе

Для простой работы с авторизацией и получением токенов существует отдельная библиотека [oosor/client-php-auth](https://gitlab.com/api-db/client-php-auth)

В сервере настроен механизм сильной безопасности с помощью OAuth2 (Passport API) на токенах Bearer.
Все запросы в сервер должны сопровождаться заголовками `Accept: application/json` (для правильной работы API OAuth) и 
`Authorization: Bearer {access_token}` (для самой авторизации в системе)

Есть несколько способов получения токенов доступа. Все они описаны в официальной документации на [Passport API](https://laravel.com/docs/5.8/passport) 

Для работы в сервисе используются такие типы токенов доступа:
- **Password Grant Tokens** - этот тип токена рекомендуется использовать только для создания других токенов доступа и (возможно) для построения таблиц в Базе данных;
- **Client Credentials Grant Tokens** - только для запросов для работы с данными в Базе данных, (не имеет доступа для построения таблиц в БД);
- **Personal Access Tokens** - для клиентских запросов в Базу данных (в некоторых условиях может также управлять таблицами в БД);

##### Scopes

- **show** - для запросов типа `select` 
- **store** - для запросов типа `insert` 
- **update** - для запросов типа `update` 
- **delete** - для запросов типа `delete` 
- **construct** - для запросов в конструктор таблиц в БД

Перед выполнением запросов токены доступа должны получить определенные `scopes` для работы с определенными запросами
- Для работы с конструктором таблиц токен доступа должен иметь один из scopes `construct` (только Password Grant Tokens и Personal Access Tokens)
- Для выполнения запросов в таблицы с данными (`show`, `store`, `update`, `delete`) (scope `construct` имеет в себе все четыре предыдущие scopes, может смотреть, создавать, обновлять и удалять данные в БД)

Токены без scopes ничего не могут делать.

## +++

При переходе по ссылке API сервера Базы данных можна увидеть Dashboard и зарегистрироваться и (или) 
залогиниться в базе данных где возможно создавать (удалять) клиентов доступа. Смотреть активных
клиентов (приложений) по токенам и при возможности отменять все доступы токенам. Создавать и удалять
токены доступа уровня `Personal Access Tokens`

## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
