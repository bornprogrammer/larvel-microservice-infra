<?php

/** @noinspection ALL */

namespace Laravel\Infrastructure\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait UUIDTrait
{
    /**
     * Override the boot function from Laravel so that
     * we give the model a new UUID when we create it.
     */
    //    protected static function boot()
    //    {
    //        parent::boot();
    //
    //        $creationCallback = function ($model) {
    //            if (empty($model->{$model->getKeyName()})) {
    //                $model->{$model->getKeyName()} = Str::uuid()->toString();
    //            }
    //        };
    //
    //        static::creating($creationCallback);
    //    }

    public function writeUUID(Model $model)
    {
        if (empty($model->{$model->getKeyName()})) {
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        }
    }


    /**
     * Override the getIncrementing() function to return false to tell
     * Laravel that the identifier does not auto increment (it's a string).
     *
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }


    /**
     * Tell laravel that the key type is a string, not an integer.
     *
     * @return string
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
