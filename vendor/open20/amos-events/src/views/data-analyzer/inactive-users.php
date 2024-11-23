<?php
/**
 * @var $dataProviderSex \yii\data\ActiveDataProvider
 * @var $dataProviderAge
 * @var $dataProviderProvincia
 */

use open20\amos\events\AmosEvents;
use yii\bootstrap\ActiveForm;
use kartik\file\FileInput;

$this->title = AmosEvents::t('amosevents', "Analisi dati - Utenti inattivi");
$this->params['breadcrumbs'][] = $this->title;

$lastImportDate = AmosEvents::t('amosevents', 'nessuna');
if (!empty($last_import_date)) {
    $importDateTime = new \DateTime($last_import_date);
    $lastImportDate = $importDateTime->format('d/m/Y h:i');
}
?>
<div>
    <?php
    $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
        ]
    ]);
    ?>
    <p><?= AmosEvents::t('amosevents', "Ultima data di importazione: <strong>{date}</strong>", ['date' => $lastImportDate]) ?></p>
    <div class="row">
        <div class="col-md-12">
            <?php echo kartik\file\FileInput::widget([
                'name' => 'import-file',
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => true,
                    'showUpload' => false,
                    'showCancel' => false
                ]
            ]);
            ?>
        </div>

    </div>

    <div class="row mt-3">
        <div class="text-center col-md-12">
            <?=
            \yii\helpers\Html::submitButton(Yii::t('amosevents', 'Carica file e mostra statistiche'), [
                'class' => 'btn btn-primary',
                'name' => 'publish',
                'title' => Yii::t('amosevents', 'Importa')
            ])
            ?>
        </div>
    </div>
    <?= \open20\amos\core\helpers\Html::hiddenInput('is_sent', true) ?>

    <div class="mt-3">
        <h4><?= AmosEvents::t('amosevents', "Statistiche utenti inattivi") ?></h4>

        <div>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderSex,
                'columns' => [
                    [
                        'label' => "Sesso",
                        'value' => function ($model) {
                            if (empty($model['label'])) {
                                return 'ND';
                            }
                            return $model['label'];
                        }
                    ],
                    [
                        'attribute' => 'n',
                        'label' => "N. utenti inattivi",
                    ],

                ]
            ]) ?>
        </div>
        <br>
        <div>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderAge,
                'columns' => [
                    [
                        'label' => "Fascia d'età",
                        'value' => function ($model) {
                            if (empty($model['label'])) {
                                return 'ND';
                            }
                            return $model['label'];
                        }
                    ],
                    [
                        'attribute' => 'n',
                        'label' => "N. utenti inattivi",
                    ],

                ]
            ]) ?>
        </div>
        <br>

        <div>
            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderProvincia,
                'columns' => [
                    [
                        'value' => function ($model) {
                            if (empty($model['label'])) {
                                return 'ND';
                            }
                            return $model['label'];
                        },
                        'label' => "Province",

                    ],
                    [
                        'attribute' => 'n',
                        'label' => "N. utenti inattivi",
                    ],
                ]
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="clearfix"></div>
</div>
