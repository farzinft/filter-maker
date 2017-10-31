<?php
namespace Farzin\FilterMaker;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class AbstractQueryStringFilter
{
    protected $builder;
    protected $request;
    protected $filters;

    public function __construct(Request $request, Builder $builder)
    {
        $this->request = $request;
        $this->builder = $builder;
    }

    public function applyFilter()
    {
        $filters = $this->filters();
        $filters->each(function ($value) {
            $methodName = $this->getMethodName($value);
            $request = $this->request->get($value);
            if (method_exists($this, $methodName) && $request) {
                if (is_string($request) && !empty(json_decode($request, true))) {
                    $request = json_decode($request, true);
                }
                call_user_func([$this, $methodName], $request);
            }
        });

        return $this->builder;
    }

    public function filters()
    {
        return collect($this->mapMethodNames)->keys();
    }

    protected function getMethodName($index)
    {
        $_index = strpos($index, '-') ? str_replace('-', '_', $index) : '';

        $indexFound = isset($this->mapMethodNames[$_index]) ? $_index : (isset($this->mapMethodNames[$index]) ? $index : false);

        if ($indexFound) {
            return $this->mapMethodNames[$indexFound];
        }

    }

    abstract protected function baseQuery();
}