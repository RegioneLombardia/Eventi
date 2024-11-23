<?php
use open20\webmeeting\WebMeetingModule;

use open20\amos\core\helpers\Html;

use kartik\datecontrol\DateControl;
use kartik\widgets\SwitchInput;
use kartik\widgets\Select2;
?>
<hr style="border:1px dashed #ccc; margin:20px 0">

<div class="col-xs-12 ">
    <?php
        $passwordInput = Html::tag(
            'div',
            $form->field($model, 'password')->textInput(['maxlength' => true]),
            ['class' => 'col-md-6 col-xs-12']
        );

        $emailHost = Html::tag(
            'div',
            $form->field($model, 'host_email')->widget(Select2::class, [
                'data' => $coHostUserEmail,
                'options' => [
                    'placeholder' => WebMeetingModule::_t('#choose_host_email'),
                    'multiple' => false,
                    'autocomplete' => 'off',
                ],
            ]),
            ['class' => 'col-md-6 col-xs-12']
        );
    ?>
    <div class="row">
        <h2 class="subtitle-form col-xs-12"><?= WebMeetingModule::_t('#advanced_configurations') ?></h2>
        <?php echo($passwordInput) ?>
        <?php echo($emailHost) ?>
    </div>
    
</div>
<div class="col-xs-12">
<?= $form->field($model, 'timezone')->widget(Select2::class, [
    'data' => $timezone,
        'options' => [
            'placeholder' => WebMeetingModule::_t('#choose_timezone'),
            'multiple' => false,
            'autocomplete' => 'off',
        ],
    ])
?>
</div>