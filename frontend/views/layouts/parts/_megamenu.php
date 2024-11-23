<?php

use yii\helpers\Html;

if (!empty($fromBackend)) {
    $currentUrl = \yii\helpers\Url::current();
}

$classActivePl = '';
$classActiveGp = '';
if (empty($fromBackend)) {

    if (Yii::$app->menu->current->getAlias() == 'palazzo-lombardia') {
        $classActivePl = 'active';
    }
    if (Yii::$app->menu->current->getAlias() == 'grattacielo-pirelli') {
        $classActiveGp = 'active';
    }
}
?>
    <div id="megamenu-eventi">
        <div class="content-top-megamenu">

            <div class="left-megamenu">
                <a class="btn-megamenu" role="button" data-toggle="collapse" href="#sub-megamenu" aria-expanded="false"
                   aria-controls="sub-megamenu">
                    <span class="icon mdi mdi-menu"></span><span
                            class="uk-visible@s"><?= Html::encode(Yii::$app->name) ?></span>
                </a>
            </div>
            <div class="right-megamenu">
                <?php if (empty($fromBackend)) { ?>
                    <div class="current-page uk-visible@s">
                        <?= Yii::$app->menu->current->getTitle() ?>
                    </div>
                <?php } ?>
                <a class="logo-regione" href="https://www.regione.lombardia.it/" target="_blank"
                   title="<?= \Yii::t('app', 'Regione Lombardia'); ?>">
                    <img src="<?= $assetBundle->baseUrl ?>/img/logo_regione_lombardia-bianco.svg"
                         alt="<?= \Yii::t('app', 'Logo Regione Lombardia'); ?>" class="logo-bianco img-responsive">
                </a>
            </div>
        </div>

        <div class="content-bottom-megamenu collapse" id="sub-megamenu">
            <div class="row">
                <div class="col-md-9 ">
                    <div class="content-eventi">
                        <div class="hedear-content-eventi">
                            <div class="titoletto">In evidenza</div>
                            <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() !== 'esplora-eventi') { ?>
                                <div class=""><a
                                            href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/esplora-eventi" ?>"
                                            title="<?= \Yii::t('app', "Tutti gli eventi") ?><"><?= \Yii::t('app', "Tutti gli eventi") ?>
                                        ></a></div>
                            <?php } ?>
                        </div>
                        <div class="preview-eventi">
                            <?php
                            $modelSearch = new open20\amos\events\models\search\EventSearch();
                            $dataProvider = $modelSearch->cmsPublishedSearch([]);
                            $dataProvider->query->limit(4);
                            $models = $dataProvider->models;
                            foreach ($models as $model) {
                                echo $this->render('@frontend/modules/backendobjects/frontend/views/default/_itemMegamenu', ['model' => $model]);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="content-link-palazzi">
                        <div class="box-palazzo <?= $classActivePl ?>">
                            <div class="icon-palazzo">
                                <img src="<?= $assetBundle->baseUrl ?>/img/icon-pl.png"
                                     alt="<?= \Yii::t('app', 'Palazzo Lombardia'); ?>" class="img-responsive">
                            </div>
                            <div class="text-palazzo">
                                <div>
                                    <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/palazzo-lombardia" ?>"
                                       title="<?= \Yii::t('app', "Palazzo Lombardia") ?>"
                                       class="link-palazzo"><?= \Yii::t('app', "Palazzo Lombardia") ?></a>
                                </div>
                                <div class="info-palazzo">
                                    <p><?= \Yii::t('app', "Sede della Giunta regionale") ?></p>
                                    <a href="#ModalInfoPalazzo" data-toggle="modal"
                                       data-target="#ModalInfoPalazzo"><span
                                                class="mdi mdi-24px mdi-information"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="box-palazzo <?= $classActiveGp ?>">
                            <div class="icon-palazzo">
                                <img src="<?= $assetBundle->baseUrl ?>/img/icon-gr.png"
                                     alt="<?= \Yii::t('app', 'Grattacielo Pirelli'); ?>" class="img-responsive">
                            </div>
                            <div class="text-palazzo">
                                <div>
                                    <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/grattacielo-pirelli" ?>"
                                       title="<?= \Yii::t('app', "Grattacielo Pirelli") ?>"
                                       class="link-palazzo"><?= \Yii::t('app', "Grattacielo Pirelli") ?></a>
                                </div>
                                <div class="info-palazzo">
                                    <p><?= \Yii::t('app', "Sede del Consiglio regionale") ?></p>
                                    <a href="#ModalInfoGp" data-toggle="modal" data-target="#ModalInfoGp"><span
                                                class="mdi mdi-24px mdi-information"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-megamenu">
                <div class="copyleft">
                    <p>
                        © <?= \Yii::t('app', 'Copyleft Regione Lombardia tutti i diritti riservati - 80050050154 - Piazza Città di Lombardia 1 - 20124 Milano'); ?>
                    </p>
                </div>
                <div class="link-credits">
                    <a data-toggle="collapse" href="#linkCredits" role="button" aria-expanded="false"
                       aria-controls="linkCredits">
                        Informazioni legali e crediti
                    </a>
                </div>
            </div>
            <div class="collapse" id="linkCredits">
                <div class="card card-body">
                    <ul class="list-inline">

                        <?php
                        if(!empty($fromBackend)){?>
                            <li><a class="nav-link" href="<?=  \Yii::$app->params['platform']['frontendUrl'].'/responsabile-della-pubblicazione-credits-e-contatti'?>" title="<?=\Yii::t('app', 'Responsabile Della Pubblicazione, Credits E Contatti')?>"><?=\Yii::t('app', 'Responsabile Della Pubblicazione, Credits E Contatti')?></a></li>
                            <li><a class="nav-link" href="<?=  \Yii::$app->params['platform']['frontendUrl'].'/privacy' ?>" title="<?=\Yii::t('app', 'Privacy')?>"><?=\Yii::t('app', 'Privacy')?></a></li>
                            <li><a class="nav-link" href="<?=  \Yii::$app->params['platform']['frontendUrl'].'/note-legali-e-cookie-policy' ?>" title="<?=\Yii::t('app', 'Note Legali E Cookie Policy')?>"><?=\Yii::t('app', 'Note Legali E Cookie Policy')?></a></li>
                            <li><a class="nav-link" href="<?=  \Yii::$app->params['platform']['backendUrl'].'/admin/user-profile/personalize-cookie?urlRedirect='.urlencode(\Yii::$app->request->absoluteUrl) ?>" title="<?=\Yii::t('app', 'Impostazione Cookie')?>"><?=\Yii::t('app', 'Impostazione Cookie')?></a></li>
                            <li><a class="nav-link" href="<?=  \Yii::$app->params['platform']['frontendUrl'].'/tickets/contacts' ?>" target="_blank" title="<?=\Yii::t('app', 'Assistenza tecnica')?>"><?=\Yii::t('app', 'Assistenza tecnica')?></a></li>
                        <?php } else {
                            $numItems = count(Yii::$app->menu->findAll(['depth' => 1, 'container' => 'footer']));
                            $i = 0;
                            ?>

                            <?php foreach (Yii::$app->menu->findAll(['depth' => 1, 'container' => 'footer']) as $item) : /* @var $item \luya\cms\menu\Item */ ?>
                                <li class="list-inline-item">
                                    <a href="<?= $item->link; ?>" target="_blank"
                                       title="<?= $item->title ?>"><?= $item->title; ?></a>
                                </li>
                            <?php endforeach;
                        }?>
                        <li class="list-inline-item"><a
                                    href="<?= \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/personalize-cookie?urlRedirect=' . urlencode(\Yii::$app->request->absoluteUrl) ?>"
                                    title="<?= \Yii::t('app', 'Impostazione Cookie') ?>"><?= \Yii::t('app', 'Impostazione Cookie') ?></a>
                        </li>
                        <li class="list-inline-item"><a href="<?= '/tickets/contacts' ?>" target="_blank"
                                                        title="<?= \Yii::t('app', 'Assistenza tecnica') ?>"><?= \Yii::t('app', 'Assistenza tecnica') ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php?>