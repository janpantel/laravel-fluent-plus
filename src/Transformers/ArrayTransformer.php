<?php

namespace JanPantel\LaravelFluentPlus\Transformers;

class ArrayTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public function handles($castDefinition, $value)
    {
        return is_array($castDefinition) && count($castDefinition) == 1;
    }

    /**
     * @inheritDoc
     */
    public function transform($castDefinition, $value)
    {
        return array_map(function ($value) use ($castDefinition) {
            return new $castDefinition[0]($value);
        }, $value);
    }
}