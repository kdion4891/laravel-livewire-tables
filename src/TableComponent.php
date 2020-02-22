<?php

namespace Kdion4891\LaravelLivewireTables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Kdion4891\LaravelLivewireTables\Traits\ThanksYajra;
use Livewire\Component;
use Livewire\WithPagination;

class TableComponent extends Component
{
    use WithPagination, ThanksYajra;

    public $table_class;
    public $thead_class;
    public $header_view;
    public $footer_view;
    public $search;
    public $checkbox;
    public $checkbox_side;
    public $checkbox_attribute = 'id';
    public $checkbox_all = false;
    public $checkbox_values = [];
    public $sort_attribute = 'id';
    public $sort_direction = 'desc';
    public $per_page;

    public function mount()
    {
        $this->setTableProperties();
    }

    public function setTableProperties()
    {
        foreach (['table_class', 'thead_class', 'checkbox', 'checkbox_side', 'per_page'] as $property) {
            $this->$property = $this->$property ?? config('laravel-livewire-tables.' . $property);
        }
    }

    public function render()
    {
        return $this->tableView();
    }

    public function tableView()
    {
        return view('laravel-livewire-tables::table', [
            'columns' => $this->columns(),
            'models' => $this->models()->paginate($this->per_page),
        ]);
    }

    public function query()
    {
        return \App\User::query();
    }

    /**
     * @return \Kdion4891\LaravelLivewireTables\Column[]
     */
    public function columns()
    {
        return [
            Column::make('ID')->searchable()->sortable(),
            Column::make('Name')->searchable()->sortable(),
            Column::make('Email')->searchable()->sortable(),
            Column::make('Created At')->searchable()->sortable(),
            Column::make('Updated At')->searchable()->sortable(),
        ];
    }

    public function thClass($attribute)
    {
        return null;
    }

    public function trClass($model)
    {
        return null;
    }

    public function tdClass($attribute, $value)
    {
        return null;
    }

    public function models()
    {
        $models = $this->query();

        if ($this->search) {
            $models->where(function (Builder $query) {
                foreach ($this->columns() as $column) {
                    if ($column->searchable) {
                        if (Str::contains($column->attribute, '.')) {
                            $relationship = $this->relationship($column->attribute);

                            $query->orWhereHas($relationship->name, function (Builder $query) use ($relationship) {
                                $query->where($relationship->attribute, 'like', '%' . $this->search . '%');
                            });
                        }
                        else if (Str::endsWith($column->attribute, '_count')) {
                            // No clean way of using having() with pagination aggregation, do not search counts for now.
                            // If you read this and have a good solution, feel free to submit a PR :P
                        }
                        else {
                            $query->orWhere($query->getModel()->getTable() . '.' . $column->attribute, 'like', '%' . $this->search . '%');
                        }
                    }
                }
            });
        }

        if (Str::contains($this->sort_attribute, '.')) {
            $relationship = $this->relationship($this->sort_attribute);
            $sort_attribute = $this->attribute($models, $relationship->name, $relationship->attribute);
        }
        else {
            $sort_attribute = $this->sort_attribute;
        }

        if (($column = $this->getColumnByAttribute($this->sort_attribute)) !== null && is_callable($column->sortCallback)) {
            return app()->call($column->sortCallback, ['models' => $models, 'sort_attribute' => $sort_attribute, 'sort_direction' => $this->sort_direction]);
        }

        return $models->orderBy($sort_attribute, $this->sort_direction);
    }

    public function updatedSearch()
    {
        $this->gotoPage(1);
    }

    public function updatedCheckboxAll()
    {
        $this->checkbox_values = [];

        if ($this->checkbox_all) {
            $this->models()->each(function ($model) {
                $this->checkbox_values[] = (string)$model->{$this->checkbox_attribute};
            });
        }
    }

    public function updatedCheckboxValues()
    {
        $this->checkbox_all = false;
    }

    public function sort($attribute)
    {
        if ($this->sort_attribute != $attribute) {
            $this->sort_direction = 'asc';
        }
        else {
            $this->sort_direction = $this->sort_direction == 'asc' ? 'desc' : 'asc';
        }

        $this->sort_attribute = $attribute;
    }

    /**
     * @return null|\Kdion4891\LaravelLivewireTables\Column
     */
    protected function getColumnByAttribute($attribute)
    {
        foreach ($this->columns() as $col) {
            if ($col->attribute === $attribute) {
                return $col;
            }
        }

        return null;
    }
}
