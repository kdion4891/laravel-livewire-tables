<?php

/**
 * Tranks to https://github.com/rappasoft/laravel-livewire-tables
 */

 namespace Kdion4891\LaravelLivewireTables\Traits;
 
use Maatwebsite\Excel\Excel;
use Kdion4891\LaravelLivewireTables\Exports\ModelExport;

trait Export
{
    public function export($type)
    {
        $type = strtolower($type);

        switch ($type) {
            case 'csv':default:
                $writer = Excel::CSV;
            break;

            case 'xls':
                $writer = Excel::XLS;
            break;

            case 'xlsx':
                $writer = Excel::XLSX;
            break;

            case 'pdf':
                $writer = Excel::DOMPDF;
                
            break;
        }

        $class = ModelExport::class;

        return (new $class(
            $this->models(),
            $this->columns(),
        ))->download($this->export_filename.'.'.$type, $writer);
    }
}
