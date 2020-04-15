<?php

namespace Faithgen\Discussions\Http\Controllers;

use Faithgen\Discussions\Http\Requests\CreateRequest;
use Faithgen\Discussions\Http\Requests\DeleteRequest;
use Faithgen\Discussions\Http\Requests\UpdateRequest;
use Faithgen\Discussions\Http\Resources\DiscussionList;
use Faithgen\Discussions\Models\Discussion;
use Faithgen\Discussions\Services\DiscussionService;
use FaithGen\SDK\Models\Ministry;
use FaithGen\SDK\Models\User;
use Illuminate\Routing\Controller;
use InnoFlash\LaraStart\Helper;
use InnoFlash\LaraStart\Http\Requests\IndexRequest;
use InnoFlash\LaraStart\Traits\APIResponses;

class DiscussionController extends Controller
{
    use APIResponses;
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
     * Fetches the discussions.
     *
     * @param  IndexRequest  $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $acceptableTypes = [Ministry::class, \App\Models\Ministry::class, User::class];

        $discussions = auth()->user()
            ->ministryDiscussions()
            ->latest()
            ->approved()
            ->with(['images', 'discussable'])
            ->exclude(['discussion'])
            ->search(['url'], $request->filter_text)
            ->orWhereHasMorph('discussable', $acceptableTypes,
                fn($discussable) => $discussable->where('name', 'LIKE', '%'.$request->filter_text.'%'))
            ->paginate(Helper::getLimit($request));

        DiscussionList::wrap('discussions');

        return DiscussionList::collection($discussions);
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
        if (count($request->validated()) === 1) {
            abort(422, 'You can not send a blank discussion!');
        }

        return $this->discussionService->createFromParent($request->validated(),
            'Discussion created successfully!'.(auth('web')->user() ? ' Waiting for admin to approve.' : ''));
    }

    /**
     * Deletes the discussion.
     *
     * @param  Discussion  $discussion
     * @param  DeleteRequest  $request
     *
     * @return mixed
     */
    public function destroy(Discussion $discussion, DeleteRequest $request)
    {
        try {
            $discussion->delete();

            return $this->successResponse('Discussion deleted successfully');
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function update(UpdateRequest $request)
    {
        return $request->all();
    }
}
