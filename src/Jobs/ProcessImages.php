<?php

namespace Faithgen\Discussions\Jobs;

use Faithgen\Discussions\Models\Discussion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\ImageManager;

class ProcessImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;
    /**
     * @var Discussion
     */
    private Discussion $discussion;

    /**
     * Create a new job instance.
     *
     * @param  Discussion  $discussion
     */
    public function __construct(Discussion $discussion)
    {
        $this->discussion = $discussion;
    }

    /**
     * Execute the job.
     *
     * @param  ImageManager  $imageManager
     *
     * @return void
     */
    public function handle(ImageManager $imageManager)
    {
        foreach ($this->discussion->images as $image) {
            try {
                $ogImage = storage_path('app/public/discussions/original/').$image->name;
                $thumb100 = storage_path('app/public/discussions/100-100/').$image->name;

                $imageManager->make($ogImage)->fit(100, 100, function ($constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                }, 'center')->save($thumb100);
            } catch (\Exception $e) {
            }
        }
    }
}
