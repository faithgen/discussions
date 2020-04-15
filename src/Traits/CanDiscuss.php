<?php

namespace Faithgen\Discussions\Traits;

use Faithgen\Discussions\Models\Discussion;

trait CanDiscuss
{
    /**
     * Gives a model ability to discuss.
     *
     * @return mixed
     */
    public function discussions()
    {
        return $this->morphMany(Discussion::class, 'discussable');
    }
}
