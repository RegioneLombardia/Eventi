<?php

use app\assets\ResourcesAsset;
use app\modules\cms\helpers\CmsHelper;

$resourceAsset = ResourcesAsset::register($this);


$truncate = 250;
?>
<div class="uk-text-center speaker">
    <div class="uk-inline-clip uk-transition-toggle uk-light" tabindex="0">
        <?php
        $image = $model->image;
        if (!is_null($image)) {
            echo $contentImage = CmsHelper::img(
                $image->getWebUrl('square_medium', false, true),
                [
                    'class' => 'el-image img',
                    'alt' => $model->name . $model->surname
                ]
            );
        } else {
            echo $contentImage = CmsHelper::img(\Yii::$app->params['platform']['backendUrl'].'/img/defaultProfiloM.png', [
                'class' => 'el-image img',
                'alt' => $model->name . $model->surname
            ]);
        }
        ?>
        <div class="uk-position-center">
            <div class="uk-transition-slide-top-small">
                <h4 class="uk-margin-small-bottom"><strong><?= $model->name . ' ' . $model->surname ?></strong></h4>
            </div>
            <div class="uk-transition-slide-bottom-small">
                <h4 class="uk-margin-remove"><?= $model->company ?></h4>
            </div>
        </div>
    </div>

</div>
