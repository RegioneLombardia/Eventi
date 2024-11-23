<?php

namespace app\assets;

/**
 * Landing Cms Asset File.
 */
class SimplePaginationAsset extends \luya\web\Asset
{
    public $sourcePath = '@bower/everlaat-jquery-simplepagination';

    public $css = [
        'simplePagination.css',
    ];

    public $js = [
        'jquery.simplePagination.js',
    ];

    public $depends = [
        //
    ];
}
