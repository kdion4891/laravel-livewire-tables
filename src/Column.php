<?php

namespace Kdion4891\LaravelLivewireTables;

use Illuminate\Support\Str;

class Column
{
    private $heading;
    private $attribute;
    private $searchable = false;
    private $sortable = false;
    private $view;

    public function __construct($heading, $attribute)
    {
        $this->heading = $heading;
        $this->attribute = $attribute ? $attribute : Str::snake(Str::lower($heading));
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public static function make($heading, $attribute = null)
    {
        return new static($heading, $attribute);
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

    public function view($view)
    {
        $this->view = $view;
        return $this;
    }
}
