<?php

namespace Faithgen\Discussions\Http\Requests;

use Faithgen\Discussions\Services\DiscussionService;
use Faithgen\Discussions\Traits\SavesDiscussion;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    use SavesDiscussion;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param  DiscussionService  $discussionService
     *
     * @return bool
     */
    public function authorize(DiscussionService $discussionService)
    {
        if (! $discussionService->getDiscussion()) {
            return false;
        }

        return $this->user()->can('update', $discussionService->getDiscussion());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'title' => 'required|string',
        ], $this->getSaveRules());
    }
}
