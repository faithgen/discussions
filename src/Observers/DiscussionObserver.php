<?php

namespace Faithgen\Discussions\Observers;

use Faithgen\Discussions\Models\Discussion;
use FaithGen\SDK\Traits\FileTraits;

class DiscussionObserver
{
    use FileTraits;

    /**
     * Handle the discussion "created" event.
     *
     * @param  \Faithgen\Discussions\Models\Discussion  $discussion
     *
     * @return void
     */
    public function created(Discussion $discussion)
    {
        //
    }

    /**
     * Handle the discussion "updated" event.
     *
     * @param  \Faithgen\Discussions\Models\Discussion  $discussion
     *
     * @return void
     */
    public function updated(Discussion $discussion)
    {
        //
    }

    /**
     * Handle the discussion "deleted" event.
     *
     * @param  \Faithgen\Discussions\Models\Discussion  $discussion
     *
     * @return void
     */
    public function deleted(Discussion $discussion)
    {
        if ($discussion->images) {
            $this->deleteFiles($discussion);
        }
    }
}
