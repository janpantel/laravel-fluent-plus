<?php

namespace JanPantel\LaravelFluentPlus\Transformers;

class ObjectTransformer implements TransformerInterface
{
    /**
     * @inheritDoc
     */
    public function handles($castDefinition, $value)
    {
        return is_string($castDefinition) && class_exists($castDefinition);
    }

    /**
     * @inheritDoc
     */
    public function transform($castDefinition, $value)
    {
        return new $castDefinition($value);
    }
}