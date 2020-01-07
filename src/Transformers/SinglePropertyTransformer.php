<?php

namespace JanPantel\LaravelFluentPlus\Transformers;

abstract class SinglePropertyTransformer implements TransformerInterface
{
    public function handles($castDefinition, $value)
    {
        return true;
    }
}