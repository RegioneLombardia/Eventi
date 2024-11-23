<?php

use app\modules\backendobjects\frontend\Module;
use kartik\datecontrol\DateControl;
use luya\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use open20\amos\layout\assets\IsotopeAsset;

IsotopeAsset::register($this);

?>
<?php
if (!is_null($dataProvider)) {
    $dataProvider->pagination =  false;
//    $dataProvider->pagination->pageSize=  3;
//    $model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
//    $dataProvider->pagination->route = \open20\amos\events\utility\EventsUtility::getUrlLanding($model);

    if ($dataProvider->getTotalCount() > 0) {
        $js = <<<JS
    $('.section-protagonisti').attr('style',"display:block !important");
JS;
        $this->registerJs($js);
        ?>
        <?php if (!$withoutSearch) { ?>
            <?php
            $form = ActiveForm::begin([
                'action' => Url::toRoute(['/backendobjects']),
                'method' => 'get',
                'options' => [
                    'id' => 'element_form_' . $model->id,
                    'class' => 'form wrap-search',
                    'enctype' => 'multipart/form-data',
                ]
            ]);
            ?>

            <?php
            foreach ($searchFields as $field) {
                switch ($field->type) {
                    case "TEXT":
                        ?>
                        <div class="col-xs-12 col-sm-6 col-lg-4">
                            <?= $form->field($model, $field->name) ?>
                        </div>
                        <?php
                        break;
                    case "DATE":
                        ?>
                        <div class="col-xs-12 col-sm-6 col-lg-4">
                            <?=
                            $form->field($model, $field->name)->widget(DateControl::className(), [
                                'type' => DateControl::FORMAT_DATE
                            ])
                            ?>
                        </div>
                        <?php
                        break;
                }
            }
            ?>

            <div class="col-xs-12 wrap-btn">
                <?= Html::a(Module::t('Annulla'), [Yii::$app->controller->action->id, 'currentView' => Yii::$app->request->getQueryParam('currentView')], ['class' => 'btn btn-secondary']) ?>
                <?= Html::submitButton(Module::t('Cerca'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        <?php } // !$withoutSearch
        ?>


        <div class="clearfix"></div>

        <?php echo ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_itemProtagonist',
            'viewParams' => [
                'detailPage' => $detailPage,
                'viewFields' => $viewFields,
                'blockItemId' => $blockItemId,
            ],
            'options' => [
                'uk-grid' => 'masonry: true',
                'class' => 'list-view uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l',
            ],
        ]); ?>

    <?php } else {
        $js = <<<JS
    // $('.section-protagonisti').attr('style',"display:none !important");
    // $('.cta-protagonisti').attr('style',"display:none !important");
    // $('#menu-protagonisti').remove();
JS;
        $this->registerJs($js);
    }
}
?>
