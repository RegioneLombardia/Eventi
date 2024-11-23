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
use open20\webmeeting\utility\WebMeetingUtility;
use open20\webmeeting\assets\WebMeetingInviteeAssets;
use open20\amos\core\views\AmosGridView;
use open20\amos\core\icons\AmosIcons;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

$resourceInvitessAsset = WebMeetingInviteeAssets::register($this);

$this->title                   = WebMeetingModule::_t('#invitees_list');
$this->params['breadcrumbs'][] = [
    'label' => WebMeetingModule::_t('#webmeeting_invitess'),
    'url' => ['join', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = $this->title;

$this->beginBlock('tab_invitees');
?>

<div class="row">
    <div class="col-lg-8 col-sm-12">
        <h2 class="subtitle-form"><?= WebMeetingModule::_t('#list_users') ?></h2><!--todo traduzione-->
        <div id="number-of-users" class="number-of-users"><?= WebMeetingModule::_t('#max_number_invitees', [
            'max_invitees' => WebMeetingModule::WEBMEETING_MAXIMUM_INVITEES
        ]) ?></div>
        <?= AmosGridView::widget([
            'id' => 'gw-invitee',
            'dataProvider' => $userDataProvider,
            'filterModel' => $modelUser,
            'showPageSummary' => false,
            'exportConfig' => null,
            'pjax' => true,
            'striped' => false,
            'hover' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => WebMeetingModule::_t('#user_list_by_scope', [
                    'maxChar' => WebMeetingUtility::getCurrentScopeName($scope)
                ]
                )
            ],
            'toggleDataContainer' => ['class' => 'btn-group mr-2'],
            'columns' => [
                [
                    'class' => 'kartik\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) use ($scope) {
                        $nameSurname = $model->cognome . ' ' . $model->nome;
                        $checkboxOptions = [
                            'value' => $model['user_id'],
                            'data-label' => $nameSurname,
                            'id' => 'user_' . $model['user_id']
                        ];

                        return $checkboxOptions;
                    }
                ],
                'cognome',
                'nome',
                'user.email',
            ]
        ]);
?>
    </div>

    <div class="col-lg-4 col-sm-12">
        <div class="summary-design d-flex justify-content-between m-b-0">
            <h2 class="subtitle-form"><?= WebMeetingModule::_t('#list_participants') ?></h2>
            <div id="number-of-partecipants" class="number-of-partecipants"><?= WebMeetingModule::_t('#0_participants') ?></div>
            <hr style="border:1px solid #ddd; ">

        </div>
        <div id="participant-list" class="participant-list">
            <ul id="preview_list" class="invitees-preview-list">
                <?php if (empty($invitees)) : ?>
                    <li><?= WebMeetingModule::_t('#no_participants_selected') ?></li>
                <?php else: ?>
                    <?php 
                    $hiddenFields = [];
                    foreach ($invitees as $invitee) : ?>
                    <li class="row">
                        <div class="selected col-xs-12">
                            <div class="selected_part selected_remove_container">
                                <div class="selected_remove" id="<?= $invitee->user_id ?>">
                                    <button type="button" class="btn btn-danger-inverse">
                                        <span class="am am-delete"></span>
                                        <span class="sr-only"><?= WebMeetingModule::_t('#delete') ?></span>
                                    </button>
                                </div>
                            </div>
                            <div class="selected_part">
                                <div class="selected_label"><?= $invitee->display_name ?></div>
                            </div>
                        </div>
                    </li>
                    <?php 
                    $hiddenFields[] = '<input type="hidden" name="selectedUsers[]" class="selectedUsers" data-id="' . $invitee->user_id . '" data-label="' . $invitee->display_name . '" value="' . $invitee->user_id . '" />';

                    endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div id="hiddenSelectedContainer"><?= implode("\n", $hiddenFields) ?></div>

</div>

<?php $this->endBlock(); ?>
