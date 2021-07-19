<?php

namespace Kdion4891\LaravelLivewireTables;

use Illuminate\Support\Str;

/**
 * @property string $heading
 * @property string $attribute
 * @property boolean $searchable
 * @property boolean $sortable
 * @property callable $sortCallback
 * @property string $view
 */
class Column
{
    protected $heading;
    protected $attribute;
    protected $searchable = false;
    protected $sortable = false;
    protected $sortCallback;
    protected $view;
    protected $accessor;
    protected $collspan;

    public function __construct($heading, $attribute, $accessor)
    {
        $this->heading = $heading;
        $this->attribute = $attribute ?? Str::snake(Str::lower($heading));
        $this->accessor = $accessor;
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public static function make($heading = null, $attribute = null, $accessor = null)
    {
        return new static($heading, $attribute, $accessor);
    }

    public function searchable()
    {
        $this->searchable = true;
        return $this;
    }

    public function sortable()
    {
        $this->sortable = true;
        return $this;
    }

    public function collspan(int $count)
    {
        $this->collspan = $count;
        return $this;
    }

    public function sortUsing(callable $callback)
    {
        $this->sortCallback = $callback;
        return $this;
    }

    public function view($view)
    {
        $this->view = $view;
        return $this;
    }
}
