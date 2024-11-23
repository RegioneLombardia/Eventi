<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\views\sondaggi
 * @category   CategoryName
 */

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\forms\ItemAndCardHeaderWidget;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\sondaggi\AmosSondaggi;

/**
 * @var \open20\amos\sondaggi\models\Sondaggi $model
 */

$isCommunityManager = false;
if(!empty(\Yii::$app->getModule('community'))) {
    $isCommunityManager = \open20\amos\community\utilities\CommunityUtil::isLoggedCommunityManager();
}
?> 

<div class="listview-container documents">
    <div class="post-horizonatal">
        <?= ItemAndCardHeaderWidget::widget([
            'model' => $model,
            'publicationDateField' => 'created_at',
        ]); ?>
        <div class="col-sm-7 col-xs-12 nop">
            <div class="post-content col-xs-12 nop">
                <div class="post-title col-xs-10">
                    <a href="<?= $model->getFullViewUrl() ?>">
                        <h2><?= $model->titolo ?></h2>
                    </a>
                </div>
                <?= ContextMenuWidget::widget([
                    'model' => $model,
                    'actionModify' => "/sondaggi/sondaggi/update?id=" . $model->id,
                    'actionDelete' => "/sondaggi/sondaggi/delete?id=" . $model->id,
                    'mainDivClasses' => 'col-xs-1 nop'
                ]) ?>
                <div class="clearfix"></div>
                <div class="row nom post-wrap">
                    <?php
                    $url = '/img/img_default.jpg';
                    if ($model->file) {
                        $url = $model->file->getUrl('original');
                    }
                    ?>
                    <div class="post-image-left nop col-sm-3 col-xs-12">
                        <?= Html::img($url, [
                            'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio'),
                            'class' => 'img-responsive'
                        ]); ?>
                    </div>
                    <div class="post-text m-b-15">
                        <p>
                            <?php
                            if (strlen($model->descrizione) > 300) {
                                $stringCut = substr($model->descrizione, 0, 300);
                                echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                            } else {
                                echo $model->descrizione;
                            }
                            ?>
                            <a class="underline" href="<?= $model->getFullViewUrl() ?>"><?= AmosSondaggi::tHtml('amossondaggi', 'Leggi tutto') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar col-sm-5 col-xs-12">
            <div class="container-sidebar">
                <div class="box">
                    <h4 class="title-sidebar-list"><?= AmosSondaggi::t('amossondaggi', 'Dettagli') ?></h4>
                    <p><strong><?= AmosSondaggi::t('amossondaggi', 'Partecipanti') ?>:</strong> <?= $model->getNumeroPartecipazioni() ?></p>
                    <p>
                        <strong><?= AmosSondaggi::t('amossondaggi', 'Stato') ?>:</strong> <?= $model->hasWorkflowStatus() ? $model->getWorkflowStatus()->getLabel() : '--'; ?>
                    </p>
                </div>

                <div class="box">
                    <h4 class="title-sidebar-list"><?= AmosSondaggi::t('amossondaggi', 'Azioni') ?></h4>
                    <div class="clearfix"></div>
                    <div class="sidebar-actions">
                        <ul>
                            <li>
                                <?php
                                $url = \yii\helpers\Url::current();
                                $partecipazioni = $model->getNumeroPartecipazioni();
                                if (\Yii::$app->getUser()->can('SONDAGGI_READ', ['model' => $model]) && $partecipazioni) {
                                    echo Html::a(AmosIcons::show('bar-chart', ['class' => 'btn btn-tool-secondary'], 'dash'), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi/risultati',
                                        'id' => $model->id,
                                        //'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Risultati del sondaggio'),
                                    ]);
                                }
                                ?>
                            </li>
                            <li>
                                <?php
                                /** @var \open20\amos\sondaggi\models\search\SondaggiSearch $model */
                                $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') ||
                                    \Yii::$app->getUser()->can('SONDAGGI_UPDATE', ['model' => $model])) {
                                    echo Html::a(AmosIcons::show('collection-item', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi/clone',
                                        'id' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Duplica sondaggio'),
                                        'data-confirm' => AmosSondaggi::t('amossondaggi','Sei sicuro di voler duplicare  il sondaggio?')

                                    ]);
                                }
                                ?>
                            </li>
                            <li>
                                <?php
                                /** @var \open20\amos\sondaggi\models\search\SondaggiSearch $model */
                                $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGIDOMANDEPAGINE_READ', ['model' => $model])) {
                                    echo Html::a(AmosIcons::show('book', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi-domande-pagine/index',
                                        'idSondaggio' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Gestisci pagine'),
                                    ]);
                                }
                                ?>
                            </li>
                            <li>
                                <?php
                                $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGIDOMANDE_READ', ['model' => $model])) {
                                    if ($model->getSondaggiDomandes()->count() == 0) {
                                        $url = Yii::$app->urlManager->createUrl(['/' . $this->context->module->id . '/sondaggi-domande-pagine/index', 'idSondaggio' => $model->id, 'url' => yii\helpers\Url::current()]);
                                        echo Html::a(AmosIcons::show('playlist-plus', ['class' => 'btn btn-tool-secondary', 'style' => 'color:red;']), Yii::$app->urlManager->createUrl([
                                            '/' . $this->context->module->id . '/sondaggi-domande/index',
                                            'idSondaggio' => $model->id,
                                            'url' => $url,
                                        ]), [
                                            'title' => AmosSondaggi::t('amossondaggi', 'Gestisci domande - E\' necessario aggiungere delle domande al sondaggio.'),
                                        ]);
                                    } else {
                                        $url = Yii::$app->urlManager->createUrl(['/' . $this->context->module->id . '/sondaggi-domande-pagine/index', 'idSondaggio' => $model->id, 'url' => yii\helpers\Url::current()]);
                                        echo Html::a(AmosIcons::show('playlist-plus', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                            '/' . $this->context->module->id . '/sondaggi-domande/index',
                                            'idSondaggio' => $model->id,
                                            'url' => $url,
                                        ]), [
                                            'title' => AmosSondaggi::t('amossondaggi', 'Gestisci domande'),
                                        ]);
                                    }
                                }
                                ?>
                            </li>
                            <li>
                                <?php $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || $isCommunityManager) {
                                    echo Html::a(AmosIcons::show('download', ['class' => 'btn btn-tool-secondary btn-sondaggi-download',
                                        'data' => [
                                            'id' => $model->id,]
                                        ]), "#", [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Download Excel'),
                                    ]);
                                }
                                ?>
                            </li>
                            <li>
                                <?php $url = \yii\helpers\Url::current();
                                if (\Yii::$app->getUser()->can('AMMINISTRAZIONE_SONDAGGI') || \Yii::$app->getUser()->can('SONDAGGI_DELETE', ['model' => $model])) {
                                    echo Html::a(AmosIcons::show('delete', ['class' => 'btn btn-tool-secondary']), Yii::$app->urlManager->createUrl([
                                        '/' . $this->context->module->id . '/sondaggi/delete',
                                        'id' => $model->id,
                                        'url' => $url,
                                    ]), [
                                        'title' => AmosSondaggi::t('amossondaggi', 'Cancella'),
                                    ]);
                                }
                                ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="col-xs-12 list-primary-btn">
            < ?= Html::a(AmosSondaggi::t('amossondaggi', 'Nuovo sondaggio'), ['create', 'url' => yii\helpers\Url::current()], ['class' => 'btn btn-success']); ?>
            < ?= Html::a(AmosSondaggi::t('amossondaggi', 'Nuovo sondaggio (Wizard)'), ['create'], ['class' => 'btn btn-success']); ?>
        </div> -->

    </div>
</div>
