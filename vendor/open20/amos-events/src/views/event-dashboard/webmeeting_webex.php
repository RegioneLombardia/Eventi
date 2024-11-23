<?php

use open20\amos\events\AmosEvents;
use yii\bootstrap4\ActiveForm;
//use open20\amos\core\forms\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use open20\webmeeting\WebMeetingModule;
use open20\amos\layout\utility\AmosIconsBi;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;


$this->title = AmosEvents::t('amosevents', 'Configurazioni Webmeeting Webex');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);


$eventType = $model->eventType;
$eventTypePresent = !is_null($eventType);
$eventTypeWithLimitedSeats = ($eventTypePresent && $eventType->limited_seats);
$js = <<<JS

    $('#switch-settings').on('switchChange.bootstrapSwitch', function(){
        if($(this).is(':checked')){
            $('#webmeeting-assets-view').show();
        }
        else {
           $('#webmeeting-assets-view').hide();
        }
    });

JS;
$this->registerJs($js);

$textError = '';
if (!$webmeeting->isNewRecord) {
    $textError = \open20\amos\events\utility\EventsUtility::errorsUpdateWebmeeting($webmeeting, $model);
}

?>
<?php
if ($textError) {
    ?>
    <div class="alert alert-warning" role="alert">
        <?= $textError ?>
    </div>
<?php } ?>

<div class="dove-evento my-4">
    <?php $form = ActiveForm::begin(); ?>

    <?= $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_form_webmeeting_config', [
        'form' => $form,
        'model' => $model,
        'webmeeting' => $webmeeting,
    ]) ?>

    <div class="row mt-5">
        <div class="col-md-3 mr-auto pb-3">
            <div class="text-muted d-flex">
                <div class="mr-2">
                    <svg class="icon icon-secondary">
                        <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_settings"></use>
                    </svg>
                </div>

                <h5 class="font-weight-bold"><?= AmosEvents::t('amosevents', "Informazioni Webex") ?></h5>
            </div>
        </div>
        <div class="col-md-8 pb-3">
            <?= \kartik\widgets\SwitchInput::widget([
                'name' => 'switch-setting',
                'options' => ['id' => 'switch-settings'],
                'pluginOptions' => [
                    //'size' => 'mini',
                    //                    'onText' => AmosIcons::show('check-circle'),
                    //                    'offText' => AmosIcons::show('circle-o')
                    'onText' => '+',
                    'offText' => '-'
                ],
            ]) ?>
        </div>
    </div>

    <div id="webmeeting-assets-view" style="display:none">
        <?= \yii\widgets\DetailView::widget([
            'model' => $webmeeting,
            'attributes' => [
                'id',
                'title',
                'agenda:ntext',
                'meeting_number',
                'meeting_id',
                'meeting_type',
                'state',
                'timezone',
                'host_user_id',
                'host_display_name',
                'host_email',
                'host_key',
                'site_url',
                'web_link',
                'password',
                'sip_address',
                'dial_in_ip_address',
                'enabled_auto_record_meeting',
                'allow_any_user_to_be_co_host',
                'telephony',
                'created_at',
                'created_by' => [
                    'attribute' => 'created_by',
                    'label' => WebMeetingModule::_t('#idx_created_by'),
                    'value' => function ($model) {
                        return empty($model->createdUserProfile->id)
                            ? '-'
                            : Html::a(
                                $model->createdUserProfile->getNomeCognome(),
                                ['/admin/user-profile/view', 'id' => $model->createdUserProfile->id],
                                [
                                    'title' => WebMeetingModule::_t('#idx_open_profile', [
                                        'nome_profilo' => $model->createdUserProfile->getNomeCognome()
                                    ])
                                ]
                            );
                    },
                    'format' => 'html'
                ],
            ],
        ]) ?>
    </div>

    <?php if (empty($textError)) { ?>
        <div class="buttons row">
            <?php echo Html::submitButton(AmosEvents::t('amosevents', 'Salva le modifiche'), ['class' => 'btn btn-primary ml-auto']) ?>
        </div>
    <?php } ?>
    <?php $form = ActiveForm::end(); ?>
</div>
