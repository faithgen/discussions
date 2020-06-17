<?php

namespace Faithgen\Discussions\Services;

use Faithgen\Discussions\Models\Discussion;
use InnoFlash\LaraStart\Services\CRUDServices;

class DiscussionService extends CRUDServices
{
    protected Discussion $discussion;

    public function __construct()
    {
        $this->discussion = new Discussion();

        if (request()->has('discussion_id')) {
            $this->discussion = Discussion::findOrFail(request('discussion_id'));
        }

        if (request()->route('discussion')) {
            $this->discussion = request()->route('discussion');
        }
    }

    /**
     * Retrieves an instance of discussion.
     *
     * @return \Faithgen\Discussions\Models\Discussion
     */
    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }

    /**
     * Makes a list of fields that you do not want to be sent
     * to the create or update methods.
     * Its mainly the fields that you do not have in the messages table.
     *
     * @return array
     */
    public function getUnsetFields(): array
    {
        return ['discussion_id'];
    }

    /**
     * Attaches a parent to the current discussion.
     * You can delete this if you do not intent to create discussions from parent relationships.
     */
    public function getParentRelationship()
    {
        return auth()->user()->ministryDiscussions();
    }
}
