<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   ModuleAssetsAsset
 */

namespace open20\webmeeting\assets;

use yii\web\AssetBundle;

/**
 * WebMeetingAssets
 * Define assets dependency of WebMeetingModule
 * 
 */
class WebMeetingInviteeAssets extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/open20/webmeeting/src/assets';
    
    /**
     * @inheritdoc
     */
    public $css = [
        'less/webmeeting.less'
    ];
    
    /**
     * @inheritdoc
     */
    public $js = [
        'js/invitee.js'
    ];
    
    /**
     * @inheritdoc
     */
    public $depends = [];
}
