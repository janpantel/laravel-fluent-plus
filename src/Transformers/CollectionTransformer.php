<?php

namespace JanPantel\LaravelFluentPlus\Transformers;

use Illuminate\Support\Collection;

class CollectionTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public function handles($castDefinition, $value)
    {
        return is_array($castDefinition) && count($castDefinition) == 2 && $castDefinition[0] == Collection::class;
    }

    /**
     * @inheritDoc
     */
    public function transform($castDefinition, $value)
    {
        return (new Collection($value))->mapInto($castDefinition[1]);
    }
}