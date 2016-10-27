<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * Indicates if filter empty attributes when converting to array.
     *
     * @var bool
     */
    public static $filterAttributes = false;

    /**
     * Indicates if make all attributes visible when converting to array.
     *
     * @var bool
     */
    public static $makeAllAttributesVisible = false;

    /**
     * Filter empty attributes when converting to array.
     *
     * @param  bool  $filter
     * @return void
     */
    public static function filterAttributes($filter = true)
    {
        static::$filterAttributes = $filter;
    }

    /**
     * Make all attributes visible when converting to array.
     *
     * @param  bool  $visible
     * @return [type]
     */
    public static function makeAllAttributesVisible($visible = true)
    {
        static::$makeAllAttributesVisible = $visible;
    }

    /**
     * Convert the model's attributes to an array.
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if (static::$filterAttributes) {
            $attributes = array_filter($attributes);
        }

        return $attributes;
    }

    /**
     * Get an attribute array of all arrayable values.
     *
     * @param  array  $values
     * @return array
     */
    protected function getArrayableItems(array $values)
    {
        if (static::$makeAllAttributesVisible) {
            return $values;
        }

        return parent::getArrayableItems($values);
    }

    /**
     * Return a timestamp as unix timestamp.
     *
     * @param  mixed  $value
     * @return int
     */
    protected function asTimeStamp($value)
    {
        $timestamp = parent::asTimeStamp($value);

        return $timestamp > 0 ? $timestamp : 0;
    }
}
