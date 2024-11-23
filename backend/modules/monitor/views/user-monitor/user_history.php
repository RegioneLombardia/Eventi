<?php

use open20\amos\events\AmosEvents;
use amos\userimporter\models\UserImportTask;
use open20\amos\events\models\EventGroupReferentMm;
use open20\amos\events\models\EventInvitationSent;

$this->title = AmosEvents::t('monitor', 'Info/Storico di') ." ". $model->nomeCognome;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">

        <h1><?= \Yii::t('monitor', "Info") ?></h1>
        <?php
        $import = UserImportTask::find()->andWhere(['id' => $model->user_import_task_id])->one();
        if ($import) { ?>
            <h3><?= \Yii::t('monitor', 'Prima importazione') ?></h3>
            <p>
                <strong><?= \Yii::t('monitor', "Data") . ': ' ?></strong><?= $import->task_date ? \Yii::$app->formatter->asDate($import->task_date) : '' ?>
            </p>
            <p>
                <strong><?= \Yii::t('monitor', "Importato da") . ': ' ?></strong><?= $import->user ? $import->user->userProfile->nomeCognome : '' ?>
            </p>
            <p>
                <strong><?= \Yii::t('monitor', "Dd di appartenenza") . ': ' ?></strong><?= $import->eventGroupReferent ? $import->eventGroupReferent->denominazione : '' ?>
            </p>
            <hr class="dahed">
        <?php } ?>

        <?php $createdBy = \open20\amos\core\user\User::find()->andWhere(['id' => $model->created_by])->one();
        if ($createdBy) {
            $referentMm = EventGroupReferentMm::find()->andWhere(['user_id' => $createdBy->id])->one();
            ?>
            <h3><?= \Yii::t('monitor', 'Prima creazione') ?></h3>
            <p>
                <strong><?= \Yii::t('monitor', "Data") . ': ' ?></strong><?= $model->created_at ? \Yii::$app->formatter->asDate($model->created_at) : '' ?>
            </p>
            <p>
                <strong><?= \Yii::t('monitor', "Importato da") . ': ' ?></strong><?= $createdBy ? $createdBy->userProfile->nomeCognome : '' ?>
            </p>
            <p>
                <strong><?= \Yii::t('monitor', "Dd di appartenenza") . ': ' ?></strong><?= $referentMm ? $referentMm->eventGroupReferent->denominazione : '' ?>
            </p>
        <?php } ?>

    </div>

    <div class="col-md-12">
        <h3><?= \Yii::t('monitor', "L'utente è presente nelle seguenti DG: ") ?></h3>
        <?php


        echo \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderDgs,
            'columns' => [
                [
                    'attribute' => 'eventGroupReferent.denominazione',
                    'label' => \Yii::t('monitor', "Dg")
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'label' => \Yii::t('monitor', "Data associazione")
                ],
            ]
        ])
        ?>
    </div>


    <div class="col-md-12">
        <hr class="dashed">
        <?php

        ?>
        <h3><?= \Yii::t('monitor', "L'utente è stato invitato in {x} eventi", [
                'x' => $queryInvite->count()
            ]) ?>
        </h3>

        <?php echo \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderInvitations,
            'columns' => [
                [
                    'attribute' => 'eventInternalList.event.title',
                    'label' => \Yii::t('monitor', "Evento")
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'label' => \Yii::t('monitor', "Data invio invito")
                ],
            ]
        ])
        ?>
    </div>


    <div class="col-md-12">
        <hr class="dashed">
        <?php
        ?>
        <h3><?= \Yii::t('monitor', "L'utente è registrato ad {x} eventi", [
                'x' => $queryRegister->count()
            ]) ?>
        </h3>

        <?php echo \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderregistration,
            'columns' => [
                [
                    'value' => function ($model) {
                        $event = \open20\amos\events\models\Event::find()->andWhere(['community_id' => $model->community_id])->one();
                        return $event ? $event->title : '';
                    },
                    'label' => \Yii::t('monitor', "Evento")
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'label' => \Yii::t('monitor', "Data registrazione")
                ],
            ]
        ])
        ?>
    </div>

    <div class="col-md-12">
        <hr class="dashed">
        <?php
        ?>
        <h3><?= \Yii::t('monitor', "L'utente ha partecipato ad {x} eventi", [
                'x' => $queryPresenza->count()
            ]) ?>
        </h3>

        <?php echo \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderPresenza,
            'columns' => [
                [
                    'attribute' => 'event.title',
                    'label' => \Yii::t('monitor', "Evento")
                ],
                [
                    'attribute' => 'presenza_scansionata_il',
                    'format' => 'date',
                    'label' => \Yii::t('monitor', "Data partecipazione")
                ],
            ]
        ])
        ?>
    </div>

    <div class="col-md-12">
        <hr class="dashed">
        <p>
            <strong><?= \backend\modules\monitor\Module::t('monitor', "Attivo") . ': ' ?></strong><?= \Yii::$app->formatter->asBoolean($model->attivo) ?>
        </p>
    </div>


</div>

<div class="row">
    <div class="col-md-12">
        <h1><?= \Yii::t('monitor', "History") ?></h1>
        <?php echo \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderHistory,
            'columns' => [
                [
                    'attribute' => 'name',
                    'label' => \Yii::t('monitor', "Operazione")
                ],
                [
                    'value' => function ($model) {
                        return \backend\modules\monitor\utility\MonitorUtility::getDescriptionHistory($model);
                    },
                    'label' => \Yii::t('monitor', "Description"),
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        return \Yii::$app->formatter->asDate($model->created_at) . ' ' . \Yii::$app->formatter->asTime($model->created_at);

                    },
                    'label' => \Yii::t('monitor', "Data operazione")
                ],
            ]
        ])
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= \yii\helpers\Html::a(\backend\modules\monitor\Module::t('monitor', "Back"),['/admin/user-profile/index'],[
                'class' => 'btn btn-secondary'
        ])?>
    </div>
</div>