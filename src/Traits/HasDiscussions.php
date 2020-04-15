<?php

namespace Faithgen\Discussions\Traits;

use Faithgen\Discussions\Models\Discussion;

trait HasDiscussions
{
    /**
     * Gives a ministry many discussions.
     *
     * @return mixed
     */
    public function ministryDiscussions()
    {
        return $this->hasMany(Discussion::class);
    }
}
