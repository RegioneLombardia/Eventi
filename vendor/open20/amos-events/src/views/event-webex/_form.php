<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\views\user-profile
 * @category   CategoryName
 */
use open20\amos\admin\AmosAdmin;
use \open20\amos\core\views\AmosGridView;
use \open20\amos\events\AmosEvents;

/**
 * @var yii\web\View $this
 * @var \open20\amos\admin\models\UserProfile $model
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var string $currentView
 * @var string $fromAction
 */
$fromAction = (isset($fromAction) ? $fromAction : 'index');

/** @var \open20\amos\admin\controllers\UserProfileController $appController */
$appController = Yii::$app->controller;
$adminModule = AmosAdmin::instance();
?>
<div class="user-profile-index">



<?php
$columns = [];
$columns[] = 'nome';
$columns[] = 'cognome';
$columns[] = 'codice_fiscale';
$columns[] = [
    'label' => 'email',
    'value' => function ($model) {
        $profile = \open20\amos\core\user\User::find()->select('email')
                        ->andWhere(['id' => $model->user_id])->one();
        return $profile->email;
    }
];
$columns[] = [
    'class' => 'open20\amos\core\views\grid\ActionColumn',
    'template' => '{assign}{delete}',
    'buttons' => [
        'assign' => function ($url, $model) use ($modelWebexAccounts) {
            return \yii\helpers\Html::a('Assegna',
                    ['/events/event-webex/assign', 'id' => $modelWebexAccounts->id, 'user_id' => $model->user_id, 'url' => \Yii::$app->getView()->params['urlget']],
                    [
                        'class' => "btn btn-xs btn-outline-primary btn-icon",
                        'data-key' => $modelWebexAccounts->id,
                        'title' => AmosEvents::t('amosevents', "Assegna utente a webex account"),
            ]);
        },
        'delete' => function ($url, $model) {
                if (empty($model->user_id)) {
                    return \yii\helpers\Html::a(AmosIcons::show('delete'), $url, [
                        'class' => 'btn btn-danger-inverse',
                        'data-confirm' => AmosEvents::t('amosevents', "Sei sicuro di eliminare l'assegnazione?"),
                        'title' => AmosEvents::t('amosevents', "Elimina assgnazione"),
                    ], true);
                }
            }
    ],
];

if (!Yii::$app->request->get('currentView') || (Yii::$app->request->get('currentView') == 'icon')) {
    $dataProvider->pagination = [
        'pageSize' => 21,
    ];
}
?>
    <?= $this->render('_search', ['modelSearch' => $modelSearch, 'id' => $modelWebexAccounts->id, 'originAction' => Yii::$app->controller->action->id]); ?>
    <?=
    AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,
    ])
    ?>

</div>
