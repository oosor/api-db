<?php

namespace App\Http\Controllers\Api\V1\Construct;


use App\Http\Controllers\Api\V1\Construct\Core\Migration;
use App\Http\Controllers\Api\V1\Construct\Models\Universal;
use App\Http\Resources\Construct\MigrationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConstructController extends Controller
{
    use ControllersHelper;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        $collect = Migration::listTables();
        if (isset($collect)) {
            return MigrationResource::collection($collect)
                ->additional($this->getSuccessAdditional('index'));
        }

        return $this->rejected('List tables is null');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'table' => 'required|string',
                'columns' => 'required|array',
            ]);

            $model = new Universal([
                'table' => $request->input('table'),
                'columns' => $request->input('columns'),
            ]);

            if ($model->isValid()) {
                $migration = new Migration($model);
                $migration->up();
                return $this->getSuccessAdditional('store');
            }

            return $this->rejected('Data not valid', $model->getMessage());
        } catch (\Exception $validationException) {
            logger()->alert($validationException->getMessage());
            return $this->rejected($validationException->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($name)
    {
        $detail = Migration::table($name);
        if (isset($detail)) {
            return (new MigrationResource($detail))->additional($this->getSuccessAdditional('show'));
        }

        return $this->rejected('Table for name `' . $name . '` not found');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $name)
    {
        try {
            $request->validate([
                'table' => 'required|string',
                'new_name' => 'string|nullable',
                'columns' => 'array|nullable',
            ]);

            $model = new Universal([
                'table' => $request->input('table'),
                'new_name' => $request->input('new_name') ?? null,
                'columns' => $request->input('columns') ?? null,
            ]);

            if ($model->isValid()) {
                $migration = new Migration($model);
                $newName = $request->input('new_name');
                if (isset($newName)) {
                    $migration->rename();
                } else {
                    $migration->patch();
                }
                return $this->getSuccessAdditional('update');
            }

            return $this->rejected('Data not valid', $model->getMessage());
        } catch (\Exception $validationException) {
            logger()->alert($validationException->getMessage());
            return $this->rejected($validationException->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function destroy($name)
    {
        if (is_string($name)) {
            Migration::down($name);
            return $this->getSuccessAdditional('destroy');
        }
        return $this->rejected('Table name does\'t valid');
    }
}
