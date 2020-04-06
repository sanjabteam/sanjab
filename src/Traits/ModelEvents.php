<?php

namespace Sanjab\Traits;

use Illuminate\Database\Eloquent\Model;

trait ModelEvents
{
    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onRetrieved(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onCreating(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onCreated(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onUpdating(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onUpdated(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onSaving(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onSaved(Model $item)
    {
    }

    /**
     * Model event ( not for soft delete ).
     *
     * @param Model $item
     * @return void
     */
    public function onDeleting(Model $item)
    {
    }

    /**
     * Model event ( not for soft delete ).
     *
     * @param Model $item
     * @return void
     */
    public function onDeleted(Model $item)
    {
    }

    /**
     * Model event ( for soft deletes only ).
     *
     * @param Model $item
     * @return void
     */
    public function onSoftDeleting(Model $item)
    {
    }

    /**
     * Model event ( for soft deletes only ).
     *
     * @param Model $item
     * @return void
     */
    public function onSoftDeleted(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onRestoring(Model $item)
    {
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onRestored(Model $item)
    {
    }
}
