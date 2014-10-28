<?php

namespace Ravloony\LaravelTransduct\Facades;

use Illuminate\Support\Facades\Facade;

class Transduct extends Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'transduct'; }

}
