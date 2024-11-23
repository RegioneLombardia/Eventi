<?php

use yii\widgets\ListView;

//use open20\design\assets\BootstrapItaliaDesignAsset;
use luya\helpers\Url;
use open20\amos\core\icons\AmosIcons;

//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use open20\amos\core\module\BaseAmosModule;
use open20\design\utility\DesignUtility;
use app\modules\cms\helpers\CmsHelper;

//$currentAsset = BootstrapItaliaDesignAsset::register($this);
$withPagination = true;
?>

    <style>
        /* clears the 'X' from Internet Explorer */
        input[type="search"]::-ms-clear {
            display: none;
            width: 0;
            height: 0;
        }

        input[type="search"]::-ms-reveal {
            display: none;
            width: 0;
            height: 0;
        }

        /* clears the 'X' from Chrome */
        input[type="search"]::-webkit-search-decoration,
        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-results-button,
        input[type="search"]::-webkit-search-results-decoration {
            display: none;
            -webkit-appearance: none;
        }
    </style>

<?php
$form = ActiveForm::begin([
    'action' => Url::toRoute(['/backendobjects']),
    'method' => 'get',
    'options' => [
        'id' => 'element_form_search',
        'class' => 'form wrap-search',
        'enctype' => 'multipart/form-data',
    ]
]);
?>
    <div class="container py-4">

<?= \yii\helpers\Html::a(AmosIcons::show('arrow-left') . \Yii::t('site', 'Torna indietro'), '/', [
    'class' => '',
    'title' => \Yii::t('site', 'Torna indietro')
]) ?>

    <h1><?= BaseAmosModule::t('amosapp', 'Cerca') ?></h1>

    <div class="mt-4 d-flex flex-column">
    <div class="full-text-src">

        <div class="input-search-event form-group">
            <?= \yii\helpers\Html::textInput('searchtext', \app\modules\cms\helpers\CmsHelper::itemQueryStringValue('searchtext'), [
                'id' => 'SearchDesign',
                'class' => 'form-control input-lg'
            ]) ?>
            <?= \yii\helpers\Html::submitButton(\open20\amos\core\icons\AmosIcons::show('search'), [
                'class' => 'btn btn-lg',
                'title' => \Yii::t('amosapp', "Cerca")
            ]) ?>
        </div>

        <!--dataprovider-->
        <div class="modulo-backend-search py-4">
            <div class="list-search-container d-flex flex-wrap">
                <?php
                if (!is_null($dataProvider)) {
                    if ($dataProvider->getTotalCount() > 0) {

                        //                    $listViewLayout = $this->render(
                        //                        '@vendor/open20/design/src/components/yii2/views/listViewLayoutBiList',
                        //                        [
                        //                            'customListClass' => 'list-search'
                        //                        ]
                        //                    );
                        echo ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemView' => '_itemListSearchDesign',
                            'viewParams' => [
                                'detailPage' => $detailPage,
                                'viewFields' => $viewFields,
                                'blockItemId' => $blockItemId,
                            ],
                            //                        'layout' => $listViewLayout,
                            //                        'summary' => DesignUtility::getLayoutSummary($withPagination),
                            //                        'pager' => DesignUtility::listViewPagerConfig(),
                            'itemOptions' => [
                                'tag' => false
                            ],
                            'options' => [
                                'class' => 'list-view w-100'
                            ]
                        ]);
                    } else { ?>
                        <div class="no-result">
                            <p><?= BaseAmosModule::t('amosapp', 'Non sono presenti risultati di ricerca') ?></p>
                        </div>
                    <?php }
                } else { ?>
                    <div class="no-result">
                        <p><?= BaseAmosModule::t('amosapp', 'Per effettuare una ricerca inserisci nella riga in alto parole o frasi quali tag, argomenti, titoli, community,... e premi INVIO sulla tastiera oppure fai clic sull\'icona della lente.') ?></p>
                        <div>
                            <?php
                            $url = '/img/cerca.png';

                            $contentImage = CmsHelper::img($url, [
                                'class' => 'el-image',
                            ]);
                            ?>
                            <?= $contentImage; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>


<?php ActiveForm::end();
