<?php
/**
 * Created by IntelliJ IDEA.
 * User: jarvis
 * Date: 12.07.19
 * Time: 9:54
 */

namespace App\Http\Controllers\Api\V1\Queries\Core\Optimization;


use Illuminate\Support\Collection;

trait OptimizeWith
{

    /**
     * @param Collection $collection
     * @return Collection
     * */
    protected function optimizeRelationship($collection)
    {
        $queryData = $this->builder->getQueryData();
        if (isset($queryData['with'])) {
            $withCollect = array_map(function ($item) {
                return $item['table'];
            }, $queryData['with']);

            $resultData = $collection->map(function ($item) use ($withCollect) {
                $newItem = [];

                foreach ($item as $key => $value) {
                    if ($this->fieldIsForWith($key, $withCollect, $matches)) {
                        $this->injectCollectionToDataKey($newItem, $matches, $value);
                    } else {
                        $newItem[$key] = $value;
                    }
                }

                return $newItem;
            });

            return $resultData->reduce(function (Collection $result, $item) {
                return $this->optimizeInjectCollection($result, $item);
            }, new Collection());
        }

        return $collection;
    }

    /** is relation data key
     * @param string $key
     * @param array $withCollect
     * @param mixed $matches
     * @return bool
     * */
    protected function fieldIsForWith(string $key, array $withCollect, &$matches)
    {
        return preg_match('/^(' . join('|', $withCollect) . ')\.(\w+)/', $key, $matches);
    }

    /** inject relation data in key table->data
     * @param array $newItem
     * @param array $matches
     * @param mixed $value
     * */
    protected function injectCollectionToDataKey(&$newItem, $matches, $value)
    {
        [, $table, $key] = $matches;

        if (!isset($newItem[$table])) {
            $newItem[$table] = new Collection([]);
        }

        $item = $newItem[$table]->shift();
        $item[$key] = $value;
        $newItem[$table]->add($item);
    }


    /**
     * @param Collection $collect
     * @param int $id
     * @return bool
     * */
    protected function hasInCollectForId($collect, $id)
    {
        return $collect->where('id', $id)->isNotEmpty();
    }

    /** optimization
     * @param Collection $collect
     * @param array $item
     * @return Collection
     * */
    protected function optimizeInjectCollection($collect, $item)
    {
        $this->clearIfNullDataInCollect($item);
        if ($this->hasInCollectForId($collect, $item['id'])) {
            $collect = $this->mergeEqualsCollect($collect, $item);
        } else {
            $collect->add($item);
        }

        return $collect;
    }

    /** clear data in collect if values is NULL
     * @param array $item
     * */
    protected function clearIfNullDataInCollect(&$item)
    {
        foreach ($item as $key => &$value) {
            if ($value instanceof Collection) {
                $first = $value->first();
                if (is_null($first['id'])) {
                    $value->shift();
                }
            }
        }
    }

    /** merging relation data
     * @param Collection $collect
     * @param array $item
     * @return Collection
     * */
    protected function mergeEqualsCollect($collect, $item)
    {
        // get element by id
        $one = $collect->first(function ($one) use ($item) {
            return $one['id'] == $item['id'];
        });

        if (!empty($one)) {
            foreach ($item as $key => $value) {
                if ($value instanceof Collection) {
                    // not duplicate
                    if (!$this->hasInCollectForId($one[$key], $value->first()['id'])) {
                        // merge collection
                        $one[$key] = $one[$key]->merge($value);
                    }
                }
            }

            // return new collection
            return $collect->map(function ($item) use ($one) {
                return $item['id'] == $one['id'] ? $one : $item;
            });
        }

        // not changed collection
        return $collect;
    }
}
