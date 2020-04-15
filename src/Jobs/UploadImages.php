<?php

namespace Faithgen\Discussions\Jobs;

use Faithgen\Discussions\Models\Discussion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\ImageManager;

class UploadImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @param  Discussion  $discussion
     * @param  array  $images
     */
    public function __construct(Discussion $discussion, array $images)
    {
        $this->discussion = $discussion;
        $this->images = $images;
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
        foreach ($this->images as $imageData) {
            $fileName = str_shuffle($this->discussion->id.time().time()).'.png';
            $ogSave = storage_path('app/public/discussions/original/').$fileName;
            try {
                $imageManager->make($imageData)->save($ogSave);
                $this->discussion->images()->create([
                    'imageable_id' => $this->discussion->id,
                    'name'         => $fileName,
                ]);
            } catch (\Exception $e) {
            }
        }
    }
}
