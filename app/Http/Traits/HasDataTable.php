<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasDataTable
{
    /**
     * Apply search filters to the query
     */
    protected function applySearch(Builder $query, Request $request, array $searchableColumns = []): Builder
    {
        $search = $request->get('search');
        
        if ($search && !empty($searchableColumns)) {
            $query->where(function ($q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    if (str_contains($column, '.')) {
                        // Handle relationship columns
                        $parts = explode('.', $column);
                        $relation = $parts[0];
                        $relationColumn = $parts[1];
                        
                        $q->orWhereHas($relation, function ($relationQuery) use ($search, $relationColumn) {
                            $relationQuery->where($relationColumn, 'like', "%{$search}%");
                        });
                    } else {
                        // Handle direct columns
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }
        
        return $query;
    }
    
    /**
     * Apply sorting to the query
     */
    protected function applySort(Builder $query, Request $request, array $sortableColumns = []): Builder
    {
        $sortColumn = $request->get('sort_column');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if ($sortColumn && in_array($sortColumn, $sortableColumns)) {
            if (str_contains($sortColumn, '.')) {
                // Handle relationship sorting
                $parts = explode('.', $sortColumn);
                $relation = $parts[0];
                $relationColumn = $parts[1];
                
                $query->leftJoin(
                    $relation,
                    $query->getModel()->getTable() . '.id',
                    '=',
                    $relation . '.id'
                )->orderBy($relation . '.' . $relationColumn, $sortDirection);
            } else {
                // Handle direct column sorting
                $query->orderBy($sortColumn, $sortDirection);
            }
        }
        
        return $query;
    }
    
    /**
     * Apply pagination to the query
     */
    protected function applyPagination(Builder $query, Request $request): LengthAwarePaginator
    {
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 15;
        
        return $query->paginate($perPage)
            ->appends($request->query());
    }
    
    /**
     * Process DataTable request
     */
    protected function processDataTableRequest(
        Builder $query,
        Request $request,
        array $searchableColumns = [],
        array $sortableColumns = []
    ): LengthAwarePaginator {
        $query = $this->applySearch($query, $request, $searchableColumns);
        $query = $this->applySort($query, $request, $sortableColumns);
        
        return $this->applyPagination($query, $request);
    }
    
    /**
     * Format pagination data for frontend
     */
    protected function formatPaginationData(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }
}

