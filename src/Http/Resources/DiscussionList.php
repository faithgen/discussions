<?php

namespace Faithgen\Discussions\Http\Resources;

use FaithGen\SDK\Helpers\ImageHelper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use InnoFlash\LaraStart\Helper;

class DiscussionList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if ($is_admin = Str::of($this->discussable_type)->contains('Ministry')) {
            $avatar =
                ImageHelper::getImage('profile', $this->discussable->image, config('faithgen-sdk.ministries-server'));
        } else {
            $avatar = ImageHelper::getImage('users', $this->discussable->image, config('faithgen-sdk.users-server'));
        }

        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'approved' => (bool) $this->approved,
            'creator'  => [
                'id'       => $this->discussable_id,
                'name'     => $this->discussable->name,
                'is_admin' => $is_admin,
                'avatar'   => $avatar,
            ],
            'date'     => Helper::getDates($this->created_at),
        ];
    }
}
