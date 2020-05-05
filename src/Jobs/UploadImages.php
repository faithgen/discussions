<?php

namespace Faithgen\Discussions\Jobs;

use Faithgen\Discussions\Models\Discussion;
use FaithGen\SDK\Traits\UploadsImages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\ImageManager;

class UploadImages implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        UploadsImages;

    public bool $deleteWhenMissingModels = true;
    /**
     * @var Discussion
     */
    private Discussion $discussion;
    /**
     * @var array
     */
    private array $images;

    /**
     * Create a new job instance.
     *
     * @param Discussion $discussion
     * @param array $images
     */
    public function __construct(Discussion $discussion, array $images)
    {
        $this->discussion = $discussion;
        $this->images = $images;
    }

    /**
     * Execute the job.
     *
     * @param ImageManager $imageManager
     *
     * @return void
     */
    public function handle(ImageManager $imageManager)
    {
        $this->uploadImages($this->discussion, $this->images, $imageManager);
    }
}
