<?php

/**
 * Tranks to https://github.com/rappasoft/laravel-livewire-tables
 */

namespace Kdion4891\LaravelLivewireTables\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ModelExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @var array
     */
    public $builder;

    /**
     * @var array
     */
    public $columns;

    /**
     * CSVExport constructor.
     *
     * @param  Builder  $builder
     * @param  array  $columns
     */
    public function __construct(Builder $builder, array $columns = [])
    {
        $this->builder = $builder;
        $this->columns = $columns;
    }

    /**
     * @return array|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return $this->builder;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->getHeadingRow();
    }

    /**
     * @return array
     */
    public function getHeadingRow(): array
    {
        $headers = [];

        foreach ($this->columns as $column) {
            $headers[] = $column->heading;
        }

        return $headers;
    }

    /**
     * @param  mixed  $row
     *
     * @return array
     */
    public function map($row): array
    {
        $map = [];

        foreach ($this->columns as $column) {
            
            $map[] = strip_tags($row->getAttribute($column->attribute));
        }

        return $map;
    }
}
