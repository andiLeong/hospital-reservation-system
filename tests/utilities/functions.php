<?php


function create($class, $attributes = [], $times = null)
{
    return $class::factory($times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}
