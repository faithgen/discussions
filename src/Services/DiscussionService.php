<?php

namespace Faithgen\Discussions\Services;

use Faithgen\Discussions\Models\Discussion;
use Illuminate\Database\Eloquent\Model as ParentModel;
use InnoFlash\LaraStart\Services\CRUDServices;

class DiscussionService extends CRUDServices
{
    private Discussion $discussion;

    public function __construct(Discussion $discussion)
    {
        if (request()->has('discussion_id')) {
            $this->discussion = Discussion::findOrFail(request('discussion_id'));
        } else {
            $this->discussion = $discussion;
        }
    }

    /**
     * Retrieves an instance of discussion.
     */
    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }

    /**
     * Makes a list of fields that you do not want to be sent
     * to the create or update methods.
     * Its mainly the fields that you do not have in the discussions table.
     */
    public function getUnsetFields()
    {
        return ['discussion_id'];
    }

    /**
     * This returns the model found in the constructor.
     * or an instance of the class if no discussion is found.
     */
    public function getModel()
    {
        return $this->getDiscussion();
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
