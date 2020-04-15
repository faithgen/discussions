<?php

namespace Faithgen\Discussions\Http\Requests;

use Faithgen\Discussions\Models\Discussion;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Request rules.
     *
     * @var array
     */
    private array $denominations = [
        'Free'        => [
            'title'      => 'required|string',
            'discussion' => 'required|string',
        ],
        'Premium'     => [
            'title'      => 'required|string',
            'discussion' => 'string',
            'url'        => 'url',
            'images'     => 'array|max:1',
            'images.*'   => 'base64image',
        ],
        'PremiumPlus' => [
            'title'      => 'required|string',
            'discussion' => 'string',
            'url'        => 'url',
            'images'     => 'array|max:5',
            'images.*'   => 'base64image',
        ],
    ];

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
        return $this->denominations[auth()->user()->account->level];
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
}
