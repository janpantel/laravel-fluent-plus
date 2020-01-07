<?php

namespace JanPantel\LaravelFluentPlus;


class FluentPlus extends StatelessFluentPlus
{
    protected $casts = [];
    protected $recursive = true;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    protected function getCasts()
    {
        return $this->casts;
    }

    protected function isRecursive()
    {
        return $this->recursive;
    }
}