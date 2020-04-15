<?php

namespace Faithgen\Discussions\Http\Resources;

use FaithGen\Gallery\Http\Resources\Image;

class Discussion extends DiscussionList
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'discussion' => $this->discussion,
            'url'        => $this->url,
            'comments'   => [
                'count' => $this->comments()->count(),
            ],
            'images'     => Image::collection($this->images),
        ]);
    }
}
