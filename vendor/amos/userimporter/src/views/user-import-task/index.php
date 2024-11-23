<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @backend/views
 */

use amos\userimporter\base\UserImportTaskStatus;
use amos\userimporter\Module;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\views\DataProviderView;
use open20\amos\layout\utility\AmosIconsBi;

use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var amos\userimporter\models\search\UserImporterTaskSearch $model
 */
$this->title = Yii::t('amosuserimporter', 'Gestione delle Importazioni Utente');
$this->params['titleSection']  = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-import-task-index mt-3">
    <?=

    $this->render('_search',
        ['model' => $model, 'originAction' => Yii::$app->controller->action->id]);
    ?>

    <?php
    $statuses = \amos\userimporter\models\UserImportTask::getStatusSending($dataProvider->keys);
    ?>
    <?=
    DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                [
                    'label' => Module::t('amosevents', 'Nome della importazione'),
                    'attribute' => 'name',
                    'value' => function ($model) {
                        return $model->name;
                    }
                ],
                [
                    'attribute' => 'task_date',
                    'format' =>
                        'datetime'
                ],


                'eventGroupReferent' => [
                    'attribute' => 'eventGroupReferent',
                    'format' => 'html',
                    'label' => Module::t('amosuserimporter',
                        'Direzione Generale'),
                    'value' => function ($model) {
                        return strip_tags($model->eventGroupReferent->denominazione);
                    }
                ],
                'user' => [
                    'attribute' => 'user',
                    'format' => 'html',
                    'label' => Module::t('amosuserimporter',
                        '#operator'),
                    'value' => function ($model) {
                        return strip_tags($model->user->userProfile->getNomeCognome());
                    }
                ],
                [
                    'label' => Module::t('amosevents', 'Totale utenti processati'),
                    'attribute' => 'tot_input_processed',
                    'value' => function ($model) {
                        return $model->tot_input_processed;
                    }
                ],
                [
                    'label' => Module::t('amosevents', 'Totale utenti importati'),
                    'attribute' => 'tot_input_imported',
                    'value' => function ($model) {
                        return $model->tot_input_imported;
                    }
                ],
                'status' => [
                    'attribute' => 'status',
                    'format' => 'html',
                    'label' => Module::t('amosuserimporter', 'Stato'),
                    'value' => function ($model) use ($statuses) {
                        return $model->getSingleStatusSending($statuses);
//                        return UserImportTaskStatus::getStateLabel($model->status);
                    }
                ],
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{view-email}{statistics}{downloadfile}{downloadfileOk}{downloadfileErr}{delete}',
                    'buttons' => [
                        'view-email' => function ($url, $model) {
                            $url = ['/userimporter/user-import-task/view-email', 'id' => $model->id];
                            return \yii\helpers\Html::a(AmosIconsBi::show('ic_email'), $url, [
                                'title' => Yii::t('amoscore', 'Visualizza email'),
                                'class' => 'btn btn-xs btn-icon'
                            ], true);
                        },
                        'statistics' => function ($url, $model) {
                            $url = ['statistics', 'userTaskListId' => $model->id];
                            if ($model->mailup_message_id) {
                                return Html::a(AmosIconsBi::show('ic_show_chart'), $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip',
                                    'title' => \Yii::t('amosevents', 'Statistics')]);
                            }
                        },
                        'update' => function ($url, $model) {
                            return \yii\helpers\Html::a(AmosIconsBi::show('ic_edit'), $url, [
                                'title' => Yii::t('amoscore', 'Modifica'),
                                'class' => 'btn btn-xs btn-icon'
                            ], true);
                        },
                        'delete' => function ($url, $model) {
                            return \yii\helpers\Html::a(AmosIconsBi::show('ic_delete', 'danger'), $url, [
                                'title' => Yii::t('amoscore', 'Elimina'),
                                'class' => 'btn btn-xs btn-icon'
                            ], true);
                        },
                        'downloadfile' => function ($url, $model) {
                            $file = $model->getFile_input();

                            if ($model->is_from_edit_list || !is_null($file)) {
                                if($model->is_from_edit_list){
                                    $url = ['/userimporter/user-import-task/download-edit-list', 'id' => $model->id];
                                }else {
                                    $url = $file->getWebUrl();
                                }

                                return Html::a(AmosIconsBi::show('ic_file_download'),
                                    $url,
                                    [
                                        'class' => 'ml-2 btn btn-xs btn-icon',
                                        'title' => Yii::t('amoscore',
                                            'Download file'),
                                        'data-pjax' => '0'
                                    ]);
                            }
                        },
                        'downloadfileOk' => function ($url, $model) {
                            $file = $model->getFile_success_input();
                            if (!is_null($file)) {
                                return Html::a(AmosIconsBi::show('ic_file_download'),
                                    $file->getWebUrl(),
                                    [
                                        'class' => 'btn btn-xs btn-icon',
                                        'title' => Yii::t('amoscore',
                                            'Riepilogo Utenti'),
                                        'data-pjax' => '0'
                                    ]);
                            }
                        },
                        'downloadfileErr' => function ($url, $model) {
                            $file = $model->getFile_errors_input();
                            if (!is_null($file)) {
                                return Html::a(AmosIconsBi::show('ic_file_download'),
                                    $file->getWebUrl(),
                                    [
                                        'class' => 'btn btn-xs btn-icon',
                                        'title' => Yii::t('amoscore',
                                            'Riepilogo Errori'),
                                        'data-pjax' => '0'
                                    ]);
                            }
                        },
                    ],
                ],
            ],
        ],
    ]);
    ?>

</div>
