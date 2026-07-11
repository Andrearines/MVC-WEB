<?php

namespace app\Core;

abstract class Plugin
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function init();
}
