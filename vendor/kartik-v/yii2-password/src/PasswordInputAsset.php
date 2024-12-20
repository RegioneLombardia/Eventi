<?php

/**
 * @package   yii2-password
 * @version   1.5.7
 */

namespace kartik\password;

use kartik\base\AssetBundle;

/**
 * Asset bundle for the [[PasswordInput]] widget.
 *
 * @since 1.0
 */
class PasswordInputAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath('@vendor/kartik-v/strength-meter');
        $this->setupAssets('css', ['css/strength-meter']);
        $this->setupAssets('js', ['js/strength-meter']);
        parent::init();
    }
}