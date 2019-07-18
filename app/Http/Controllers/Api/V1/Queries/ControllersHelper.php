<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 19:07
 */

namespace App\Http\Controllers\Api\V1\Queries;


use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

trait ControllersHelper
{
    /**
     * @var \League\OAuth2\Server\ResourceServer $server
     * */
    private $server;
    protected $superScopes = ['*', 'construct', 'superuser'];

    /** is scope
     * @param \Illuminate\Http\Request $request
     * @param string $need
     * @return bool
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     * */
    protected function validateScope($request, $need)
    {
        $psr17Factory = new Psr17Factory();
        $psr = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psr = $psr->createRequest($request);
        $psr = $this->server->validateAuthenticatedRequest($psr);
        $tokenScopes = $psr->getAttribute('oauth_scopes') ?? [];

        $super = array_filter($tokenScopes, function ($scope) {
            return in_array($scope, $this->superScopes);
        });

        if (empty($super)) {
            return in_array($need, $tokenScopes);
        }

        return true;
    }

    /** get scope for method
     * @param string $method
     * @return bool
     * */
    protected function getScopeMethod($method)
    {
        switch (strtoupper($method)) {
            case 'SELECT':
                return 'show';
            case 'INSERT':
                return 'store';
            case 'UPDATE':
                return 'update';
            case 'DELETE':
                return 'delete';
        }

        return null;
    }

    /** rejected resource
     * @param string $message
     * @param mixed $data
     * @return array
     * */
    protected function rejected($message, $data = null)
    {
        return [
            'status' => false,
            'error' => $message,
            'data' => $data ?? [],
        ];
    }

    /** with resource
     * @param bool $status
     * @param string $type
     * @return array
     * */
    protected function getSuccessAdditional($status, $type)
    {
        return [
            'status' => $status,
            'operation' => $type,
        ];
    }
}
