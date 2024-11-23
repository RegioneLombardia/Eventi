<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
use app\modules\backendobjects\frontend\Module;
use kartik\datecontrol\DateControl;
use luya\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use open20\amos\layout\assets\IsotopeAsset;


$js = <<<JS
    $('#btn-go-to-reg').on('click', function(e){
        e.preventDefault();
        var url = $('#hour-time-period').val();
        if(url !== '' && url !== undefined){
            window.location.href = url;
        }
        else {
            alert("E' necessario scegliere un orario!");
        }
    });
JS;

$this->registerJs($js);
?>
<div id="timePeriods-container">

    <div class="<?= $cssClass ?>">
        <div class="clearfix"></div>

        <?php
        //pr($dataProvider->query->createCommand()->rawSql);

        $query = $dataProvider->query->select('event.id, begin_date_hour')->groupBy('event.id, begin_date_hour');
        $eventsDateArray = $query->asArray()->all();
        $dates = [];
        $datesEu = [];

        foreach ($eventsDateArray as $date) {
            $explode = explode(' ', $date['begin_date_hour']);
//                pr($dates, $explode[0]);
            if (!in_array($explode[0], $datesEu)) {
                $beginDate = new \DateTime($explode[0]);
                $dates[$date['id']] = $beginDate->format('d/m/Y');
                $datesEu[$date['id']] = $beginDate->format('Y-m-d');
            }
        }
        ?>

        <div class="form-section uk-section section-landing-cms-form green-blu-form">

            <div class="uk-container">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <h3 class="form-title"><?= \Yii::t('site', "Scegli l'orario e registrati") ?></h3>
                    </div>
                </div>
                <div class="col-md-12" style="margin-left:10px;">
                    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>
                    <div class="col-md-6 form-group">
                        <label style="color:white;" class="control-label"><?= \Yii::t('site', "Data") ?></label>
                        <?= \kartik\select2\Select2::widget([
                            'name' => 'date-period',
                            'options' => [
                                'id' => 'date-period',
                                'placeholder' => \Yii::t('site', "Seleziona una data"),
                            ],
                            'data' => $dates
                        ]);
                        ?>
                    </div>

                    <div class="col-md-6 form-group">
                        <label style="color:white;" class="control-label"><?= \Yii::t('site', "Orario") ?></label>
                        <?= \kartik\depdrop\DepDrop::widget([
                            'name' => 'hour-time-period',
                            'options' => [
                                'id' => 'hour-time-period',
                                'disabled' => false,
                                'placeholder' => \Yii::t('site', 'Orario'),
                                'title' => \Yii::t('site', 'Orario'),
                            ],
                            'select2Options' => [
                                'pluginOptions' => ['allowClear' => true, 'title' => \Yii::t('site', "per selezionare l'ora scegliere prima una data")],
                                'options' => ['title' => \Yii::t('site', 'per selezionare il comune impostare prima una provincia')]
                            ],
                            'pluginOptions' => [
                                'depends' => ['date-period'],
                                'placeholder' => ['Seleziona ...'],
//                            'title' => 'per selezionare il comune impostare prima una provincia',
                                'url' => \yii\helpers\Url::to(['/events/event-dashboard/hour-by-date']),
//                            'params' => ['nascita_comuni_id-id'],
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-12 uk-form-controls">
                        <?= Html::a(\Yii::t('site', "Vai all'evento"), '#', [
                            'class' => 'btn btn-primary',
                            'id' => 'btn-go-to-reg'
                        ]) ?>

                    </div>
                    <?php \yii\bootstrap\ActiveForm::end(); ?>

                </div>
            </div>
        </div>
</div>
