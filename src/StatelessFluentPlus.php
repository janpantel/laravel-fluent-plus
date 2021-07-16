<?php

namespace JanPantel\LaravelFluentPlus;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Fluent;
use JanPantel\LaravelFluentPlus\Transformers\ArrayTransformer;
use JanPantel\LaravelFluentPlus\Transformers\ObjectTransformer;
use JanPantel\LaravelFluentPlus\Transformers\TransformerInterface;
use JanPantel\LaravelFluentPlus\Transformers\CollectionTransformer;

/**
 * Class StatelessFluentPlus
 *
 * Implements the FluentPlus logic without keeping global properties.
 * While this is the less pleasant way of doing things it makes serialization cheaper.
 *
 * If you don't mind serialization size the `FluentPlus` class should be used for a better experience.
 *
 * @package JanPantel\LaravelFluentPlus
 */
class StatelessFluentPlus extends Fluent
{
    /**
     * FluentPlus constructor.
     *
     * @param array|object $attributes
     * @param array? $casts
     * @param TransformerInterface[]? $caster
     */
    public function __construct($attributes = [])
    {
        parent::__construct(
            $this->prepare(
                $attributes,
                $this->getCasts(),
                $this->getTransformers(),
                $this->isRecursive()
            )
        );
    }

    /**
     * Returns the array of cast definitions.
     *
     * @return array
     */
    protected function getCasts()
    {
        return [];
    }

    /**
     * Determines whether sub-assocs should be parsed into a FluentPlus instance.
     *
     * @return bool
     */
    protected function isRecursive()
    {
        return true;
    }

    /**
     * Returns the array of casters.
     *
     * @return TransformerInterface[]
     */
    protected function getTransformers()
    {
        return [
            new ObjectTransformer,
            new ArrayTransformer,
            new CollectionTransformer,
        ];
    }

    /**
     * Prepares the attributes before they are handed to the base Fluent class.
     *
     * @param array|object $attributes
     * @param array $casts
     * @param TransformerInterface[] $transformers
     * @param boolean $isRecursive
     * @return array
     */
    private static function prepare($attributes, $casts, $transformers, $isRecursive)
    {
        foreach ($attributes as $key => $value) {
            $castDefinition = Arr::get($casts, $key);

            $transformer = null;
            if (! is_null($castDefinition)) {
                $transformer = self::findTransformer($castDefinition, $transformers, $value);
            }

            if (is_null($transformer)) {
                $transformer = function ($castDefinition, $value) use ($isRecursive) {
                    if ($isRecursive && is_array($value)) {
                        if (Arr::isAssoc($value)) {
                            return new FluentPlus($value);
                        } else {
                            return collect($value)->map(function ($child) {
                                return is_array($child) ? new FluentPlus($child) : $child;
                            });
                        }
                    }
                    return $value;
                };
            }

            if ($transformer instanceof TransformerInterface) {
                $transformer = function ($castDefinition, $value) use ($transformer) {
                    return $transformer->transform($castDefinition, $value);
                };
            }

            $attributes[$key] = $transformer($castDefinition, $value);
        }

        return $attributes;
    }

    /**
     * Returns the transformer to be used for the given cast definition.
     *
     * @param mixed $castDefinition
     * @param TransformerInterface[] $casters
     * @param mixed $value
     * @return TransformerInterface?|\Callable
     */
    private static function findTransformer($castDefinition, $casters, $value)
    {
        if ($castDefinition instanceof \Closure || $castDefinition instanceof TransformerInterface) {
            return $castDefinition;
        }

        return Arr::first($casters, function ($caster) use ($castDefinition, $value) {
            /** @var TransformerInterface $caster */
            return $caster->handles($castDefinition, $value);
        });
    }


    /**
     * Get an attribute from the fluent instance.
     * Updated to support dot notation
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (is_string($key) && Str::contains($key, '.')) {
            return $this->getRecursive($key, null, $default);
        }

        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return value($default);
    }

    /**
     * Recursively get an attribute from the fluent instance using dot notation.
     *
     * @param  string  $key
     * @param  mixed  $offest
     * @param  mixed  $default
     * @return mixed
     */
    protected function getRecursive($key, $offset = null, $default = null)
    {
        if (is_null($offset)) {
            $offset = $this->attributes;
        }

        if (Str::contains($key, '.')) {
            $keys = explode('.', $key);
            $key  = array_shift($keys);
            $next = count($keys) ? implode('.', $keys) : '';

            if ($next) {
                return $this->getRecursive($next, $offset[$key], $default);
            }
        }

        # Using `isset()` to support both arrays & fluent !
        if (isset($offset[$key])) {
            return $offset[$key];
        }

        return value($default);
    }
}
