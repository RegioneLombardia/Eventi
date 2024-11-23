<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

use open20\amos\admin\models\UserProfile;

use Yii;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
?>
<div class="box-widget-header">
    <div class="box-widget-wrapper">
        <h2 class="box-widget-title col-xs-10 nop">
            <?= AmosIcons::show('bar-chart', [], AmosIcons::DASH); ?>
            <span class="language-item"><?= \Yii::t('amosapp', 'Monitoraggio (real-time)'); ?></span>
        </h2>
    </div>
</div>


<div class="box-widget graphic-custom-monitor">
    <section>
        <div class="list-items">
            <h2 class="sr-only"><?= \Yii::t('amosapp', 'il mio profilo'); ?></h2>
            <div class="widget-listbox-option row list-items" role="option">
                <article class="wrap-item-box col-xs-12 nop">
<!--                    <div class="icon-admin-wgt">-->
                        <div class="col-xs-12 nop">
                            <?= GridView::widget([
                                'tableOptions' => ['class' => ''],
                                'dataProvider' => $dataProvider,
                                'layout' => "{items}\n{summary}\n{pager}",
                                'showPageSummary' => true,
                                'showFooter' => false,
                                'pageSummaryRowOptions' => ['class' => 'kv-page-summary summarystyle'],
                                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '0'],
                                'striped' => true,
                                'hover' => true,
                                'condensed' => true,
                                'responsiveWrap' => false,
                                //'persistResize' => true,
                                'floatHeader' => false,
                                'emptyTextOptions' => 'ciao',
                                'panel' => [
                                    'type' => GridView::TYPE_INFO, 'heading' => \Yii::t('monitors', 'Tabella dati'),
                                ],
                                'toggleDataOptions' => [
                                    'all' => [
                                        'label' => 'Tutto',
                                        'class' => 'btn btn-secondary'
                                    ],
                                ],
                                'columns' => [
                                    '0' => [
                                        'class' => '\kartik\grid\DataColumn',
                                        'attribute' => '0',
                                        'label' => \Yii::t('monitors', 'Classificazione dei partecipanti'),
                                        'format' => 'html',
                                        'hAlign' => 'left',
                                        'value' => function ($model) {
                                            return (string) "&nbsp;" . $model[0] . "&nbsp;";
                                        },
                                        //'pageSummary' => \Yii::t('monitors', 'Totale'),
                                        //'group' => true,
                                    ],
                                    '1' => [
                                        'class' => '\kartik\grid\DataColumn',
                                        'attribute' => '1',
                                        'label' => \Yii::t('monitors', 'Numero partecipanti'),
                                        'value' => function ($model) {
                                            return $model[1];
                                        },
                                        'hAlign' => 'center',
                                        //'pageSummary' => true,
                                    ],
                                ]
                            ]);
                            ?>
                        </div>
<!--                    </div>-->
                </article>
            </div>
        </div>
    </section>
</div>



<!--<div class="grid-item" style="min-height: 650px">-->
<!--  <div class="box-widget graphic-custom-monitor">-->
<!--    <div class="box-widget-toolbar dark-toolbar row nom">-->
<!--      <h2 class="box-widget-title col-xs-10 nop">< ?= \Yii::t('amosapp', 'Monitoraggio (real-time)'); ?></h2>-->
<!--    </div>-->
<!--      -->
<!--    <section><h2 class="sr-only">< ?= \Yii::t('amosapp', 'il mio profilo'); ?></h2>-->
<!--      <div role="listbox">-->
<!--        <div class="widget-listbox-option row list-items" role="option">-->
<!--          <article class="col-xs-12 nop">-->
<!--            <div class="icon-admin-wgt">-->
<!--              <div class="col-lg-12">-->
<!--              < ?= GridView::widget([-->
<!--                'tableOptions' => ['class' => 'bho'],-->
<!--                'dataProvider' => $dataProvider,-->
<!--                'layout' => "{items}\n{summary}\n{pager}",-->
<!--                'showPageSummary' => false,-->
<!--                'showFooter' => false,-->
<!--                'pageSummaryRowOptions' => ['class' => 'kv-page-summary summarystyle'],-->
<!--                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '0'],-->
<!--                'striped' => true,-->
<!--                'hover' => true,-->
<!--                'condensed' => true,-->
<!--                'responsiveWrap' => false,-->
<!--                //'persistResize' => true,-->
<!--                'floatHeader' => false,-->
<!--                'emptyTextOptions' => 'ciao',-->
<!--                'panel' => [-->
<!--                  'type' => GridView::TYPE_INFO, 'heading' => \Yii::t('monitors', 'Tabella dati'),-->
<!--                ],-->
<!--                'toggleDataOptions' => [-->
<!--                  'all' => [-->
<!--                    'label' => 'Tutto',-->
<!--                    'class' => 'btn btn-secondary'-->
<!--                  ],-->
<!--                ],-->
<!--                'columns' => [-->
<!--                  '0' => [-->
<!--                    'class' => '\kartik\grid\DataColumn',-->
<!--                    'attribute' => '0',-->
<!--                    'label' => \Yii::t('monitors', 'Classificazione dei partecipanti'),-->
<!--                    'format' => 'html',-->
<!--                    'hAlign' => 'left',-->
<!--                    'value' => function ($model) {-->
<!--                      return (string) "&nbsp;" . $model[0] . "&nbsp;";-->
<!--                    },-->
<!--                    //'pageSummary' => \Yii::t('monitors', 'Totale'),-->
<!--                    //'group' => true,-->
<!--                  ],-->
<!--                  '1' => [-->
<!--                    'class' => '\kartik\grid\DataColumn',-->
<!--                    'attribute' => '1',-->
<!--                    'label' => \Yii::t('monitors', 'Numero partecipanti'),-->
<!--                    'value' => function ($model) {-->
<!--                      return $model[1];-->
<!--                    },-->
<!--                    'hAlign' => 'center',-->
<!--                    //'pageSummary' => true,-->
<!--                  ],-->
<!--                ]-->
<!--              ]);-->
<!--              ?>-->
<!--              </div>-->
<!--            </div>-->
<!--          </article>-->
<!--        </div>-->
<!--      </div>-->
<!--    </section>-->
<!--  </div>-->
<!--</div>-->