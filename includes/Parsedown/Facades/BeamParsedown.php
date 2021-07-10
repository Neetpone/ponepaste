<?php

namespace ArdiSSoebrata\BeamParsedown\Facades;

use Illuminate\Support\Facades\Facade;

class BeamParsedown extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'beam-parsedown';
    }
}