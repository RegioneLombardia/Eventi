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
<a href="<?= $urlView ?>" class="link-evento" title="Visualizza dettaglio evento">

    <div class="box-evento">

        <div class="content-evento">

            <div class="img-evento">
                <amp-img src="<?= $urlImg ?>" layout="fill" height="700" width="auto"></amp-img>
            </div>
            <div class="position-cover <?= $class ?>">
                <div class="content-tag">
                    <?php $tagsPreference = $model->getEventTagPreferences();
                    foreach ($tagsPreference as $tag) {
                        echo "<span class='tipologia-evento'>" . $tag->nome . "</span>";
                    }
                    ?>
                </div>

                <?php if ($highlight) { ?>
                    <div class="triangle" title="<?= AmosEvents::t('amosevents', 'Evento in evidenza') ?>"></div>
                <?php } ?>

                <div class="content-info-evento">
                    <div class="titolo-evento">
                        <span><?= $model->title ?></span>
                    </div>

                    <div class="content-data-location">
                        <div class="data-eventi uk-margin-remove-bottom">
                            <span class="material-icons">
                                event_note
                            </span>

                            <div class="data-text">
                                <?php $beginDate = new \DateTime($model->begin_date_hour) ?>
                                <?php if (!empty($model->begin_date_hour)) {
                                    $endDate = new \DateTime($model->end_date_hour) ?>
                                    <span><?= \Yii::t('app', "dal") . ' ' . $beginDate->format('d.m.Y - H:i') . '</span><span>' . ' al ' . $endDate->format('d.m.Y - H:i') ?></span>
                                <?php } else { ?>
                                    <?= $beginDate->format('d.m.Y H:i') ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="location-eventi">
                            <span class="material-icons">
                                place
                            </span>
                            <?php if ($eventLocation) { ?>
                                <span class="location-text"><?= $eventLocation->name ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="data-gruppo-eventi uk-margin-remove-bottom">

                </div>
            </div>
        </div>
    </div>
</a>