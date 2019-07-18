<?php

namespace Sanjab;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sanjab\Sanjab\Skeleton\SkeletonClass
 */
class SanjabFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sanjab';
    }
}
