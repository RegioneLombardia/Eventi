<?php

use app\assets\ResourcesAsset;
use app\modules\cms\helpers\CmsHelper;
use open20\amos\news\AmosNews;
use luya\helpers\Url;
use yii\helpers\Html;

$resourceAsset = ResourcesAsset::register($this);
$truncate = 250;
?>

<?php $route = \open20\amos\events\utility\EventsUtility::getUrlLanding($model) ?>



<div class="content-evento-megamenu">

  <div class="img-evento">
    <?php
    $url = $model->getMainImageEvent();
    $contentImage = Html::img($url, [
      'class' => 'img-responsive',
      'alt' => \Yii::t('app', 'Immagine delle evento: ') . $model->getTitle()
    ]);
    // $contentImageLink = Html::a($contentImage, $route, ['title' => 'Leggi tutto']);
    ?>
    <?= $contentImage; ?>
  </div>
  <div class="text-evento">
    <div class="tit-evento">
    <a href="<?= $route ?>"  title="Visualizza dettaglio evento: <?= $model->getTitle(); ?>">
    <?= $model->getTitle(); ?>
    </a>
  </div>
    <div class="data-evento">
      <?php $beginDate = new \DateTime($model->begin_date_hour) ?>
      <?php if (!empty($model->begin_date_hour)) {
        $endDate = new \DateTime($model->end_date_hour) ?>
        <?= \Yii::t('app', "dal") . ' ' . $beginDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y') ?>
      <?php } else { ?>
        <?= $beginDate->format('d/m/Y H:i') ?>
      <?php } ?>
    </div>
  </div>


</div>