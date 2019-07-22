# Библиотека для работы с сервером API Базы данных (конструктор запросов для работы с данными в БД)

Сервер [API Базы данных](https://gitlab.com/api-db/server)

Библиотека возлагает на себя построение сложных запросов по работе с данными в Базе данных и предоставляет
простой интерфейс конструктора запросов.

## Возможности

1. Получение данных с Базы данных
2. Добавление данных в Базу данных.
3. Обновления данных в Базе данных.
4. Удаления данных в Базе данных.

## Установка 

в вашем приложении в `composer.json` добавте:
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://gitlab.com/api-db/client-php-query"
    }
  ],
  "require": {
    "oosor/client-php-query": "~1.0.0"
  }
}
```

запустите `composer update`

## Получение данных с Базы данных

```php
$select = new \Oosor\ClientQuery\SelectBuilder('table_name_1');

$select
    ->columns(['*'])
    ->with('relation_table', 'first_table_col', 'relation_table_col')
    ->with('relation_table_2', 'first_table_col_2', 'relation_table_col_2', function (\Oosor\ClientQuery\Models\Build $build) {

        $build
            ->where('relation_table_2_id', 'value')
            ->whereDate('date_col', '<', '2010-01-02');

    })
    ->where(function (\Oosor\ClientQuery\Models\Build $build) {

        $build
            ->where('text_col', '=', 'value_text')
            ->whereDate('col_date', '2012-12-05')
            ->where(function (\Oosor\ClientQuery\Models\Build $build) {

                $build
                    ->orWhere('col_or_where', 'value_33')
                    ->orWhere(function (\Oosor\ClientQuery\Models\Build $build) {

                        $build
                            ->orWhereIn('col_in', [1, 3, 5])
                            ->whereDate('date_col', '>', '2018-02-19');

                    });

            })
            ->whereNotNull('not_null_col');

    })
    ->whereNull('be_null_col')
    ->orderDesc('date_col')
    ->limit(5, 10);

$requestData = $select->getResult();
```

Description:
> Все методы класса `\Oosor\ClientQuery\SelectBuilder` простые описаны в самом классе (Очень похожие [Database: Query Builder](https://laravel.com/docs/5.8/queries))<br>
Класс `\Oosor\ClientConstruct\Models\Build` для построения вложенных запросов `with` и `where`


## Добавление данных в Базу данных

```php
$insert = new \Oosor\ClientQuery\InsertBuilder('table_name_2');

$insert
    ->data([
        ['col_1' => 'val_1', 'col_2' => 'val_2', 'col_3' => 'val_3'],
        ['col_1' => 'val_1_1', 'col_2' => 'val_2_2', 'col_3' => 'val_3_3'],
    ])
    ->pushData(['col_1' => 'val_1_pushed', 'col_2' => 'val_2_pushed', 'col_3' => 'val_3_pushed']);

$requestData = $insert->getResult();
```

Description:
> Все методы класса `\Oosor\ClientQuery\InsertBuilder` простые описаны в самом классе<br>


## Обновления данных в Базе данных

```php
$update = new \Oosor\ClientQuery\UpdateBuilder('table_name_3');

$update
    ->data(['col_1' => 'val_1_new'])
    ->whereIn('col_2', ['val_2', 'val_2_pushed']);

$requestData = $update->getResult();
```

Description:
> Все методы класса `\Oosor\ClientQuery\UpdateBuilder` простые описаны в самом классе<br>
Класс `\Oosor\ClientConstruct\Models\Build` для построения вложенных запросов `where`


## Удаления данных в Базе данных

```php
$delete = new \Oosor\ClientQuery\DeleteBuilder('table_name_4');

$delete
    ->where('id', '>', 5);

$requestData = $delete->getResult();
```

Description:
> Все методы класса `\Oosor\ClientQuery\DeleteBuilder` простые описаны в самом классе<br>
Класс `\Oosor\ClientConstruct\Models\Build` для построения вложенных запросов `where`
