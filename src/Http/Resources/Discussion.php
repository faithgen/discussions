<?php

namespace Faithgen\Discussions\Http\Resources;

class Discussion extends DiscussionList
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'discussion' => $this->discussion,
        ]);
    }
}
