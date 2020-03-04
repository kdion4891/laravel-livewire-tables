# Laravel Livewire Tables

![Laravel Livewire Tables](https://i.imgur.com/Jg5WPOa.gif)

A dynamic, responsive [Laravel Livewire](https://laravel-livewire.com) table component with searching, sorting, checkboxes, and pagination.

- [Support](https://github.com/kdion4891/laravel-livewire-tables/issues)
- [Contributions](https://github.com/kdion4891/laravel-livewire-tables/pulls)
- [Buy me a coffee](https://paypal.me/kjjdion)

# Installation

Make sure you've [installed Laravel Livewire](https://laravel-livewire.com/docs/installation/).

Installing this package via composer:

    composer require kdion4891/laravel-livewire-tables
    
This package was designed to work well with [Laravel frontend scaffolding](https://laravel.com/docs/master/frontend).

If you're just doing scaffolding now, you'll need to add the Livewire `@livewireScripts` and `@livewireStyles` blade directives to your `resources/views/layouts/app.blade.php` file:

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
    
    ...
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts

This package also uses [Font Awesome](https://fontawesome.com) for icons. If you don't already have it installed, it's as simple as:

    npm install @fortawesome/fontawesome-free
    
Then add the following line to `resources/sass/app.scss`:
    
    @import '~@fortawesome/fontawesome-free/css/all.min.css';
    
Now all that's left is to compile the assets:
    
    npm install && npm run dev

# Making Table Components

Using the `make` command:

    php artisan make:table UserTable --model=User

This creates your new table component in the `app/Http/Livewire` folder.

After making a component, you may want to edit the `query` and `column` methods:

    class UserTable extends TableComponent
    {
        public function query()
        {
            return User::query();
        }
    
        public function columns()
        {
            return [
                Column::make('ID')->searchable()->sortable(),
                Column::make('Created At')->searchable()->sortable(),
                Column::make('Updated At')->searchable()->sortable(),
            ];
        }
    }
    
You don't have to use the `render()` method in your table component or worry about a component view, because the package handles that automatically.

# Using Table Components

You use table components in views just like any other Livewire component:

    @livewire('user-table')

Now all you have to do is update your table component class!

# Table Component Properties

### `$table_class`

Sets the CSS class names to use on the `<table>`. Defaults to `table-hover`.

Example:

    public $table_class = 'table-hover table-striped';
    
Or, via `.env` to apply globally:

    TABLE_CLASS="table-hover table-striped"

### `$thead_class`

Sets the CSS class names to use on the `<thead>`. Defaults to `thead-light`.

Example:

    public $thead_class = 'thead-dark';
    
Or, via `.env` to apply globally:

    TABLE_THEAD_CLASS="thead-dark"
    
### `$header_view`

Sets a custom view to use for the table header (displayed next to the search).

Example:

    public $header_view = 'users.table-header';
    
**Protip: any view you reference in your table component can use Livewire actions, triggers, etc!**
    
    {{-- resources/views/users/table-header.blade.php --}}
    <button class="btn btn-primary" wire:click="createUser">Create User</button>
    
### `$footer_view`

Sets a custom view to use for the table footer (displayed next to the pagination).

Example:

    public $footer_view = 'users.table-footer';
    
### `$checkbox`

Boolean for if the table should use checkboxes or not. Defaults to `true`.

Example:

    public $checkbox = false;
    
Or, via `.env` to apply globally:

    TABLE_CHECKBOX=false
    
### `$checkbox_side`

The side of the table to place checkboxes on. Accepts `left` or `right`. Defaults to `right`.

Example:

    public $checkbox_side = 'left';
    
Or, via `.env` to apply globally:

    TABLE_CHECKBOX_SIDE="left"
    
### `$checkbox_attribute`

Sets the attribute name to use for `$checkbox_values`. Defaults to `id`. I recommend keeping this as `id`.

Example:

    public $checkbox_attribute = 'id';
    
### `$checkbox_values`

Contains an array of checked values. For example, if `$checkbox_attribute` is set to `id`, this will contain an array of checked model `id`s.
Then you can use those `id`s to do whatever you want in your component. For example, a `deleteChecked` button inside a custom `$header_view`.

Example `deleteChecked` button:

    <button class="btn btn-danger" onclick="confirm('Are you sure?') || event.stopImmediatePropagation();" wire:click="deleteChecked">
        Delete Checked
    </button>

Example `deleteChecked` method:

    public function deleteChecked()
    {
        Car::whereIn('id', $this->checkbox_values)->delete();
    }

### `$sort_attribute`

Sets the default attribute to sort by. Defaults to `id`. This also works with counts and relationships.

Example:

    public $sort_attribute = 'created_at';
    
Count example (if you added `->withCount('relations')` to the `query()` method):

    public $sort_attribute = 'relations_count';
    
Relationship example (if you added `->with('relation')` to the `query()` method):

    public $sort_attribute = 'relation.name';
    
Notice the use of the dot notation. You use this when declaring column relationship attributes as well.

### `$sort_direction`

Sets the default direction to sort by. Accepts `asc` or `desc`. Defaults to `desc`.

Example:

    public $sort_direction = 'asc';
    
### `$per_page`

Sets the amount of results to display per page. Defaults to `15`.

Example:

    public $per_page = 25;
    
Or, via `.env` to apply globally:

    TABLE_PER_PAGE=25

# Table Component Methods

### `query()`

This method returns an [Eloquent](https://laravel.com/docs/master/eloquent) model query to be used by the table.

Example:

    public function query()
    {
        return Car::with('brand')->withCount('accidents');
    }

### `columns()`

This method returns an array of `Column`s to use in the table.

Example:

    public function columns()
    {
        return [
            Column::make('ID')->searchable()->sortable(),
            Column::make('Brand Name', 'brand.name')->searchable()->sortable(),
            Column::make('Name')->searchable()->sortable(),
            Column::make('Color')->searchable()->sortable()->view('cars.table-color'),
            Column::make('Accidents', 'accidents_count')->sortable(),
            Column::make()->view('cars.table-actions'),
        ];
    }

Declaring `Column`s is similar to declaring Laravel Nova fields. [Jump to the column declaration section](#table-column-declaration) to learn more.

### `thClass($attribute)`

This method is used to compute the `<th>` CSS class for the table header.

##### `$attribute`

The column attribute.

Example:

    public function thClass($attribute)
    {
        if ($attribute == 'name') return 'font-italic';
        if ($attribute == 'accidents_count') return 'text-right';
        if ($attribute == 'brand.name') return 'font-weight-bold';

        return null;
    }

### `trClass($model)`

This method is used to compute the `<tr>` CSS class for the table row. 
    
##### `$model`

The model instance for the table row.

Example:

    public function trClass($model)
    {
        if ($model->name == 'Silverado') return 'table-secondary';
        if ($model->accidents_count > 8) return 'table-danger';
        if ($model->brand->name == 'Ford') return 'table-primary';

        return null;
    }

### `tdClass($attribute, $value)`

This method is used to compute the `<td>` CSS class for the table data.

##### `$attribute`

The column attribute.

##### `$value`

The column value.

Example:

    public function tdClass($attribute, $value)
    {
        if ($attribute == 'name' && $value == 'Silverado') return 'table-secondary';
        if ($attribute == 'accidents_count' && $value < 2) return 'table-success';
        if ($attribute == 'brand.name' && $value == 'Ford') return 'table-primary';

        return null;
    }

### `mount()`

This method sets the initial table properties. If you have to override it, be sure to call `$this->setTableProperties()`.

Example:

    public function mount()
    {
        $this->setTableProperties();
        
        // my custom code
    }

### `render()`

This method renders the table component view. If you have to override it, be sure to `return $this->tableView()`.

Example:

    public function render()
    {
        // my custom code
        
        return $this->tableView();
    }
    
# Table Column Declaration

The `Column` class is used to declare your table columns.

    public function columns()
    {
        return [
            Column::make('ID')->searchable()->sortable(),
            Column::make('Created At')->searchable()->sortable(),
            Column::make('Updated At')->searchable()->sortable(),
        ];
    }

### `make($heading = null, $attribute = null)`

##### `$heading`

The heading to use for the table column, e.g. `Created At`. Can be null for view-only columns.

##### `$attribute`

The attribute to use for the table column value. If null, it will use a snake cased `$heading`.

You can also specify `_count`s and relationship attributes with a dot notation.

For counts, let's say I added `withCount()` to my `query()`:

    public function query()
    {
        return Car::withCount('accidents');
    }

Now I can create a column using this count like so:

    Column::make('Accidents', 'accidents_count')->sortable(),
    
For relationships, let's say I added `with()` to my `query()`:

    public function query()
    {
        return Car::with('brand');
    }
    
Now I can create a column using any of the relationship attributes like so:

    Column::make('Brand ID', 'brand.id')->searchable()->sortable(),
    Column::make('Brand Name', 'brand.name')->searchable()->sortable(),

### `searchable()`

Sets the column to be searchable.

### `sortable()`

Sets the column to be sortable.

### `sortUsing($callback)`

Allows custom logic to be used for sorting. Your supplied [`callable`](https://www.php.net/manual/en/language.types.callable.php) will receive the following parameters:

* `$models`: The current Eloquent query (`\Illuminate\Database\Eloquent\Builder`). You should apply your sort logic to this query, and return it.
* `$sort_attribute`: The name of the column currently being sorted. If you used a nested relationship for sorting, it will be properly transformed to `relationship_table.column_name` format so the query will be scoped correctly.
* `$sort_direction`: The direction sort direction requested, either `asc`, or `desc`.

Additionally, your callback will be passed through Laravel's Container so you may inject any dependencies you need in your callback. Make sure your dependencies are listed before the parameters above.

Example:

    Column::make('Paint Color')->searchable()->sortable()->sortUsing(function ($models, $sort_attribute, $sort_direction) {
        return $models->orderByRaw('?->\'$.color_code\' ?', [$sort_attribute, $sort_direction]);
    });
    
This will sort the `paint_color` column using the JSON value `color_code`.

**SQL Injection warning**: Make sure if you are using any of Eloquent's `*Raw` methods, you always use the bindings feature.

### `view($view)`

Sets a custom view to use for the column.

Example:

    Column::make('Paint Color')->searchable()->sortable()->view('cars.table-paint-color'),
    
Notice how the column is still `searchable()` and `sortable()`, because the `Car` model contains a `paint_color` attribute!

If you're making a view-only column (for action buttons, etc), just don't make it searchable or sortable:

    Column::make()->view('cars.table-actions'),

**Custom column views are passed `$model` and `$column` objects, as well as variables passed from the table component.**

For the `Paint Color` example, we can use the `paint_color` attribute from the model like so:

    {{-- resources/views/cars/table-paint-color.blade.php --}}
    <i class="fa fa-circle" style="color: {{ $model->paint_color }};"></i>

For the action buttons example, we can use the `id` attribute from the model like so:

    {{-- resources/views/cars/table-actions.blade.php --}}
    <button class="btn btn-primary" wire:click="showCar({{ $model->id }})">Show</button>
    <button class="btn btn-primary" wire:click="editCar({{ $model->id }})">Edit</button>

Using a custom view for a relationship column? No problem:

    {{-- resources/views/cars/table-brand-name.blade.php --}}
    {{ $model->brand->name }}

# Publishing Files

Publishing files is optional.

Publishing the table view files:

    php artisan vendor:publish --tag=table-views

Publishing the config file:

    php artisan vendor:publish --tag=table-config
