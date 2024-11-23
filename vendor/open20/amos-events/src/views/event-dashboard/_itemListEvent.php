<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news\views\news
 * @category   CategoryName
 */

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\forms\ItemAndCardHeaderWidget;
use open20\amos\core\forms\PublishedByWidget;
use yii\helpers\Html;
use open20\amos\core\views\toolbars\StatsToolbar;
use open20\amos\events\AmosEvents;
use open20\amos\notificationmanager\forms\NewsWidget;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\utility\EventsUtility;

$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
$flagAsset = \open20\amos\events\assets\FlagAssets::register($this);
$eventLocation = $model->eventLocation;
$class = EventsUtility::getBackgroundClassCmsTemplate($model, $templates);

/**
 * @var \open20\amos\events\models\Event $model
 */
?>
<?php $route = \open20\amos\events\utility\EventsUtility::getUrlLanding($model); ?>
<div class="link-evento">
    <div class="box-evento">
        <div class="actions-event">

            <?php if ($model->multilanguage) { ?>
                <span class="multilingua" style="margin-right: 6px;">
                    <?= Html::img($flagAsset->baseUrl . '/img/italy.png', ['width' => 24, 'title' => AmosEvents::t('amosevents', "Evento multilingua")]) ?>
                    <?= Html::img($flagAsset->baseUrl . '/img/united-kingdom.png', ['width' => 24, 'title' => AmosEvents::t('amosevents', "Evento multilingua")]) ?>
                </span>
            <?php } ?>
            <?php
            if (\Yii::$app->user->can('EVENT_UPDATE')) {
                $urlModifica = "/events/event-dashboard/view?id=" . $model->id;
                $iconModifica = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_edit></use>";
                $svgIconModifica = Html::tag('svg', $iconModifica, ['class' => 'icon']);
                $spanSvgIconModifica = Html::tag('span', $svgIconModifica, ['class' => '']) . Html::tag('span', AmosEvents::t('amosevents', 'Modifica'), ['class' => 'sr-only']);
                echo Html::a($spanSvgIconModifica, $urlModifica, ['class' => 'btn btn-default btn-xs btn-icon p-1', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
            }
            if (\Yii::$app->user->can('EVENT_DELETE')) {
                $urlDelete = "/events/event-dashboard/delete-event?id=" . $model->id;
                $iconDelete = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_close></use>";
                $svgIconDelete = Html::tag('svg', $iconDelete, ['class' => 'icon icon-white']);
                $spanSvgIconDelete = Html::tag('span', $svgIconDelete, ['class' => '']) . Html::tag('span', AmosEvents::t('amosevents', 'Elimina'), ['class' => 'sr-only']);
                echo Html::a($spanSvgIconDelete, $urlDelete, ['class' => 'btn btn-danger btn-xs btn-icon p-1', 'data-toggle' => 'tooltip', 'title' => 'Elimina'], true);
            }
            ?>
        </div>
        <div class="content-evento">
            <div class="img-evento">
                <?php
                $url = $model->getMainImageEvent();
                $img = \yii\helpers\Html::img($url, ['alt' => AmosEvents::t('amosevents', 'Immagine delle evento: ') . $model->getTitle()]);
                echo $img;
                ?>
            </div>
            <div class="position-cover  <?= $class ?>">
                <div class="content-tag">
                    <?php $tagsPreference = $model->getEventTagPreferences();
                    foreach ($tagsPreference as $tag) {
                        echo "<span class='tipologia-evento'>" . $tag->nome . "</span>";
                    }
                    ?>
                </div>
                <div class="content-info-evento">
                    <?php if ($model->eventType->webmeeting_webex && $model->webmeeting_webex_id) { ?>
                        <?php if ($model->canGoToWebexUrl()) { ?>
                            <?= Html::a(
                                '<span class="icon-webex mdi-24px mdi mdi-play-circle-outline"></span> <span class="sr-only">' . AmosEvents::t('amosevents', 'MEETING WEBEX') . '</span>',
                                $model->webMeetingWebex->web_link,
                                [
                                    'class' => 'btn btn-webex btn-sm btn-icon mb-2 rounded-0',
                                    'title' => AmosEvents::t('amosevents', 'Avvia videoconferenza'),
                                    'data-toggle' => "tooltip",
                                    'target' => '_blank'
                                ]
                            ); ?>
                        <?php } else { ?>
                            <?= Html::a(
                                '<span class="icon-webex mdi-24px mdi mdi-play-circle-outline"></span> <span class="sr-only">' . AmosEvents::t('amosevents', 'MEETING WEBEX') . '</span>',
                                'javascript:void(0);',
                                [
                                    'class' => 'btn btn-webex btn-sm btn-icon mb-2 rounded-0',
                                    'title' => AmosEvents::t('amosevents', "La sessione di videoconferenza non è ancora iniziata. Quando il pulsante sarà attivo sarai rediretto all'area dedicata alla videoconferenza"),
                                    'disabled' => true
                                ]
                            ); ?>
                        <?php } ?>

                    <?php } ?>

                    <div class="content-badge">


                    <?php
                    // DOWNLOAD BIGLIETTO
                    echo \open20\amos\events\utility\EventsUtility::getButtonDownloadTicket($model, \Yii::$app->params['platform']['backendUrl'], 'btn-download-ticket', true);
                    if ($model->enable_companions) {
                        $companions = \open20\amos\events\utility\EventsUtility::getCompanions($model->id);
                        $ncompanions = count($companions);
                        // echo AmosEvents::t('amosevents', "+{i} partecipanti aggiuntivi", ['i' => $ncompanions]);
                    

                            ?>

                            <span class="badge-accompagnatori"
                                  title="<?= AmosEvents::t('amosevents', "+{i} accompagnatori", ['i' => $ncompanions]); ?>"><span
                                        class="mdi mdi-account-supervisor"></span><?= $ncompanions ?><span
                                        class="sr-only"><?= AmosEvents::t('amosevents', "+{i} accompagnatori", ['i' => $ncompanions]); ?></span></span>
                        <?php } ?>
                    </div>

                    <div><span class="badge badge-secondary py-2 text-truncate" data-toggle="tooltip"
                               title="<?= $model->eventType->title ?>"
                               style="background-color:<?= $model->eventType->color ?>"><?= $model->eventType->title ?></span>
                    </div>
                    <a class="link-evento" href="<?= $route ?>"
                       title="Visualizza dettaglio evento: <?= $model->getTitle(); ?>">
                        <div class="titolo-evento">
                            <span><?= $model->getTitle(); ?></span>
                        </div>
                    </a>
                    <div class="content-data-location">
                        <div class="data-eventi uk-margin-remove-bottom">
                            <span class="mdi mdi-calendar-text mdi-24px data-icon"></span>
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

                        <?php if ($eventLocation) { ?>
                            <div class="location-eventi">
                                <span class="mdi mdi-map-marker mdi-24px location-icon"></span>
                                <span class="location-text"><?= $eventLocation->name ?></span>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>