<?php

/**
 * @var $model \open20\amos\events\models\EventLandingProtagonist
 */

$image = $model->image;
$url = '/img/img_default.jpg';
if ($image) {
    $url = $image->getWebUrl('square_medium', false, true);
}
?>

<div class="uk-first-column">
    <div class="uk-text-center speaker">
        <div class="uk-inline-clip uk-transition-toggle uk-light" tabindex="0">
            <amp-img src="<?= $url ?>" height="300" width="300" layout="responsive" class="el-image img"></amp-img>
            <div class="text-protagonist">
                <h4><?= $model->name . ' ' . $model->surname ?></h4>
                <p><?= $model->company ?></p>
            </div>
        </div>
    </div>


</div>