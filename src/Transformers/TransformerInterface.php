<?php

namespace JanPantel\LaravelFluentPlus\Transformers;

interface TransformerInterface
{
    /**
     * Determines whether this transformer should handle a property.
     *
     * @param mixed $castDefinition
     * @param mixed $value
     * @return boolean
     */
    public function handles($castDefinition, $value);

    /**
     * Transform the given value.
     *
     * @param mixed $castDefinition
     * @param mixed $value
     * @return mixed
     */
    public function transform($castDefinition, $value);
}