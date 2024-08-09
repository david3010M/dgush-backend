<?php

namespace App\Traits;

use App\Utils\Constants;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    protected function applyFilters($query, $request, $filters)
    {
        foreach ($filters as $filter => $operator) {
            $paramName = str_replace('.', '_', $filter);
            $value = $request->query($paramName);

            if ($value !== null) {
                if (strpos($filter, '.') !== false) {
                    [$relation, $relationFilter] = explode('.', $filter);

                    $query->whereHas($relation, function ($q) use ($relationFilter, $operator, $value) {
                        $this->applyFilterCondition($q, $relationFilter, $operator, $value);
                    });
                } else {
                    $this->applyFilterCondition($query, $filter, $operator, $value);
                }
            }
        }

        return $query;
    }

    protected function applyFilterCondition($query, $filter, $operator, $value)
    {
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
                break;
        }
    }

    protected function applySorting($query, $request, $sorts)
    {
        $sortField = $request->query('sort');
        $sortOrder = $request->query('direction', 'desc');

        if ($sortField !== null && in_array($sortField, $sorts)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query;
    }

    protected function getFilteredResults($modelOrQuery, $request, $filters, $sorts, $resource)
    {
        if ($modelOrQuery instanceof Builder) {
            $query = $modelOrQuery;
        } else {
            $query = $modelOrQuery::query();
        }

        $query = $this->applyFilters($query, $request, $filters);
        $query = $this->applySorting($query, $request, $sorts);

        $all = $request->query('all', false) === 'true';
        $results = $all ? $query->get() : $query->paginate($request->query('per_page', Constants::DEFAULT_PER_PAGE));

        return $all ? response()->json($resource::collection($results)) : $resource::collection($results);
    }
}
