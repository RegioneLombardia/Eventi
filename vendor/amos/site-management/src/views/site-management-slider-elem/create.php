<?php

use amos\sitemanagement\Module;

/**
 * @var yii\web\View $this
 * @var \amos\sitemanagement\models\SiteManagementSliderElem $model
 * @var $slider \amos\sitemanagement\models\SiteManagementSlider
 */
$tipoElemento = $_GET['slider_type'];

if($tipoElemento == 1) {
    $this->title = Yii::t('amossitemanagement', 'Aggiungi un\'immagine alla Galleria di') . ' "' . $slider->title . '"';
} else if($tipoElemento == 2) {
    $this->title = Yii::t('amossitemanagement', 'Aggiungi un video alla Galleria di') . ' "' . $slider->title . '"';
} else if($tipoElemento == 3) {
    $this->title = Yii::t('amossitemanagement', 'Aggiungi un video Instagram alla Galleria di') . ' "' . $slider->title . '"';
} else {
    $this->title = Yii::t('amossitemanagement', 'Create slider element') . ' "' . $slider->title . '"';
}
$externalSessionPreviousUrl = Yii::$app->session->get(Module::externalPreviousUrlSessionKey());
$externalSessionPreviousTitle = Yii::$app->session->get(Module::externalPreviousTitleSessionKey());
if (!is_null($externalSessionPreviousUrl)) {
    $this->params['breadcrumbs'][] = ['label' => $externalSessionPreviousTitle, 'url' => $externalSessionPreviousUrl];
} else {
    $this->params['breadcrumbs'][] = ['label' => Module::t('amossitemanagement', 'Site Management Slider Elem'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-management-slider-elem-create">
    <?= $this->render('_form', [
        'model' => $model,
        'slider' => $slider,
        'files' => $files,
        'useCrop' => $useCrop,
        'ratioCrop' => $ratioCrop,
        'onlyImages' => $onlyImages,
        'onlyVideos' => $onlyVideos,
        'onlyInstagramVideos' => $onlyInstagramVideos
    ]) ?>

</div>
