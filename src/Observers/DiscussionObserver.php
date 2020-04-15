<?php

namespace Faithgen\Discussions\Observers;

use Faithgen\Discussions\Jobs\ProcessImages;
use Faithgen\Discussions\Jobs\S3Upload;
use Faithgen\Discussions\Jobs\UploadImages;
use Faithgen\Discussions\Models\Discussion;
use FaithGen\SDK\Models\User;
use FaithGen\SDK\Traits\FileTraits;

class DiscussionObserver
{
    use FileTraits;

    private array $levels = [
        'Free'        => false,
        'Premium'     => true,
        'PremiumPlus' => true,
    ];

    /**
     * Handle the discussion "created" event.
     *
     * @param  \Faithgen\Discussions\Models\Discussion  $discussion
     *
     * @return void
     */
    public function created(Discussion $discussion)
    {
        if ($this->levels[auth()->user()->account->level] && request()->has('images')) {
            if (is_string(request('images'))) {
                $images = json_decode(request('images'), true);
            } else {
                $images = request('images');
            }

            UploadImages::withChain([
                new ProcessImages(),
                new S3Upload(),
            ])
                ->dispatch($discussion, $images);
        }
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

    /**
     * Converts data when a discussion is being created.
     *
     * @param  Discussion  $discussion
     */
    public function creating(Discussion $discussion)
    {
        if ($user = auth('web')->user()) {
            $discussion->discussable_type = User::class;
            $discussion->discussable_id = $user->id;
        } else {
            $discussion->discussable_type = get_class(auth()->user());
            $discussion->discussable_id = auth()->user()->id;
            $discussion->approved = true;
        }
    }
}
