<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    /** @var Request */
    public $request;

    /** @var Builder */
    protected $builder;

    /** @var array */
    protected $against = [
        //
    ];

    /** @var array */
    protected $globalValidationRules = [
        'latest' => 'prohibits:sort',
    ];

    /** @var array */
    protected $validationRules = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function request($key)
    {
        return $this->request->query($key);
    }

    //public function user(): User
    //{
    //    return $this->request->user();
    //}

    public function getFields()
    {
        return Schema::getColumnListing((new $this->model)->getTable());
    }

    public function pre(Builder $query, Request $request)
    {
        //
    }

    public function latest($latest = 'created_at')
    {
        $latest = $latest ?: 'created_at';

        return $this->builder->orderBy($latest, 'desc');
    }

    public function group($groupBy)
    {
        return $this->builder->groupBy($groupBy)->select($this->getFields());
        //return $this->builder->groupBy($groupBy)->distinct();
    }

    public function sort(string $field = null)
    {
        $fields = Schema::getColumnListing((new User)->getTable());
        $ascendency = Str::startsWith($field, '-') ? 'desc' : 'asc';
        $field = Str::startsWith($field, '-') ? substr($field, 1) : $field;

        if (!$field || !in_array($field, $fields)) {
            return $this->builder;
        }

        return $this->builder->orderBy($field, $ascendency);
    }

    final public function apply(Builder $query)
    {
        $this->builder = $query;

        $this->request->validate(
            array_merge($this->globalValidationRules, $this->validationRules)
        );

        $this->pre($query, $this->request);

        foreach ($this->filters() as $name => $value) {
            $name = method_exists($this, $name)
                ? $name : (method_exists($this, Str::camel($name)) ? Str::camel($name) : null);

            $name ?
            call_user_func_array([$this, $name], array_filter([$value])) : '';
        }

        return $this->builder;
    }

    final public function filters()
    {
        return $this->request->all();
    }
}
