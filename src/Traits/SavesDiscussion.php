<?php

namespace Faithgen\Discussions\Traits;

use Faithgen\Discussions\Services\DiscussionService;

trait SavesDiscussion
{
    /**
     * Converts image string array to usable string in the validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        if (is_string($this->images)) {
            $this->merge([
                'images' => json_decode($this->images, true),
            ]);
        }
    }

    /**
     * Get the update rules for the given ministry level.
     *
     * @return array
     */
    protected function getSaveRules(): array
    {
        if ($discussion = app(DiscussionService::class)->getDiscussion()) {
            $currentCount = $discussion->images()->count();
        } else {
            $currentCount = 0;
        }

        return [
            'Free'        => [
                'discussion' => 'required|string',
            ],
            'Premium'     => [
                'discussion' => 'string',
                'url'        => 'url',
                'images'     => 'array|max:'.(1 - $currentCount),
                'images.*'   => 'base64image',
            ],
            'PremiumPlus' => [
                'discussion' => 'string',
                'url'        => 'url',
                'images'     => 'array|max:'.(5 - $currentCount),
                'images.*'   => 'base64image',
            ],
        ][auth()->user()->account->level];
    }
}
