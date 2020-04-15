<?php

namespace Faithgen\Discussions\Policies;

use Faithgen\Discussions\Models\Discussion;
use FaithGen\SDK\Models\Ministry;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscussionPolicy
{
    use HandlesAuthorization;

    private array $denominations = [
        'Free'        => 2,
        'Premium'     => 10000,
        'PremiumPlus' => 100000,
    ];

    /**
     * Decides whether a discussion has got to be created or not.
     *
     * @param  Ministry  $ministry
     *
     * @return bool
     */
    public function create(Ministry $ministry)
    {
        $canCreate = $ministry->ministryDiscussions()
                ->whereBetween('created_at', [now()->firstOfMonth(), now()->lastOfMonth()])
                ->count() <= $this->denominations[$ministry->account->level];

        if ($user = auth('web')->user()) {
            return $canCreate && $user->active;
        }

        return $canCreate;
    }

    /**
     * Authorizes viewing this discussion.
     *
     * @param  Ministry  $ministry
     * @param  Discussion  $discussion
     *
     * @return bool
     */
    public function view(Ministry $ministry, Discussion $discussion)
    {
        return $ministry->id === $discussion->ministry_id;
    }

    /**
     * Decides whether or not a discussion should be deleted.
     *
     * @param  Ministry  $ministry
     * @param  Discussion  $discussion
     *
     * @return bool
     */
    public function delete(Ministry $ministry, Discussion $discussion)
    {
        return $this->canTransact($ministry, $discussion);
    }

    /**
     * Decides whether or not to update this discussion.
     *
     * @param  Ministry  $ministry
     * @param  Discussion  $discussion
     *
     * @return bool
     */
    public function update(Ministry $ministry, Discussion $discussion)
    {
        return $this->canTransact($ministry, $discussion);
    }

    /**
     * Checks whether or not to transact on the given discussion.
     *
     * @param  Ministry  $ministry
     * @param  Discussion  $discussion
     *
     * @return bool
     */
    private function canTransact(Ministry $ministry, Discussion $discussion)
    {
        if ($user = auth('web')->user()) {
            return $ministry->id === $discussion->ministry_id
                && auth('web')->user()->id === $discussion->discussable_id;
        }

        return $ministry->id === $discussion->ministry_id;
    }
}
