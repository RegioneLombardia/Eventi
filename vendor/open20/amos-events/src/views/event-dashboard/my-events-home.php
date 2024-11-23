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
$this->title = AmosEvents::t('amosevents',"I miei eventi");
$this->params['breadcrumbs'][] = $this->title;
$userProfile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
$wizardAsset = \open20\amos\events\assets\WizardEventAsset::register($this);

$viewParams['templates'] = \open20\amos\events\utility\EventsUtility::getCmsTemplates();

?>

<div class="event-index container">
    <?php

    //    echo $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->session->get('previousUrl')]);
    //    echo $this->render('_order', ['model' => $model,'originAction' => Yii::$app->session->get('previousUrl')]);
    //$dataProvider->pagination->pageSize = 5;

    ?>
    <h1 class="mb-3"><?= AmosEvents::t('amosevents', "I miei eventi") ?></h1>
    <hr class="uk-margin-medium-bottom">
    <div class="row">
        <div class="col-md-8">
            <h3><span class="mdi mdi-calendar-account"></span> <?= AmosEvents::t('amosevents', "Eventi a cui sei iscritto") ?></h3>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a(AmosEvents::t('amosevents', "Tutti gli eventi a cui sei iscritto"), ['/events/event-dashboard/my-registrations-home']); ?> <span class="mdi mdi-arrow-right"></span>
        </div>

    </div>
    <div class="container-elenco-eventi">
        <?php
        echo \open20\amos\core\views\ListView::widget([
            'dataProvider' => $dataProviderReg,
            'itemView' => '_itemListEvent',
            'viewParams' => $viewParams,
            'options' => [
                'class' => 'event-card-content',
            ],
            'itemsContainerOptions' => [
                'class' => "lista-eventi",
                "role" => "listbox",
                "data-role" => "list-view",
            ],
            'itemOptions' => [
                'class' => "data-key",
            ],
        ]);
        ?>

    </div>

    <div class="row">
        <div class="col-md-8">
            <h3> <span class="mdi mdi-ticket-account"></span> <?= AmosEvents::t('amosevents', "I miei eventi con biglietto") ?></h3>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a(AmosEvents::t('amosevents', "Tutti gli eventi con biglietto a cui sono iscritto"), ['/events/event-dashboard/my-tickets-home']); ?> <span class="mdi mdi-arrow-right"></span>
        </div>
    </div>

    <div class="container-elenco-eventi container-eventi-in-attesa">
        <?php echo \open20\amos\core\views\ListView::widget([
            'dataProvider' => $dataProviderTickets,
            'itemView' => '_itemListEvent',
            'options' => [
                'class' => 'event-card-content',
            ],
            'itemsContainerOptions' => [
                'class' => "lista-eventi",
                "role" => "listbox",
                "data-role" => "list-view",
            ],
            'itemOptions' => [
                'class' => "data-key item-in-attesa",
            ],
        ]); ?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h3> <span class="mdi mdi-calendar-question"></span> <?= AmosEvents::t('amosevents', "In attesa di risposta") ?></h3>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a(AmosEvents::t('amosevents', "Tutti gli eventi in attesa di risposta"), ['/events/event-dashboard/my-invitation-home']); ?> <span class="mdi mdi-arrow-right"></span>
        </div>

    </div>

    <div class="container-elenco-eventi container-eventi-in-attesa">
        <?php echo \open20\amos\core\views\ListView::widget([
            'dataProvider' => $dataProviderInvite,
            'itemView' => '_itemListEvent',
            'options' => [
                'class' => 'event-card-content',
            ],
            'itemsContainerOptions' => [
                'class' => "lista-eventi",
                "role" => "listbox",
                "data-role" => "list-view",
            ],
            'itemOptions' => [
                'class' => "data-key item-in-attesa",
            ],
        ]); ?>
    </div>

    <div class="container-elenco-eventi container-eventi-interessanti">

        <h3><span class="mdi mdi-calendar-heart"></span> <?= AmosEvents::t('amosevents', "Ti potrebbe interessare") ?></h3>

        <?php echo \open20\amos\core\views\ListView::widget([
            'dataProvider' => $dataProviderOwnInterest,
            'itemView' => '_itemListEvent',
            'options' => [
                'class' => 'event-card-content',
            ],
            'itemsContainerOptions' => [
                'class' => "lista-eventi",
                "role" => "listbox",
                "data-role" => "list-view",
            ],
            'itemOptions' => [
                'class' => "data-key item-interessanti",
            ],
        ]);
        ?>
    </div>

</div>