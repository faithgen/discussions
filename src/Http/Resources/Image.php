<?php

namespace Faithgen\Discussions\Http\Resources;

use FaithGen\SDK\Helpers\ImageHelper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use InnoFlash\LaraStart\Helper;

class Image extends JsonResource
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
        if (Str::of($this->imageable->discussable_type)->contains('Ministry')) {
            $server = config('faithgen-sdk.ministries-server');
        } else {
            $server = config('faithgen-sdk.users-server-server');
        }

        return [
            'id'       => $this->id,
            'caption'  => $this->caption,
            'comments' => $this->comments()->count(),
            'avatar'   => ImageHelper::getImage('discussions', $this->resource, $server),
            'date'     => Helper::getDates($this->created_at),
        ];
    }
}
