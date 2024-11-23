<?php
/*
 * To change this proscription header, choose Proscription Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\assets;

use luya\web\Asset;
use Yii;

/**
 * Description of SocialAsset
 *
 */
class SmartAppAsset extends Asset
{
    public $sourcePath     = '@app/resources/smartapp';
    public $css            = [
        'css/smartbanner.css'
    ];
    public $js             = [
        'js/smartbanner.js'
    ];
    public $publishOptions = [
        'only' => [
            'css/*',
            'js/*',
        ]
    ];
    public $depends        = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];

}