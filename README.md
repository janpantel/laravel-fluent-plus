Laravel Fluent Plus
===
This package extends Laravel's `Fluent` class to provide a
supercharged experience like type casting and recursive
Fluent access to sub-assocs.

## Basic usage
Instead of extending `Illuminate\Support\Fluent` you extend
`JanPantel\LaravelFluentPlus\FluentPlus` instead. As this
class is a superset of Fluent it can be treated as a drop
in replacement.

`FluentPlus` implements a `$casts` property that defines
how the attributes of your assoc should be cast.

For the following example assume `NamesContainer`, `Price`
and `StockLevel` to be classes extending `Fluent`
or `FluentPlus` as well.
```php
class MyContainer extends FluentPlus
{
    protected $casts = [
        // Use the contents of the `names` attribute
        // to initialize a new `NamesContainer` instance.
        'names' => NamesContainer::class,

        // Assumes that the `price` attribute contains
        // an array and initializes a new `Price` instance
        // for every item. 
        'prices' => [Price::class],

        // The same as the array syntax but using
        // Laravel's `Collection` class instead.
        'stock' => [
            \Illuminate\Support\Collection::class,
            StockLevel::class
        ],
    ];
}
```

The resulting object now offers convenient property access:
```php
$container = new MyContainer($sourceAssoc);

var_dump($container->names->english);
var_dump($container->prices[0]->amount);
var_dump($container->stock->get('store_1')->quantity);
```

## The problem this package solves
If you work with assocs a lot and like using property access
over assoc access Laravel's `Fluent` class is a convenient
way to do so.

The use case that made me write this package is working
with responses fetched from 3rd party APIs. Since I want
to use `@property` PHP docs to have "type safety" while
not manually assigning properties.

## Advanced usage
### Recursive array casts
`FluentPlus` casts sub-assocs that do not
have a cast defined into other FluentPlus for infinite
dimension property access.
```php
$instance = new Fluent(['foo' => ['bar' => 'baz']]);
$instancePlus = new FluentPlus(['foo' => ['bar' => 'baz']]);

// breaks :(
var_dump($instance->foo->bar);
// works :)
var_dump($instancePlus->foo->bar);
```
You can turn this feature off by setting the `$recursive`
property in your derived `FluentPlus` to `false` or using
the `NonRecursiveFluentPlus` utility class if you want
to inline it instead.