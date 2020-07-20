use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\NumericColumn;
# Livewire Datatables

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mediconesystems/livewire-datatables.svg?style=flat-square)](https://packagist.org/packages/mediconesystems/livewire-datatables)
[![Build Status](https://img.shields.io/travis/mediconesystems/livewire-datatables/master.svg?style=flat-square)](https://travis-ci.org/mediconesystems/livewire-datatables)
[![Quality Score](https://img.shields.io/scrutinizer/g/mediconesystems/livewire-datatables.svg?style=flat-square)](https://scrutinizer-ci.com/g/mediconesystems/livewire-datatables)
[![Total Downloads](https://img.shields.io/packagist/dt/mediconesystems/livewire-datatables.svg?style=flat-square)](https://packagist.org/packages/mediconesystems/livewire-datatables)

### Features
- Use a model or query builder to supply data
- Mutate and format columns using preset or cutom callbacks
- Sort data using column or computed column
- Filter using booleans, times, dates, selects or free text
- Show / hide columns

## [Live Demo App](https://livewire-datatables.com)

## [Demo App Repo](https://github.com/MedicOneSystems/demo-livewire-datatables)

![screenshot](resources/images/screenshot.png "Screenshot")

## Requirements
- [Laravel 7](https://laravel.com/docs/7.x)
- [Livewire](https://laravel-livewire.com/)
- [Tailwind](https://tailwindcss.com/)


## Installation

You can install the package via composer:

```bash
composer require mediconesystems/livewire-datatables
```
### Optional
You don't need to, but if you like you can publish the config file and blade template assets:
```bash
php artisan vendor:publish
```
This will enable you to modify the blade views and apply your own styling, the datatables views will be published to resources/livewire/datatables. The config file contains the default time and date formats used throughout
> - This can be useful if you're using Purge CSS on your project, to make sure all the livewire-datatables classes get included

## Basic Usage

- Use the ```livewire-datatable``` component in your blade view, and pass in a model:
```html
...

<livewire:datatable model="App\User" />

...
```

## Template Syntax
- There are many ways to modify the table by passing additional properties into the component:
```html
<livewire:datatable
    model="App\User"
    exclude="updated_at, email_verified_at"
    dates="dob"
    renames="created_at|Created"
/>
```

### Props
| Property | Arguments | Result | Example |
|----|----|----|----|
|**model**|*String* full model name|Define the base model for the table| ```model="App\Post"```|
|**include**|*String\|Array* of column definitions|only these columns are shown in table| ```include="name, email, dob, role"```|
|**exclude**|*String\|Array* of column definitions|columns are excluded from table| ```:exlcude="['created_at', 'updated_at']"```|
|**hide**|*String\|Array* of column definitions|columns are present, but start hidden|```:hidden="email_verified_at"```|
|**dates**|*String\|Array* of column definitions [ and optional format in \| delimited string]|column values are formatted as per the default date format, or format can be included in string with \| separator | ```:dates="['dob|lS F y', 'created_at']"```|
|**times**|*String\|Array* of column definitions [ and optional format in \| delimited string]|column values are formatted as per the default time format, or format can be included in string with \| separator | ```'bedtime|g:i A'```|
|**renames**|*String\|Array* of column definitions and desired name in \| delimited string |Applies custom column names | ```renames="email_verified_at|Verififed"```|
|**searchable**|*String\|Array* of column names | Defines columns to be included in global search | ```searchable="name, email"```|
|**sort**|*String* of column definition [and optional 'asc' or 'desc' (default: 'desc') in \| delimited string]|Specifies the column and direction for initial table sort. Default is column 0 descending | ```sort="name|asc"```|
|**hide-header**|*Boolean* default: *false*|The top row of the table including the column titles is removed if this is ```true```| |
|**hide-pagination**|*Boolean* default: *false*|Pagination controls are removed if this is ```true```| |
|**per-page**|*Integer* default: 10|Number of rows per page| ```per-page="20"``` |


---


## Component Syntax

To get full control over your datatable:

- create a livewire component that extends ```Mediconesystems\LivewireDatatables\LivewireDatatable```
> ```php artisan livewire:datatable foo``` --> 'app/Http/Livewire/Foo.php'

> ```php artisan livewire:datatable tables.bar``` --> 'app/Http/Livewire/Tables/Bar.php'

- Provide a datasource by declaring public property ```$model``` **OR** public method ```builder()``` that returns an instance of ```Illuminate\Database\Eloquent\Builder```
> ```php artisan livewire:datatable users-table --model=user``` --> 'app/Http/Livewire/UsersTable.php' with ```public $model = User::class```
- Declare a public method ```columns``` that returns a ```Mediconesystems\LivewireDatatables\Columnset``` containing one or more ```Mediconesystems\LivewireDatatables\Column```
- Columns can be built using any of the static methods below, and then their attributes assigned using fluent method chains.
There are different types of Column, using the correct one for your datatype will enable type-specific formatting and filtering:

| Class | Description |
|---|---|
|Column|Generic string-based column. Filter will be a text input|
|NumericColumn| Number-based column. Filters will be a numeric range|
|BooleanColumn| Values will be automatically formatted to a yes/no icon, filters will be yes/no|
|DateColumn| Values will be automatically formatted to the default date format. Filters will be a date range|
|TimeColumn| Values will be automatically formatted to the default time format. Filters will be a time range|

```php
class ComplexDemoTable extends LivewireDatatable
{

    public function builder()
    {
        return User::query()
            ->leftJoin('planets', 'planets.id', 'users.planet_id');
    }

    public function columns()
    {
        return Columnset::fromArray([
            NumericColumn::field('users.id')
                ->label('ID')
                ->linkTo('job', 6),

            BooleanColumn::field('users.email_verified_at')
                ->label('Email Verified')
                ->format()
                ->filterable(),

            Column::field('users.name')
                ->defaultSort('asc')
                ->searchable()
                ->filterable(),

            Column::field('planets.name')
                ->label('Planet')
                ->searchable()
                ->filterable($this->planets),

            DateColumn::field('users.dob')
                ->label('DOB')
                ->filterable()
                ->hide()
        ]);
    }
}
```

### Column Methods
| Method | Arguments | Result | Example |
|----|----|----|----|
|_static_ **field**| *String* $column |Builds a column from column definition|```Column::field('users.name')```|
|_static_ **raw**| *String* $rawSqlStatement|Builds a column from raw SQL statement. Must include "... AS _alias_"|```Column::raw("CONCAT(ROUND(DATEDIFF(NOW(), users.dob) / planets.orbital_period, 1) AS `Native Age`")```|
|_static_ **scope**|*String* $scope, *String* $alias|Builds a column from a scope on the parent model|```Column::scope('selectLastLogin', 'Last Login')```|
|**label**|*String* $name|Changes the display name of a column|```Column::field('users.id')->label('ID)```|
|**format**|[*String* $format]|Formats the column value according to type. Dates/times will use the default format or the argument |```Column::field('users.email_verified_at')->filterable(),```|
|**hide**| |Marks column to start as hidden|```Column::field('users.id')->hidden()```|
|**sortBy**|*String\|Expression* $column|Changes the query by which the column is sorted|```Column::field('users.dob')->sortBy(DB::raw('DATE_FORMAT(users.dob, "%m%d%Y")')),```|
|**truncate**|[*Integer* $length (default: 16)]Truncates column to $length and provides full-text in a tooltip. Uses ```view('livewire-datatables::tooltip)```|```Column::field('users.biography)->truncate(30)```|
|**linkTo**|*String* $model, [*Integer* $pad]|Replaces the value with a link to ```"/$model/$value"```. Useful for ID columns. Optional zero-padding. Uses ```view('livewire-datatables::link)```|```Column::field('users.id')->linkTo('user')```|
|**round**|[*Integer* $precision (default: 0)]|Rounds value to given precision|```Column::field('patients.age')->round()```|
|**defaultSort**|[*String* $direction (default: 'desc')]|Marks the column as the default search column|```Column::field('users.name')->defaultSort('asc')```|
|**searchable**| |Includes the column in the global search|```Column::field('users.name')->searchable()```|
|**filterable**|[*Array* $options], [*String* $filterScope]|Adds a filter to the column, according to Column type. If an array of options is passed it wil be used to populate a select input. If the column is a scope column then the name of the filter scope muyst also be passed|```Column::field('users.allegiance')->filterable(['Rebellion', 'Empire'])```|
|**callback**|*Closure\|String* $callback [, *Array* $params (default: [])]| Passes the column value, whole row of values, and any additional parameters to a callback to allow custom mutations the callback can be a method of the table class, or inline and will recieve the column value, and the row of other data as its first 2 parameters | _(see below)_|
|**additionalSelects**|*String\|Array* $selectStatements| Queries additional data required for callbacks, views or editable columns| _(see below)_|
|**view**|*String* $viewName| Passes the column value, whole row of values, and any additional parameters to a view template | _(see below)_|
|**editable**| | Marks the column as editable | _(see below)_|


### Callbacks
Callbacks give you the freedom to perform any mutations you like on the data before displaying in the table.
- The callbacks are performed to the paginated results of the database query
- Callbacks will receive the column's value as their first argument, and the whole query row as the second, followed by any specified.

```php
class CallbackDemoTable extends LivewireDatatable
{
    public model = User::class

    public function columns()
    {
        return Columnset::fromArray([
            Column::field('users.id'),

            Column::field('users.dob')->format(),

            Column::field('users.signup_date')->callback('ageAtSignup', 10, 'red'),
        ]);
    }

    public function ageAtSignup($value, $row, $threshold, $colour)
    {
        $age = $value->diffInYears($row->dob);
        return age > $threshold
            ? '<span class="text-' . $colour . '-500">' .$age . '</span>'
            : $age;
    }
}
```

> If you are using a callback that depends on data that has not been queried from the database, you can use ```additionalSelects``` to append them to the query

```php
class CallbackDemoTable extends LivewireDatatable
{
    public model = User::class

    public function columns()
    {
        return Columnset::fromArray([
            Column::field('users.id'),

            Column::field('users.signup_date')
                ->additionalSelects('users.dob')
                ->callback(function ($value, $row, $threshold, $colour) {
                    $age = $value->diffInYears($row->dob);
                    return age > 10
                        ? '<span class="text-red-500">' .$age . '</span>'
                        : $age;
                })
        ]);
    }
}
```

### Views
You can specify that a column's output is piped directly into a separate blade view template.
- Template is specified using ususal laravel view helper syntax
- Views will receive the column's value as ```$value```, and the whole query row as ```$row```
```php
class CallbackDemoTable extends LivewireDatatable
{
    public model = User::class

    public function columns()
    {
        return Columnset::fromArray([
            Column::field('users.id'),

            Column::field('users.dob')->view('tables.dateview'),

            Column::field('users.signup_date')->format(),
        ]);
    }
```
```html
'tables/dateview.blade.php'
<span class="mx-4 my-2 bg-pink-500">
    <x-date-thing :value="$value" />
</span>
```

### Editable Columns
You can mark a column as editable using ```editable```
This uses the ```view()``` method above to pass the data into an Alpine/Livewire compnent that can directly update the underlying database data. Requires the column to have ```column``` defined using standard Laravel naming. This is included as an example. Much more comprehensive custom editable columns with validation etc can be built using the callback or view methods above.

```php
class EditableTable extends LivewireDatatable
{

    public function builder()
    {
        return User::query()
            ->leftJoin('planets', 'planets.id', 'users.planet_id');
    }

    public function columns()
    {
        return Columnset::fromArray([
            Column::field('users.id')
                ->label('ID')
                ->linkTo('job', 6),

            Column::field('users.email')
                ->editable()
        ]);
    }
}
````
