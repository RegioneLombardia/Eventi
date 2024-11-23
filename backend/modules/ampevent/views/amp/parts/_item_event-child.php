<?php

use open20\amos\events\AmosEvents;

/**
 * @var $model \open20\amos\events\models\Event
 */

$urlImg = $model->getMainImageEvent();
$urlView = \open20\amos\events\utility\EventsUtility::getUrlLanding($model) . '/amp';
$eventLocation = $model->eventLocation;
$highlight = \open20\amos\events\models\EventHighlights::find()->andWhere(['event_id' => $model->id])->orderBy('id DESC')->count();

$class  = \open20\amos\events\utility\EventsUtility::getBackgroundClassCmsTemplate($model, $templates);


?>
<div class="col-md-4 item-child">
  <a href="<?= $urlView ?>" title="Visualizza dettaglio evento: <?= $model->title ?>">
    <amp-img src="<?= $urlImg ?>" layout="responsive" width="16" height="9"></amp-img>
    <div class="title-event-child"><?= $model->title ?></div>
    <?php $beginDate = new \DateTime($model->begin_date_hour) ?>
    <?php if (!empty($model->begin_date_hour)) {
      $endDate = new \DateTime($model->end_date_hour) ?>
      <span><?= \Yii::t('app', "dal") . ' ' . $beginDate->format('d.m.Y - H:i') . '</span><span>' . ' al ' . $endDate->format('d.m.Y - H:i') ?></span>
    <?php } else { ?>
      <?= $beginDate->format('d.m.Y H:i') ?>
    <?php } ?>

  </a>
</div>