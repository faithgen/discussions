<?php

namespace Faithgen\Discussions\Http\Requests;

use Faithgen\Discussions\Services\DiscussionService;
use FaithGen\SDK\Helpers\Helper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
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

        return $this->user()->can('view', $discussionService->getDiscussion());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'discussion_id' => Helper::$idValidation,
            'comment'       => 'required|string',
        ];
    }

    public function failedAuthorization()
    {
        throw new AuthorizationException('You are not allowed to comment on this discussion');
    }
}
