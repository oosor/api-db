# Библиотека для работы с сервером API Базы данных (авторизация в сервисе и получение токенов доступа)

Сервер [API Базы данных](https://gitlab.com/api-db/server)

Библиотека возлагает на себя построение сложных запросов по работе с авторизацией и получение токенов доступа, предоставляет
простой интерфейс.

## Возможности

1. Получение токенов типа **Password Grant Tokens**
2. Получение токенов типа **Client Credentials Grant Tokens**
4. Получение токенов типа **Personal Access Tokens**

## Установка 

в вашем приложении в `composer.json` добавте:
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://gitlab.com/api-db/client-php-auth"
    }
  ],
  "require": {
    "oosor/client-php-auth": "~1.0.0"
  }
}
```

запустите `composer update`

## Подготовка к работе

Имеется единственный класс `\Oosor\AuthApiDatabase\Auth` для получения любого токена из выше перечисленных.
В конструктор которого передается обьект который реализует интерфейс `Oosor\AuthApiDatabase\Contracts\Configuration`

> - Для получения `Password Grant Tokens` `ВашКласс implements Oosor\AuthApiDatabase\Contracts\Configuration`
> в методах `getClientId()`, `getClientSecret()`, `userName()`, `userPassword()` должны возвращаться соответствующие значения.
> Остальные методы могут возвращать `null`.<br>
> - Для получения `Client Credentials Grant Tokens` `ВашКласс implements Oosor\AuthApiDatabase\Contracts\Configuration`
> в методах `getClientId()`, `getClientSecret()` должны возвращаться соответствующие значения.
> Остальные методы могут возвращать `null`.<br>
> - Для получения `Personal Access Tokens` `ВашКласс implements Oosor\AuthApiDatabase\Contracts\Configuration`
> в методе `accessToken()` должен возвращать токен доступа уровня `Password Grant Tokens`.
> Остальные методы могут возвращать `null`.

Пример полностью заполненого класса конфигурации (как описано выше: для каждого типа токена есть свои обязательные поля):
```php
class Config implements \Oosor\AuthApiDatabase\Contracts\Configuration {
    public function getClientId()
    {
        return 2;
    }

    public function getClientSecret()
    {
        return 'a0ac55idyWvsM4k77fhNBtweZJosmP6X10FafEE5';
    }

    public function userName()
    {
        return 'andrew@google.com';
    }

    public function userPassword()
    {
        return 'secretpa$$w0rd';
    }

    public function accessToken()
    {
        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijg0NmQ3YTJjZWFmNjliOTU3NDkzMmVhYWJmZjk2NjZhYjZhMTYxMWMyZmI5YmJlM2UzODlkYzE2ZmM5NWIzMDlmMzk0NGRlMzY0MTI0Yjk2In0.eyJhdWQiOiIyIiwianRpIjoiODQ2ZDdhMmNlYWY2OWI5NTc0OTMyZWFhYmZmOTY2NmFiNmExNjExYzJmYjliYmUzZTM4OWRjMTZmYzk1YjMwOWYzOTQ0ZGUzNjQxMjRiOTYiLCJpYXQiOjE1NjM2MzE4OTEsIm5iZiI6MTU2MzYzMTg5MSwiZXhwIjoxNTk1MjU0MjkxLCJzdWIiOiIxIiwic2NvcGVzIjpbImNvbnN0cnVjdCJdfQ.gggBkqiThcCYQXOow8KJYp-BV4LI5cJzVzI65MTthdCCFWN9fgAce6PNXaVkiqPXOEvFzemZ9Vr-MH45VcqbXHucDrIOGgsmtDbA0ggCP0Sk-nu6CGYnAEC_zR6ewLowQxnDkpJ6HMAiqYTGL-fSwOleTGYlIh8CErT22Dh8qAskfdW0SDrK7z42nA2fojLMXYiO1z-vRI54_lwtb0pHP4tcMLXJlUr3eE_ztjfksNIhBt9OPbbf1KGPy3kzEL4W18l5c0FyGzvhY8PxeRzclB4X7LvlliH28hSw5nACL7Pbc0c0To1jH-1WsrNJQ_7DGP8-uK4qMufeEcSV01IkndaUyMlB06O1sPlc2_pdi6Rnxm6f9oVtQ-i4k8SvexG0lh6KTtraOWJXOmLZTwmszHVzeqqui6WBxruKabRmXPh2xV2cIa8SAxP5MqxLv-UEVGnl0cNtS9VEc-tJ3uqHxS9_x8PFanllyK1XQsKqq3UFD96LT05em3chahYnu2nFTWSx3tvF4vRMYfArJOCU7QjfBtkiOA7WOdLX7zw67Rc13Bg1tZnNkFuNLWjqpjtQog9PIaDIMvnZ_z9s6rIwpgE1yp-3vza6Y1Gm72FD8SyDpxov3U2vK1uostfB7pJUUI8cI-DOss4ZYTAbquRSzO2ul2pjJnR5OpXAISPWvjs';
    }
}
```

## Получение Tokens

```php
$auth = new \Oosor\AuthApiDatabase\Auth('https://server-api-db', new ConfigClient());

// Password Grant Tokens
echo $auth
    ->passwordToken()
    ->addConstructScope()
    ->get();

// Client Credentials Grant Tokens
echo $auth
    ->clientToken()
    ->addUpdateScope()
    ->addDeleteScope()
    ->get();

// Personal Access Tokens
echo $auth
    ->personalToken('name_token')
    ->addInsertScope()
    ->addSelectScope()
    ->addUpdateScope()
    ->get();
```

Второй параметр конструктора является необязательным, но вы перед запросом токена должны установить конфиг
через метод `setConfig(Configuration $config)` класса `\Oosor\AuthApiDatabase\Auth`:
```php
$auth = new \Oosor\AuthApiDatabase\Auth('https://server-api-db');
$auth->setConfig(new Config());
```

Description:
> Класс `\Oosor\AuthApiDatabase\Auth` помимо метода `setConfig(Configuration $config)` имеет всего еще три 
метода `passwordToken()`, `clientToken()`, `personalToken(string $name)` для получения определенного токена.
Сами эти методы возвращают обьект одного из интерфейса `Oosor\AuthApiDatabase\Contracts\PasswordToken`, 
`Oosor\AuthApiDatabase\Contracts\ClientToken`, `Oosor\AuthApiDatabase\Contracts\PersonalToken` соответственно.
Каждый интерфейс разограничивает нужные scopes для каждого из типов токенов. Метод `get()` делает непосредственный
подготовленый с вашими параметрами запрос к серверу API Базы данных и в случае успеха возвращает токен доступа


Выходные данные:

Для простоты дальнейшего использования токенов возвращается обьект `Oosor\AuthApiDatabase\Models\Bearer`
который хранить в себе всю информацию о токене доступа:
- Для `Password Grant Tokens` будут заполнены методы:
 `tokenType()` (тип токена всегда Bearer),
 `accessToken()` (сам токен доступа),
 `refreshToken()` (токен обновления - для простого запроса нового токена доступа если старый скоро просрочится)
 `tokenExpiresIn()` (время жизни токена доступа)
- Для `Client Credentials Grant Tokens` будут заполнены методы:
 `tokenType()` (тип токена всегда Bearer),
 `accessToken()` (сам токен доступа),
 `tokenExpiresIn()` (время жизни токена доступа)
- Для `Personal Access Tokens` будут заполнены методы:
 `accessToken()` (сам токен доступа),
 `token()` (дополнительная информация о клиенте от имени которого создан этот токен)

## Важная информация 
**Чтобы не плодить токенов (типа один токен - один запрос) вызывая токен при каждом обращении в сервер API DB,
при получении данных токена ваше приложение должно уметь сохранить нужные вам данные токена где нибудь у себя в хранилище
и использовать токен повторно. По умолчанию время жизни токена 1 год (вполне достаточно). Так же не стоит ползоваться токеном уровня `Password Grant Tokens` для 
простых запросов работы с данными. К присеру, если ваше приложение не будет добавлять и (или) удалять данные в БД -
давайте минимальные права `scopes` (будете уверены что приложение не навредит данным в Базе данных)** 