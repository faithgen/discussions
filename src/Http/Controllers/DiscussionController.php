<?php

namespace Faithgen\Discussions\Http\Controllers;

use Faithgen\Discussions\Http\Requests\CreateRequest;
use Faithgen\Discussions\Services\DiscussionService;
use Illuminate\Routing\Controller;

class DiscussionController extends Controller
{
    /**
     * @var DiscussionService
     */
    private DiscussionService $discussionService;

    /**
     * DiscussionController constructor.
     *
     * @param  DiscussionService  $discussionService
     */
    public function __construct(DiscussionService $discussionService)
    {
        $this->discussionService = $discussionService;
    }

    /**
     * Creates a discussion.
     *
     * @param  CreateRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateRequest $request)
    {
        if (! count($request->validated())) {
            abort(422, 'You can not send a blank discussion!');
        }

        return $this->discussionService->createFromParent($request->validated(),
            'Discussion created successfully!'. (auth('web')->user() ? ' Waiting for admin to approve.' : ''));
    }
}
