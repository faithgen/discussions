<?php

namespace Faithgen\Discussions\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('delete', $this->discussion);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Error to return if deleting is not allowed.
     *
     * @throws AuthorizationException
     */
    public function failedAuthorization()
    {
        throw new AuthorizationException('You are not allowed to delete this discussion');
    }
}
