# Библиотека для работы с сервером API Базы данных (конструктор запросов для управления таблицами в БД)

Сервер [API Базы данных](https://gitlab.com/api-db/server)

Библиотека возлагает на себя построение сложных запросов по управлению таблицами в Базе данных и предоставляет
простой интерфейс конструктора.

## Возможности

1. Создание таблицы в Базе данных
2. Обновление таблицы в Базе данных.

Просмотр и удаление таблиц не предоставляется так как он из API Базы данных уже простой в использовании

## Установка 

в вашем приложении в `composer.json` добавте:
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://gitlab.com/api-db/client-php"
    }
  ],
  "require": {
    "oosor/client-php-construct": "~1.0.0"
  }
}
```

запустите `composer update`

## Создание таблиц

```php
$insert = new \Oosor\ClientConstruct\InsertBuilder('table_insert');

$insert
    ->addColumn('col_name_1', function (\Oosor\ClientConstruct\Models\Build $build) {
        $build->bigIncrements();
    })
    ->addColumn('col_name_2', function (\Oosor\ClientConstruct\Models\Build $build) {
        $build->string(255)->default('default value');
    })
    ->addColumn('col_name_3', function (\Oosor\ClientConstruct\Models\Build $build) {
        $build->date()->nullable();
    });

$requestData = $insert->getResult();
```

Description:
> Обьект класса `\Oosor\ClientConstruct\InsertBuilder` принимает в параметр конструктора название новой таблицы

Методы **\Oosor\ClientConstruct\InsertBuilder**:
- `setTableName(string $name): $this` -> устанавливает имя таблицы (перезаписывает значение переданное в конструкторе)
- `addColumn(string $columnName, \Closure $callback): $this` -> `$columnName` - название столбца, `$callback` - функция обратного вызова
принимает параметр `\Oosor\ClientConstruct\Models\Build $build`
- `getResult(): array` -> возвращает массив данных готовых к отправке на Сервер API Базы данных

Класс `\Oosor\ClientConstruct\Models\Build` для построения структуры столбца
В классе `\Oosor\ClientConstruct\Models\Build` методов очень много. Все их можно посмотреть в самом класе,
все довольно просто


## Обновление таблиц

```php
$update = new \Oosor\ClientConstruct\UpdateBuilder('table_update');

$update
    ->addColumn('col_name_4', function (\Oosor\ClientConstruct\Models\Build $build) {
        $build->json()->pushPatch();
    })
    ->addColumn('col_name_5', function (\Oosor\ClientConstruct\Models\Build $build) {
        $build->date()->default('2019-07-12')->changePatch();
    })
    ->addColumn('col_name_2', function (\Oosor\ClientConstruct\Models\Build $build) {
        $build->string(255)->dropPatch();
    })
    ->addColumn('col_name_3', function (\Oosor\ClientConstruct\Models\Build $build) {
        $build->date(255)->renamePatch('col_new_name_3');
    });

$requestData = $update->getResult();
```

```php
$updateTableName = new \Oosor\ClientConstruct\UpdateBuilder('table_update_name');

$updateTableName->setTableNewName('table_update_new_name');

$requestData = $updateTableName->getResult();
```

Description:
> Обьект класса `\Oosor\ClientConstruct\UpdateBuilder` принимает в параметр конструктора название новой таблицы

Методы **\Oosor\ClientConstruct\UpdateBuilder**:
- `setTableName(string $name): $this` -> устанавливает имя таблицы (перезаписывает значение переданное в конструкторе)
- `setTableNewName(string $name): $this` -> устанавливает значение для установления нового имени таблицы в БД 
- `addColumn(string $columnName, \Closure $callback): $this` -> `$columnName` - название столбца, `$callback` - функция обратного вызова
принимает параметр `\Oosor\ClientConstruct\Models\Build $build`
- `getResult(): array` -> возвращает массив данных готовых к отправке на Сервер API Базы данных

Класс `\Oosor\ClientConstruct\Models\Build` для построения структуры столбца
В классе `\Oosor\ClientConstruct\Models\Build` методов очень много. Все их можно посмотреть в самом класе,
все довольно просто