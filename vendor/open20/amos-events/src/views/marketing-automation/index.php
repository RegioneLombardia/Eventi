<?php

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use \yii\bootstrap4\ActiveForm;

// use open20\amos\core\forms\ActiveForm;
use open20\amos\events\models\search\UserEventSearch;
use yii\helpers\Html;
use open20\amos\events\models\EventCommunication;
use open20\amos\layout\utility\AmosIconsBi;


$this->title = AmosEvents::t('amosevents',"Lista comunicazioni");
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;
$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);

$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
$iconModifica = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_edit></use>";
$svgIconModifica = Html::tag('svg', $iconModifica, ['class' => 'icon icon-white']);
$spanSvgIconModifica = Html::tag('span', $svgIconModifica, ['class' => 'rounded-icon rounded-secondary p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Modifica'), ['class' => 'sr-only']);


$statuses = \open20\amos\events\models\EventCommunication::getStatusSending($dataProviderLists->keys);
?>
<h5 class="font-weight-bold my-2">
    <svg class="icon">
        <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_contact_mail"></use>
    </svg>
    <?= AmosEvents::t('amosevents', "Comunicazioni salvate") ?>
</h5>
<div class="row">

    <div class="col-md-12">
        <?= \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderLists,
            'columns' => [
                'title',
                [
                    'attribute' => 'communication_type',
                    'label' => AmosEvents::t('amosevents', "Destinatari della comunicazione"),
                    'value' => function ($model) {
                        return EventCommunication::getCommunicationTypeLabels()[$model->communication_type];
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'value' => function ($model) {
                        $profile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $model->created_by])->one();
                        if ($profile) {
                            return $profile->nomeCognome;
                        }
                    }

                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date'
                ],
                [
                    'label' => AmosEvents::t('amosevents', 'Tempistica invio'),
                    'value' => function ($model) {
                        if (is_null($model->time_schedule_type)) {
                            return ' - ';
                        } else if ($model->time_schedule_type == \open20\amos\events\models\EventCommunication::SENDING_TIME_IMMEDIATE) {
                            return AmosEvents::t('amosevents', 'Immediato');
                        } else {
                            $date = new \DateTime($model->time_schedule_date);
                            return AmosEvents::t('amosevents', 'Pianificato') . "<br>" . $date->format('d/m/Y H:i:s');

                        }
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'n_users',
                    'label' => AmosEvents::t('amosevents', "Utenti raggiunti")
                ],
                [
                    'class' => \open20\amos\core\views\grid\ActionColumn::className(),
                    'template' => '{stop_sending}{statistics}{update}{delete}{send-communication}',
                    'buttons' => [
                        'update' => function ($url, $model) use ($spanSvgIconModifica) {
//                            if ($model->communication_type == EventCommunication::TYPE_NOMINAL) {
//                                $url = ['/events/event-dashboard/nominal-invitation',
//                                    'id' => $model->event_id,
//                                    'list_id' => $model->event_internal_list_id,
//                                    'communication_id' => $model->id];
//                                return Html::a($spanSvgIconModifica, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
//                            }
                            $currentAction = EventCommunication::getActionForMarketingTypeCommunication($model->communication_type);
                            if (!$model->is_automatic && is_null($model->status)) {
                                $url = [$currentAction, 'id' => $model->id];
                                return Html::a($spanSvgIconModifica, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
                            }
                        },

                        'delete' => function ($url, $model) {
                            if (!$model->is_automatic) {
                                $url = ['/events/marketing-automation/delete-communication', 'id' => $model->id,  'urlRedirect' => '/events/marketing-automation/index'];
                                $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                                $iconDelete = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_close></use>";
                                $svgIconDelete = Html::tag('svg', $iconDelete, ['class' => 'icon icon-white']);
                                $spanSvgIconDelete = Html::tag('span', $svgIconDelete, ['class' => 'rounded-icon rounded-danger p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Elimina'), ['class' => 'sr-only']);
                                return Html::a($spanSvgIconDelete, $url, [
                                    'class' => 'btn btn-xs btn-icon',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Elimina',
                                    'data-confirm' => AmosEvents::t('amosevents', "Sei sicuro di eliminare la comunicazione?")
                                ], true);
                            }
                        },
                        'statistics' => function ($url, $model)  {
                            $url = ['/events/event-dashboard/statistics-communication', 'communicationId' => $model->id,  'urlRedirect' => '/events/marketing-automation/index'];
                            if ($model->mailup_message_id) {
                                return Html::a(AmosIconsBi::show('ic_show_chart'), $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => AmosEvents::t('amosevents', 'Statistics')], true);
                            }
                        },
                        'send-communication' => function ($url, $model) use ( $statuses) {
                            if (!is_null($model->status)) {
                                $status = $model->getSingleStatusSending($statuses);
                                return $status;
                            };
                            $url = ['send-communication', 'id' => $model->id,];
                            $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                            $iconSend = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_send></use>";
                            $svgIconSend = Html::tag('svg', $iconSend, ['class' => 'icon icon-white']);
                            $spanSvgIconSend = Html::tag('span', $svgIconSend, ['class' => 'rounded-icon rounded-primary  p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Invia'), ['class' => 'ml-1']);
                            return Html::a($spanSvgIconSend, $url, ['class' => 'ml-2 btn btn-xs btn-icon btn-primary', 'data-toggle' => 'tooltip', 'title' => 'Invia inviti'], true);
                        },
                        'stop_sending' => function ($url, $model) {
                            $url = ['/events/event-dashboard/stop-sending-communication', 'communicationId' => $model->id, 'urlRedirect' => '/events/marketing-automation/index'];
                            if (!in_array($model->status, [EventCommunication::STATUS_SENT, EventCommunication::STATUS_NO_USER_TO_IMPORT]) && $model->status != null) {
                                return Html::a(AmosIconsBi::show('ic_block', 'danger'), $url, [
                                    'class' => 'btn btn-xs btn-icon',
                                    'data-toggle' => 'tooltip',
                                    'title' => AmosEvents::t('amosevents', 'Annulla invio'),
                                    'data-confirm' => AmosEvents::t('amosevents', 'Sei sicuro di annullare l\'invio?')
                                ], true);
                            }
                        },

                    ]
                ]
            ],

        ])
        ?>
    </div>
</div>

