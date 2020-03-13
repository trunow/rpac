<?php

namespace Trunow\Rpac\Example;

class PostPolicy extends \Trunow\Rpac\Policies\RpacPolicy
{

    protected $relationships = ['author'];

    /**
     * Policy need to know Model it works with
     * @return string
     */
    protected function model()
    {
        Post::class;
    }
}