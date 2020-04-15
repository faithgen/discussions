<?php

namespace Faithgen\Discussions\Models;

use FaithGen\SDK\Models\UuidModel;
use FaithGen\SDK\Traits\Relationships\Belongs\BelongsToMinistryTrait;
use FaithGen\SDK\Traits\Relationships\Morphs\CommentableTrait;
use FaithGen\SDK\Traits\Relationships\Morphs\ImageableTrait;
use FaithGen\SDK\Traits\StorageTrait;
use FaithGen\SDK\Traits\TitleTrait;

class Discussion extends UuidModel
{
    use BelongsToMinistryTrait;
    use CommentableTrait;
    use ImageableTrait;
    use StorageTrait;
    use TitleTrait;

    protected $table = 'fg_discussions';

    /**
     * Relates this discussion to models using it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function discussable()
    {
        return $this->morphTo();
    }

    /**
     * The directory name for the folder holding images.
     *
     * @return string
     */
    public function filesDir()
    {
        return 'discussions';
    }

    /**
     * The name(s) of image(s).
     *
     * @return mixed
     */
    public function getFileName()
    {
        return $this->images()
            ->pluck('name')
            ->toArray();
    }

    /**
     * The dimensions the discussion images has.
     *
     * @return array
     */
    public function getImageDimensions()
    {
        return [0, 100];
    }

    /**
     * Returns only approved discussions if being called from the app API.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeApproved($query)
    {
        if (auth('web')->user()) {
            return $query->whereApproved(true);
        }

        return $query;
    }
}
