<?php

use app\modules\backendobjects\frontend\Module;
use kartik\datecontrol\DateControl;
use luya\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

?>
<?php if (!is_null($dataProvider)) {
    if ($dataProvider->getTotalCount() > 0) {

        $js = <<<JS
    $('.section-news').attr('style',"display:block !important");
JS;
        $this->registerJs($js);
        $model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
        $landing =  \app\modules\cms\utility\CachedEvent::getModelLanding();
//        $model = \backend\modules\eventsadmin\utility\EventsAdminUtility::getModelEventFormLuya($parms);
//        if($model){
//            $landing = $model->eventLanding;
//        }

        $title =  \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_info');
        if($landing && !empty($landing->title_info)){
            $title = $landing->title_info;
        }
        ?>
        <div class="<?= $cssClass ?>">
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
            <div class="slider-news-home">
                <h2><?= $title ?></h2>

        <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slider>
                        <?php
                        echo ListView::widget([
                            'summary' => false,
                            'dataProvider' => $dataProvider,
                            'itemView' => '_itemNewsHp',
                            'viewParams' => [
                                'detailPage' => $detailPage,
                                'viewFields' => $viewFields,
                                'blockItemId' => $blockItemId,
                            ],
                            'options' => [
                                'class' => 'uk-slider-items uk-grid',
                            ]
                        ]);
                        ?>

                        <div class="uk-flex uk-flex-middle uk-margin-top">
                            <div class="uk-slidenav-container uk-margin-right">
                                <a href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                                <a href="#" uk-slidenav-next uk-slider-item="next"></a>
                            </div>
                            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                        </div>

                    </div>
                </div>
            </div>
        <?php
    } else {
        $js = <<<JS
    //$('.section-news').attr('style',"display:none !important");
    //$('#menu-news').remove();
JS;
        $this->registerJs($js);
    }
}
?>