<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\views\event
 * @category   CategoryName
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\ModalUtility;
use open20\amos\core\views\DataProviderView;
use open20\amos\events\AmosEvents;

use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\search\EventSearch $model
 * @var string $currentView
 */


/** @var AmosEvents $eventsModule */
$eventsModule = Yii::$app->getModule(AmosEvents::getModuleName());
$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
$userProfile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
$wizardAsset = \open20\amos\events\assets\WizardEventAsset::register($this);

$viewParams = [];
$isFrontend = \Yii::$app->controller->action->id == 'my-registrations-home' || \Yii::$app->controller->action->id == 'my-invitation-home' || \Yii::$app->controller->action->id == 'my-tickets-home';
?>

<div class="event-index">
    <?php
    $itemFile = '_itemListEventBackend';
    $viewParams['templates'] = \open20\amos\events\utility\EventsUtility::getCmsTemplates();

    if ($isFrontend){
    $itemFile = '_itemListEvent'; ?>
    <div class="container uk-margin-large-bottom">
        <?php echo \yii\helpers\Html::a("<span class=\"mdi mdi-arrow-left\"></span>" . AmosEvents::t('amosevents', 'Indietro'), ['/events/event-dashboard/my-events-home']) ?>
        <h1>
            <?= $title ?>
        </h1>
        <?php } ?>
        <?php

        echo $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->session->get('previousUrl')]);
        //    echo $this->render('_order', ['model' => $model,'originAction' => Yii::$app->session->get('previousUrl')]);
        //$dataProvider->pagination->pageSize = 5;
        $columns = [
            'title',
            'event_type_id' => [
                'attribute' => 'event_type_id',
                'label' => $model->getAttributeLabel('eventType'),
                'value' => 'eventType.title'
            ],
        ];

        if (in_array(\Yii::$app->controller->action->id, ['my-registrations-home', 'my-tickets-home', 'my-events', 'my-tickets'])) {
            $columns['companions'] = [
                'value' => function ($model) {
                    $n = 0;
                    if ($model->enable_companions) {
                        $invitation = \open20\amos\events\models\EventInvitation::find()
                            ->andWhere(['event_id' => $model->id])
                            ->andWhere(['user_id' => \Yii::$app->user->id])
                            ->one();
                        if ($invitation) {
                            $n = $invitation->getCompanions()->count();
                        }
                        if ($n == 0) {
                            $n = AmosEvents::t('amosevents', "Nessuno");
                        }

                        return $n;
                    }
                    return AmosEvents::t('amosevents', "Non disponibile");
                },
                'label' => AmosEvents::t('amosevents', "Accompagnatori")
            ];
        }

        $columns = \yii\helpers\ArrayHelper::merge($columns, [
            'multilanguage' => [
                'attribute' => 'multilanguage',
                'label' => $model->getAttributeLabel('Multilingua'),
                'value' => function ($model) {
                    if (is_null($model->multilanguage)) {
                        return false;
                    }
                    return $model->multilanguage;

                },
                'format' => 'boolean'
            ],

            'status' => [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getWorkflowBaseStatusLabel();
                }
            ],
            'begin_date_hour:datetime',
            [
                'class' => 'open20\amos\core\views\grid\ActionColumn',
                'template' => '{ticket}{companions}{info}{view}{community}{webex}{register}{unsubscribe}',
                'buttons' => [

                    'ticket' => function ($url, $model) {
                        return \open20\amos\events\utility\EventsUtility::getButtonDownloadTicket($model, \Yii::$app->params['platform']['backendUrl'], 'btn btn-secondary btn-xs mx-1', true);
                    },
                    'companions' => function ($url, $model) {
                        if (in_array(\Yii::$app->controller->action->id, ['my-registrations-home', 'my-tickets-home', 'my-events', 'my-tickets'])) {
                            return  Html::a(AmosIcons::show('accounts'),'/events/event/companions?id='. $model->id, [
                                'class' => 'btn btn-secondary btn-xs mx-1',
                                'title' => \Yii::t('amosevents', 'Partecipanti'),
                            ]);
                        }
                    },
                    'info' => function ($url, $model) {
                        $userEventCreator = $model->userCreator;
                        $userLastUpdate = $model->getUserLastUpdate();
                        $str = AmosEvents::t('amosevents', '<strong>Creatore evento</strong>') . ': ' . ($userEventCreator ? $userEventCreator->userProfile->nomeCognome : '') . "<br>";
                        $str .= AmosEvents::t('amosevents', '<strong>Autore ultima modifica</strong>') . ': ' . ($userLastUpdate ? $userLastUpdate->userProfile->nomeCognome : '');

                        return Html::a(AmosIcons::show('info-outline'), 'javascript:void(0)', [
                            'class' => 'btn btn-secondary btn-xs mx-1',
                            'data-container' => 'body', 'data-toggle' => 'popover', 'data-trigger' => 'hover', 'data-html' => 'true', 'title' => 'Info', 'data-content' => $str
                        ]);
                    },
                    'view' => function ($url, $model) {
                        return Html::a('Landing', \open20\amos\events\utility\EventsUtility::getUrlLanding($model), [
                            'class' => 'btn btn-secondary btn-xs mx-1',
                            'title' => 'Vai alla landing',
                            'target' => 'blank'
                        ]);
                    },
                    'community' => function ($url, $model) {
                        return \open20\amos\events\utility\EventsUtility::getButtonCommunity($model);
                    },
                    'webex' => function ($url, $model) {
                        $isInvitation = (\Yii::$app->controller->action->id == 'my-invitations');
                        if ($model->eventType->webmeeting_webex && !empty($model->webmeeting_webex_id) && !empty($model->webMeetingWebex)) {
                            $btnWebex = '';
                            if (!$model->webexIsClosed()) {
                                if (!$isInvitation) {
                                    if ($model->canGoToWebexUrl()) {
                                        $btnWebex = Html::a('Avvia Videoconferenza', $model->webMeetingWebex->web_link, [
                                            'class' => 'btn btn-primary btn-xs mr-1',
                                            'target' => '_blank',
                                            'title' => AmosEvents::t('amosevents', "Avvia la videoconferenza")
                                        ]);
                                    } else {
                                        $btnWebex = Html::a('Avvia Videoconferenza', 'javascript:void(0)', [
                                            'class' => 'btn btn-secondary btn-xs disabled',
                                            'disabled' => true,
                                            'title' => AmosEvents::t('amosevents', "La sessione di videoconferenza non è ancora iniziata. Quando il pulsante sarà attivo sarai rediretto all'area dedicata alla videoconferenza.")
                                        ]);
                                    }
                                }
                            }
                            return $btnWebex;
                        }
                        return '';
                    },
                    'register' => function ($url, $model) use ($userProfile) {
                        if (\Yii::$app->controller->action->id == 'my-invitations') {
                            $urlYes = \open20\amos\events\utility\EventsUtility::getUrlLanding($model) . "?pName={$userProfile->nome}&pSurname={$userProfile->cognome}&pEmail={$userProfile->user->email}";

                            return Html::a('Accetta Invito', $urlYes, ['class' => 'btn btn-primary btn-xs', 'title' => AmosEvents::t('amosevents', "Accetta Invito")]);
                        }
                    },
                    'download-ticket' => function ($url, $model) use ($userProfile) {

                        //                            \open20\amos\events\models\EventInvitationSent::find()
                        //                                ->andWhere(['user_id' => \Yii::$app->user->id])
                        return \open20\amos\events\utility\EventsUtility::getButtonDownloadTicket($model);
                    },


                    'unsubscribe' => function ($url, $model) use ($userProfile) {
                        if (\Yii::$app->controller->action->id == 'my-events') {
                            $member = \open20\amos\community\models\CommunityUserMm::find()
                                ->andWhere(['community_id' => $model->community_id])
                                ->andWhere(['user_id' => \Yii::$app->user->id])
                                ->andWhere(['status' => \open20\amos\community\models\CommunityUserMm::STATUS_ACTIVE])->one();
                            if ($member) {
                                //                            \open20\amos\events\models\EventInvitationSent::find()
                                //                                ->andWhere(['user_id' => \Yii::$app->user->id])
                                return Html::a(AmosEvents::t('amosevents', "Disiscriviti"), [
                                    '/events/event-dashboard/unsubscribe', 'id' => $model->id
                                ], ['class' => 'btn btn-danger btn-xs mx-1', 'title' => AmosEvents::t('amosevents', "Disiscriviti")]);
                            }
                        }
                    }
                ]
            ]
        ]);

        echo DataProviderView::widget([
            'dataProvider' => $dataProvider,
            'currentView' => $currentView,
            'gridView' => [
                'columns' => $columns
            ],
            'listView' => [
                'itemView' => $itemFile,
                'options' => [
                    'class' => 'event-card-content',
                ],
                'viewParams' => $viewParams,
                'itemsContainerOptions' => [
                    'class' => "row variable-gutters",
                    "role" => "listbox",
                    "data-role" => "list-view",
                ],
                'itemOptions' => [
                    'class' => "col-md-4 col-sm-6 my-4",
                ],

            ]
        ]); ?>
        <?php if ($isFrontend){ ?>

    </div>
<?php } ?>
</div>