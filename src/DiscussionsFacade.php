<?php

namespace Faithgen\Discussions;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Faithgen\Discussions\Skeleton\SkeletonClass
 */
class DiscussionsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'discussions';
    }
}
