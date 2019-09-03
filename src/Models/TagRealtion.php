<?php

namespace Sanjab\Models;

/**
 * Support storing tags using eloquent relation instead of simple text.
 *
 * @property string $tagModel           Model class name of tag
 * @property string $tagName            Tag name field
 * @property string $tagPivotTable      Tags pivot table
 * @property string $tagForeignPivotKey Tag foreign pivot key
 * @property string $tags               tags as string
 * @property array $tags_array          tags as array
 */
trait TagRelation
{
    /**
     * Stroing tags then saving them help to ignore error when Model is not saved in database yet.
     *
     * @var array
     */
    protected $cachedTags = [];

    /**
     * Tag saving event submited before or not.
     *
     * @var boolean
     */
    protected static $tagEventHandlerInit = false;

    /**
     * Tags this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tagModels()
    {
        return $this->belongsToMany($this->tagModel ?? 'App\Tag', $this->tagPivotTable ?? null, $this->tagForeignPivotKey ?? null);
    }

    /**
     * Get tags as array
     *
     * @return array
     */
    public function getTagsArrayAttribute()
    {
        $this->loadMissing('tagModels');
        return $this->tagModels->pluck($this->tagName ?? 'name')->toArray();
    }

    /**
     * Set tags from array of names.
     *
     * @param string[] $tags
     * @return void
     */
    public function setTagsArrayAttribute($tags)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        if (is_array($tags) && count($tags) > 0) {
            $this->cachedTags = $tags;
            if (! static::$tagEventHandlerInit) {
                static::saved(function ($post) {
                    $tags = [];
                    foreach ($post->getCachedTags() as $tag) {
                        $tags[] = ($this->tagModel ?? 'App\Tag')::where($this->tagName ?? 'name', $tag)->firstOrCreate([$this->tagName ?? 'name' => $tag])->id;
                    }
                    $post->tagModels()->sync($tags);
                });
                static::$tagEventHandlerInit = true;
            }
        }
    }

    /**
     * Get tags as string.
     *
     * @return string
     */
    public function getTagsAttribute()
    {
        return implode(',', $this->tags_array);
    }

    /**
     * Set tags as string
     *
     * @param string $tags
     * @return void
     */
    public function setTagsAttribute($tags)
    {
        $this->setTagsArrayAttribute($tags);
    }

    /**
     * Get the value of cachedTags
     *
     * @return array
     */
    public function getCachedTags()
    {
        return $this->cachedTags;
    }
}
