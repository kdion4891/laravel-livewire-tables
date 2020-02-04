<?php

namespace Kdion4891\LaravelLivewireTables\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeTable extends Command
{
    protected $signature = 'make:table {name} {--model=Model}';
    protected $description = 'Make a new Laravel Livewire table component.';

    public function handle()
    {
        $stub = File::get(__DIR__ . '/../../resources/stubs/component.stub');
        $stub = str_replace('DummyComponent', $this->argument('name'), $stub);
        $stub = str_replace('DummyModel', $this->option('model'), $stub);
        $path = app_path('Http/Livewire/' . $this->argument('name') . '.php');

        File::ensureDirectoryExists(app_path('Http/Livewire'));

        if (!File::exists($path) || $this->confirm($this->argument('name') . ' already exists. Overwrite it?')) {
            File::put($path, $stub);
            $this->info($this->argument('name') . ' was made!');
        }
    }
}
