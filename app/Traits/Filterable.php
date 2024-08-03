<?php

namespace App\Traits;

use App\Utils\Constants;

trait Filterable
{
    protected function applyFilters($query, $request, $filters)
    {
        foreach ($filters as $filter => $operator) {
            $value = $request->query($filter);

            if ($value !== null) {
                switch ($operator) {
                    case 'like':
                        $query->where($filter, 'like', '%' . $value . '%');
                        break;
                    case 'between':
                        if (is_array($value) && count($value) === 2) {
                            $query->whereBetween($filter, $value);
                        }
                        break;
                    case '>':
                        $query->where($filter, '>', $value);
                        break;
                    case '<':
                        $query->where($filter, '<', $value);
                        break;
                    case '>=':
                        $query->where($filter, '>=', $value);
                        break;
                    case '<=':
                        $query->where($filter, '<=', $value);
                        break;
                    case '=':
                        $query->where($filter, '=', $value);
                        break;
                    default:
                        // Maneja operadores adicionales si es necesario
                        break;
                }
            }
        }

        return $query;
    }

    protected function getFilteredResults($model, $request, $filters, $resource)
    {
        $query = $this->applyFilters($model::query(), $request, $filters);

        $all = $request->query('all', false) === 'true';
        $results = $all ? $query->get() : $query->paginate($request->query('per_page', Constants::DEFAULT_PER_PAGE));

        return $all ? response()->json($resource::collection($results)) : $resource::collection($results);
    }
}
