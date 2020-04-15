<?php

namespace Faithgen\Discussions\Policies;

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
}
