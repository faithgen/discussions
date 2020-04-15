<?php

namespace Faithgen\Discussions\Http\Requests;

use Faithgen\Discussions\Models\Discussion;
use Faithgen\Discussions\Traits\SavesDiscussion;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    use SavesDiscussion;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Discussion::class);
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

    public function failedAuthorization()
    {
        if ($user = auth('web')->user()) {
            if ($user->active) {
                $message = 'You cant post a discussion because either your ministry is out of tokens for this month';
            } else {
                $message = 'You are not allowed to posting anything on this app';
            }
        } else {
            $message = 'You are out of credit, consider upgrading your subscription to post more discussions';
        }

        throw new AuthorizationException($message);
    }
}
