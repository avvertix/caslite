<?php

namespace Avvertix\Caslite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Avvertix\Caslite\CasliteManager
 */
class Caslite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Avvertix\Caslite\Contracts\Factory';
    }
}

