<?php

use open20\amos\events\AmosEvents;
use kartik\select2\Select2;
use open20\amos\events\models\search\EventTypeSearch;
use yii\helpers\Html;


?>

<?php
// METRICA TIPOLOGIA EVENTO
if ($actionType == 'analyze-event-types') {
    $data = [];
    $dataSelect = EventTypeSearch::searchEnabledGenericContextEventTypesReadyForSelect();
    $i = 0;
    foreach ($dataSelect as $key => $value) {
        if ($i == 0) {
            $data['all'] = AmosEvents::t('amosevents', "Tutti");
        }
        $data [$key] = $value;
    }

    if (empty($model->event_type_id)) {
        $model->event_type_id = 'all';
    }
    ?>

    <?= $form->field($model, 'event_type_id')->widget(Select2::className(), [
        'data' => $data,
        'language' => substr(Yii::$app->language, 0, 2),
        'options' => [
            'id' => 'event-type-id',
            'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
            'data-toggle' => 'tooltip'
        ],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                ]
    ])->label(AmosEvents::t('amosevents', "Tipologia evento"))
    ?>


    <?php
// METRICA PER DIREZIONE GENERALE
} else if ($actionType == 'analyze-event-dg') {
    $dgs = \open20\amos\events\models\EventGroupReferent::find()->orderBy('denominazione ASC')->all();
    $dataSelect = \yii\helpers\ArrayHelper::map($dgs, 'id', 'denominazione');
    $i = 0;
    foreach ($dataSelect as $key => $value) {
        if ($i == 0) {
            $data['all'] = AmosEvents::t('amosevents', "Tutte");
        }
        $data [$key] = $value;
    }
    if (empty($model->event_group_referent_id)) {
        $model->event_group_referent_id = 'all';
    }
    ?>
    <?= $form->field($model, 'event_group_referent_id')->widget(Select2::className(), [
        'data' => $data,
        'language' => substr(Yii::$app->language, 0, 2),
        'options' => [
            'id' => 'event-type-id',
            'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
            'data-toggle' => 'tooltip'
        ],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                ]
    ])->label(AmosEvents::t('amosevents', "Direzione generale"))
    ?>

    <?php
// METRICA PER AMBITO DELEGATO
} else if ($actionType == 'analyze-event-delegations') {
    $moduleEvents = Yii::$app->getModule('events');
    if ($moduleEvents) {
        $dgAdministrator = $moduleEvents->groupReferentAdministration['id'];
    }
    $dgs = \open20\amos\events\models\EventGroupReferent::find()
        ->andFilterWhere(['!=', 'id', $dgAdministrator]) // Exclude DG Administrator (UO Comunicazione)
        ->orderBy('denominazione ASC')
        ->all();
    $dataSelect = \yii\helpers\ArrayHelper::map($dgs, 'id', 'denominazione');
    $i = 0;
    foreach ($dataSelect as $key => $value) {
        if ($i == 0) {
            $data['all'] = AmosEvents::t('amosevents', "Tutte");
        }
        $data [$key] = $value;
    }
    if (empty($model->event_group_referent_id)) {
        $model->event_group_referent_id = 'all';
    }
    ?>
    <?= $form->field($model, 'event_group_referent_id')->widget(Select2::className(), [
        'data' => $data,
        'language' => substr(Yii::$app->language, 0, 2),
        'options' => [
            'id' => 'event-type-id',
            'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
            'data-toggle' => 'tooltip'
        ],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                ]
    ])->label(AmosEvents::t('amosevents', "Direzione generale"))
    ?>

    <?php
// METRICA TAGS INFORMATIVI
} else if ($actionType == 'analyze-event-preference-tags') {
    $preferenceCenterTags = \open20\amos\events\utility\EventsUtility::getPreferenceCenterTags();
    $dataSelect = \yii\helpers\ArrayHelper::map($preferenceCenterTags, 'id', 'nome');

    $i = 0;
    foreach ($dataSelect as $key => $value) {
        if ($i == 0) {
            $data['all'] = AmosEvents::t('amosevents', "Almeno un tag");
        }
        $data [$key] = $value;
    }
    if (empty($model->tagPreference)) {
        $model->tagPreference = 'all';
    }
    ?>
    <?= $form->field($model, 'tagPreference')->widget(Select2::className(), [
        'data' => $data,
        'language' => substr(Yii::$app->language, 0, 2),
        'options' => [
            'id' => 'event-tags-id',
            'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
            'data-toggle' => 'tooltip'
        ],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                ]
    ])->label(AmosEvents::t('amosevents', "Tag informativo"))
    ?>

    <?php // METRICA PARTEECIPANTI ALL'EVENTO ?>
<?php } else if ($actionType == 'analyze-event-participants') {
    $dgs = \open20\amos\events\models\EventGroupReferent::find()->orderBy('denominazione ASC')->all();
    $dataSelect = \yii\helpers\ArrayHelper::map($dgs, 'id', 'denominazione');
    $i = 0;
    foreach ($dataSelect as $key => $value) {
        if ($i == 0) {
            $data['all'] = AmosEvents::t('amosevents', "Tutte");
        }
        $data [$key] = $value;
    }
    if (empty($model->event_group_referent_id)) {
        $model->event_group_referent_id = 'all';
    }
    ?>
    <?= $form->field($model, 'event_group_referent_id')->widget(Select2::className(), [
        'data' => $data,
        'language' => substr(Yii::$app->language, 0, 2),
        'options' => [
            'id' => 'event-type-id',
            'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
            'data-toggle' => 'tooltip'
        ],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                ]
    ])->label(AmosEvents::t('amosevents', "Direzione generale"))
    ?>
    <?php // METRICA PARTEECIPANTI ALL'EVENTO ?>
<?php } else if ($actionType == 'analyze-user-access') {
    $accessTypes = open20\amos\admin\models\UserAccessLog::getAccessTypeLabels();
    $isFrontend = \Yii::$app->controller->action->id == 'user-access-frontend';
    $i = 0;
    foreach ($accessTypes as $key => $value) {
        if ($i == 0) {
            $data['all'] = AmosEvents::t('amosevents', "Tutte");
        }
        //nel frontend tolgo il login standard
        if ($isFrontend) {
            if ($key != open20\amos\admin\models\UserAccessLog::ACCESS_METHOD_LOGIN_STANDARD) {
                $data [$key] = $value;
            }
            // nel backend tolgo authenticazione stranieri
        } else {
            if ($key != open20\amos\admin\models\UserAccessLog::ACCESS_METHOD_BASIC_AUTH_STRANIERI) {
                $data [$key] = $value;
            }
        }
    }
    if (empty($model->access_type)) {
        $model->access_type = 'all';
    }

    ?>
    <?= $form->field($model, 'access_type')->widget(Select2::className(), [
        'data' => $data,
        'language' => substr(Yii::$app->language, 0, 2),
        'options' => [
            'id' => 'event-type-id',
            'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
            'data-toggle' => 'tooltip'
        ],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                ]
    ])->label(AmosEvents::t('amosevents', "tipologie di accesso"))
    ?>


<?php // METRICA REGISTRAZIONE UTENTI ?>
<?php } else if ($actionType == 'registration-users') {
    $dgs = \open20\amos\events\models\EventGroupReferent::find()->orderBy('denominazione ASC')->all();
    $dataSelect = \yii\helpers\ArrayHelper::map($dgs, 'id', 'denominazione');
    $i = 0;
    foreach ($dataSelect as $key => $value) {
        if ($i == 0) {
            $data['all'] = AmosEvents::t('amosevents', "Tutte");
            $data['none'] = AmosEvents::t('amosevents', "Nessuna dg");
            $i = 1;
        }
        $data [$key] = $value;
    }
    if (empty($model->event_group_referent_id)) {
        $model->event_group_referent_id = 'all';
    }
    ?>
    <?= $form->field($model, 'event_group_referent_id')->widget(Select2::className(), [
        'data' => $data,
        'language' => substr(Yii::$app->language, 0, 2),
        'options' => [
            'id' => 'event-type-id',
            'placeholder' => AmosEvents::t('amosevents', 'Select/Choose') . '...',
            'data-toggle' => 'tooltip'
        ],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                ]
    ])->label(AmosEvents::t('amosevents', "Direzione generale"))
    ?>
<?php } ?>
