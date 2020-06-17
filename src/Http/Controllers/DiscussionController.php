<?php

namespace Faithgen\Discussions\Http\Controllers;

use Faithgen\Discussions\Http\Requests\CommentRequest;
use Faithgen\Discussions\Http\Requests\CreateRequest;
use Faithgen\Discussions\Http\Requests\DeleteImageRequest;
use Faithgen\Discussions\Http\Requests\DeleteRequest;
use Faithgen\Discussions\Http\Requests\UpdateRequest;
use Faithgen\Discussions\Http\Resources\Discussion as DiscussionResource;
use Faithgen\Discussions\Http\Resources\DiscussionList;
use Faithgen\Discussions\Models\Discussion;
use Faithgen\Discussions\Services\DiscussionService;
use FaithGen\SDK\Helpers\CommentHelper;
use FaithGen\SDK\Models\Image;
use FaithGen\SDK\Models\Ministry;
use FaithGen\SDK\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InnoFlash\LaraStart\Helper;
use InnoFlash\LaraStart\Http\Requests\IndexRequest;
use InnoFlash\LaraStart\Traits\APIResponses;

class DiscussionController extends Controller
{
    use APIResponses;
    use AuthorizesRequests;

    /**
     * @var DiscussionService
     */
    private DiscussionService $discussionService;

    /**
     * DiscussionController constructor.
     *
     * @param DiscussionService $discussionService
     */
    public function __construct(DiscussionService $discussionService)
    {
        $this->discussionService = $discussionService;
    }

    /**
     * Fetches the discussions.
     *
     * @param IndexRequest $request
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
            ->with(['discussable.image'])
            ->exclude(['discussion'])
            ->withCount('comments')
            ->where(function ($query) use ($request, $acceptableTypes) {
                return $query->search(['url'], $request->filter_text)
                    ->orWhereHasMorph('discussable', $acceptableTypes,
                        fn ($discussable) => $discussable->where('name', 'LIKE', '%'.$request->filter_text.'%'));
            })->paginate(Helper::getLimit($request));

        DiscussionList::wrap('discussions');

        return DiscussionList::collection($discussions);
    }

    /**
     * Creates a discussion.
     *
     * @param CreateRequest $request
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
     * @param Discussion $discussion
     * @param DeleteRequest $request
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

    /**
     * Updates the discussion.
     *
     * @param UpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(UpdateRequest $request)
    {
        return $this->discussionService->update($request->validated(), 'Discussion updated successfully!');
    }

    /**
     * Shows the discussion in detail.
     *
     * @param Discussion $discussion
     *
     * @return DiscussionResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Discussion $discussion)
    {
        $this->authorize('view', $discussion);

        $discussion->load([
            'images',
            'discussable.image',
        ]);

        DiscussionResource::withoutWrapping();

        return new DiscussionResource($discussion);
    }

    /**
     * Fetch discussion comments.
     *
     * @param Request $request
     * @param Discussion $discussion
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function comments(Request $request, Discussion $discussion)
    {
        $this->authorize('view', $discussion);

        return CommentHelper::getComments($discussion, $request);
    }

    /**
     * Creates a comment for a discussion.
     *
     * @param CommentRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function comment(CommentRequest $request)
    {
        return CommentHelper::createComment($this->discussionService->getDiscussion(), $request);
    }

    /**
     * Deletes an image from a discussion.
     *
     * @param DeleteImageRequest $request
     * @param Discussion $discussion
     * @param Image $image
     *
     * @return mixed
     * @throws \Exception
     */
    public function deleteImage(DeleteImageRequest $request, Discussion $discussion, Image $image)
    {
        try {
            unlink(storage_path('app/public/discussions/100-100/'.$image->name));
            unlink(storage_path('app/public/discussions/original/'.$image->name));
        } catch (\Exception $e) {
            //abort(500, $e->getMessage());
        } finally {
            $image->delete();

            return $this->successResponse('Image deleted!');
        }
    }

    /**
     * Changes the discussion status.
     *
     * @param Discussion $discussion
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function toggleStatus(Discussion $discussion)
    {
        $this->authorize('update', $discussion);

        $discussion->approved = ! $discussion->approved;
        $discussion->save();

        return $this->successResponse('Discussion state changed');
    }

    /**
     * Get the discussions raised by a user.
     *
     * @param $user_id
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userDiscussions($user_id, IndexRequest $request)
    {
        $userModel = config('auth.providers.users.model');
        $user = $userModel::findOrFail($user_id);

        $discussions = $user->discussions()
            ->latest()
            ->with(['discussable.image'])
            ->exclude(['discussion'])
            ->withCount('comments')
            ->search(['url'], $request->filter_text)
            ->where('ministry_id', auth()->user()->id)
            ->where(function ($query) use ($user) {
                if (auth('web')->user() && $user->id !== auth('web')->user()->id) {
                    return $query->approved();
                }

                return $query;
            })->paginate(Helper::getLimit($request));

        DiscussionList::wrap('discussions');

        return DiscussionList::collection($discussions);
    }
}
