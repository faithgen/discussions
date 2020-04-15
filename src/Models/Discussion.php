<?php

namespace Faithgen\Discussions\Models;

use FaithGen\SDK\Models\UuidModel;
use FaithGen\SDK\Traits\Relationships\Belongs\BelongsToMinistryTrait;
use FaithGen\SDK\Traits\Relationships\Morphs\CommentableTrait;
use FaithGen\SDK\Traits\Relationships\Morphs\ImageableTrait;

class Discussion extends UuidModel
{
    use BelongsToMinistryTrait;
    use CommentableTrait;
    use ImageableTrait;

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
}
