<?php

namespace Faithgen\Discussions\Http\Controllers;

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
}
