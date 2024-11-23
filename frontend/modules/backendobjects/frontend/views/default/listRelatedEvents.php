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

     if ($dataProvider->getTotalCount() > 0) {
         $js = <<<JS
    $('.section-related-events').attr('style',"display:block !important");
JS;
         $this->registerJs($js);
         ?>
         <div>
             <div class="uk-container">
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
                             <?= Html::a(\Yii::t('site', 'Annulla'), [Yii::$app->controller->action->id, 'currentView' => Yii::$app->request->getQueryParam('currentView')], ['class' => 'btn btn-secondary']) ?>
                             <?= Html::submitButton(\Yii::t('site', 'Cerca'), ['class' => 'btn btn-primary']) ?>
                         </div>

                         <?php ActiveForm::end(); ?>
                     <?php } // !$withoutSearch
                     ?>

                     <div class="clearfix"></div>

                     <div class="slider-gruppo-eventi">

                         <div uk-slider>
                             <div class="uk-flex uk-flex-middle uk-margin-bottom">
                                 <div class="uk-slidenav-container uk-margin-auto-left">
                                     <a href="#" uk-slidenav-previous uk-slider-item="previous" title="<?= \Yii::t('site', "Indietro")?>"></a>
                                     <a href="#" uk-slidenav-next uk-slider-item="next" title="<?= \Yii::t('site', "Avanti")?>"></a>
                                 </div>
                             </div>


                             <?php

                             echo ListView::widget([
                                 'summary' => false,
                                 'dataProvider' => $dataProvider,
                                 'itemView' => '_item_related_events',
                                 'viewParams' => [
                                     'detailPage' => $detailPage,
                                     'viewFields' => $viewFields,
                                     'blockItemId' => $blockItemId,
                                 ],
                                 'options' => [
                                     'class' => 'uk-slider-items uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-4@m uk-grid',
                                 ]
                             ]);

                             ?>


                             <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                         </div>

                     </div>

                 </div>
             </div>
         </div>
     <?php }
     else {
         $js = <<<JS
    // $('.section-related-events').attr('style',"display:none !important");
JS;
         $this->registerJs($js);
     }
 }?>