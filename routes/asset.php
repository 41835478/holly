<?php

Route::get('images/{template}/{filename}', [
    'uses' => '\App\Support\Intervention\Image\ImageCacheController@getResponse',
    'as' => 'image',
])->where(['filename' => '[ \w\\.\\/\\-\\@]+']);
