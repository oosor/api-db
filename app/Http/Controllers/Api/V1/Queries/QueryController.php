<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 10.07.19
 * Time: 21:26
 */

namespace App\Http\Controllers\Api\V1\Queries;


use App\Http\Controllers\Api\V1\Queries\Core\{
    Optimization, QueryBuilder
};
use App\Http\Controllers\Api\V1\Queries\Exceptions\ScopesException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Queries\QueryResource;
use Illuminate\Http\Request;
use League\OAuth2\Server\ResourceServer;

class QueryController extends Controller
{
    use ControllersHelper;

    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }


    public function index(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string',
                'table' => 'required|string',
                'columns' => 'array|nullable',
                'with' => 'array|nullable',
                'where' => 'array|nullable',
                'order' => 'array|nullable',
                'limit' => 'array|nullable',
                'data' => 'array|nullable',
            ]);

            if (!$this->validateScope($request, $this->getScopeMethod($request->input('query')))) {
                throw new ScopesException('Scope permission denied');
            }

            $builder = new QueryBuilder($request->all());
            if ($builder->isValid()) {
                $optimizationData = new Optimization($builder);
                $data = $optimizationData->getData();

                return (new QueryResource($data))
                    ->additional($this->getSuccessAdditional($data['status'], $data['operation']));
            }

            return $this->rejected($builder->getMessage());
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());
            return $this->rejected($exception->getMessage());
        }
    }
}
