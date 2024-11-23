<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting View
 */
use open20\webmeeting\WebMeetingModule;
use open20\webmeeting\assets\WebMeetingInviteeAssets;

use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\Tabs;
use open20\amos\core\forms\CloseSaveButtonWidget;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

WebMeetingInviteeAssets::register($this);

$form = ActiveForm::begin([
    'options' => [
        'id' => 'webmeeting',
        'data-fid' => (isset($fid)) ? $fid : 0,
        'data-field' => ((isset($dataField)) ? $dataField : ''),
        'data-entity' => ((isset($dataEntity)) ? $dataEntity : ''),
        'class' => ((isset($class)) ? $class : ''),
        'enctype' => 'multipart/form-data' // important
    ]
]);
?>

<div class="webex-form">
    <?= $this->render('parts/webmeeting', [
        'form' => $form,
        'model' => $model,
        'timezone' => $timezone,
        'coHostUserEmail' => $coHostUserEmail,
        'canUpdate' => $canUpdate,
        'scope' => $scope,
        'currentView' => $currentView,
    ]) ?>

    <?= $this->render('parts/participants', [
        'form' => $form,
        'model' => $model,
        'userDataProvider' => $userDataProvider,
        'modelUser' => $modelUser,
        'modelInvitees' => $modelInvitees,
        'dualListInvited' => $dualListInvited,
        'invitees' => $invitees,
        'canUpdate' => $canUpdate,
        'scope' => $scope,
        'currentView' => $currentView,
    ]) ?>

    <?php
    $itemsTab = [
        [
            'label' => WebMeetingModule::_t('#tab_details'),
            'content' => $this->blocks['tab_details'],
            'options' => [
                'id' => 'tab_details',
                'class'=>'row'
            ],
            'active' => true
        ],
        [
            'label' => WebMeetingModule::_t('#tab_invitees'),
            'content' => $this->blocks['tab_invitees'],
            'options' => ['id' => 'tab_invitees'],
        ]
    ];
    ?>

    <div class="row">
        <div class="col-xs-12">
        <?= Tabs::widget([
            'encodeLabels' => false,
            'items' => $itemsTab
        ]);
        ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 note_asterisk ">
            <?= WebMeetingModule::_t('#required_fields', [
                '0' => '<span class="red">*</span>'
            ])
            ?>
        </div>
        <div class="col-xs-12">
            <?= CloseSaveButtonWidget::widget([
                'model' => $model,
                'urlClose' => WebMeetingModule::getMyIndexLink(),
                'closeButtonLabel' => WebMeetingModule::_t('#close')
            ]);
            ?>

        </div>
    </div>
    
</div>
<?php ActiveForm::end(); ?>
