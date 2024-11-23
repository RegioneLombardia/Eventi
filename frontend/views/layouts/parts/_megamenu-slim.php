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
<div id="megamenu-slim-eventi">
  <div class="content-top-megamenu">

    <div class="left-megamenu">
      <a class="btn-megamenu" role="button" data-toggle="collapse" href="#sub-megamenu" aria-expanded="true" aria-controls="sub-megamenu">
        <span class="icon mdi mdi-menu"></span>
      </a>
      <div class="uk-visible@s title-page-footer">
        <?php if (empty($fromBackend)) { ?>
          <span class="uk-margin-small-right"><?= Yii::$app->menu->current->getTitle() ?> </span>
            <?php
            if(\Yii::$app->user->can('ADMIN')) {
                $urlUpdateEvent = \backend\modules\eventsadmin\utility\EventsAdminUtility::getUrlUpdateEventFromAlias(Yii::$app->menu->current->getAlias());
                if(!empty($urlUpdateEvent)) {
                    echo \open20\amos\core\helpers\Html::a(\open20\amos\core\icons\AmosIcons::show('square-editor', [], 'dash'), $urlUpdateEvent, [
                        'class' => 'btn btn-tools-secondary',
                        'style' => 'text-decoration: none;font-size: large;',
                        'title' => \Yii::t('site', "Modifica evento")
                    ]);
                }
            }
            ?>
          <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() == 'palazzo-lombardia') { ?>
            <a href="#ModalInfoPalazzo" data-toggle="modal" data-target="#ModalInfoPalazzo"><span class="mdi mdi-information"></span></a>
          <?php } ?>
          <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() == 'grattacielo-pirelli') { ?>
            <a href="#ModalInfoGp" data-toggle="modal" data-target="#ModalInfoGp"><span class="mdi mdi-information"></span></a>
          <?php } ?>
        <?php } else { ?>
          <span class="uk-margin-small-right"><?= $this->title ?> </span>
        <?php  } ?>
      </div>
    </div>
    <div class="right-megamenu">
      <div class="link-menu">
        <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() !== 'palazzo-lombardia') { ?>
          <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/palazzo-lombardia" ?>" title="<?= \Yii::t('app', "Palazzo Lombardia") ?>" class="current-page"><?= \Yii::t('app', "Palazzo Lombardia") ?></a>
        <?php } ?>
        <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() !== 'grattacielo-pirelli') { ?>
          <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/grattacielo-pirelli" ?>" title="<?= \Yii::t('app', "Grattacielo Pirelli") ?>" class="current-page"><?= \Yii::t('app', "Grattacielo Pirelli") ?></a>
        <?php } ?>
        <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() !== 'esplora-eventi') { ?>
          <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/esplora-eventi" ?>" title="<?= \Yii::t('app', "Tutti gli eventi") ?>" class="current-page"><?= \Yii::t('app', "Tutti gli eventi") ?></a>
        <?php } ?>
      </div>
      <a class="logo-regione" href="https://www.regione.lombardia.it/" target="_blank" title="<?= \Yii::t('app', 'Regione Lombardia'); ?>">
        <img src="<?= $assetBundle->baseUrl ?>/img/logo_regione_lombardia-bianco.svg" alt="<?= \Yii::t('app', 'Logo Regione Lombardia'); ?>" class="logo-bianco img-responsive">
      </a>
    </div>
  </div>

  <div class="content-bottom-megamenu-slim collapse in" id="sub-megamenu">
    <div class="menu-bottom">

      <div class="link-menu">
        <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() !== 'palazzo-lombardia') { ?>
          <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/palazzo-lombardia" ?>" title="<?= \Yii::t('app', "Palazzo Lombardia") ?>" class="current-page"><?= \Yii::t('app', "Palazzo Lombardia") ?></a>
        <?php } ?>
        <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() !== 'grattacielo-pirelli') { ?>
          <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/grattacielo-pirelli" ?>" title="<?= \Yii::t('app', "Grattacielo Pirelli") ?>" class="current-page"><?= \Yii::t('app', "Grattacielo Pirelli") ?></a>
        <?php } ?>
        <?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() !== 'esplora-eventi') { ?>
          <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/esplora-eventi" ?>" title="<?= \Yii::t('app', "Tutti gli eventi") ?>" class="current-page"><?= \Yii::t('app', "Tutti gli eventi") ?></a>
        <?php } ?>
      </div>
    </div>
    <div class="footer-megamenu">
      <div class="copyleft">
        <p>
          © <?= \Yii::t('app', 'Copyleft Regione Lombardia tutti i diritti riservati - 80050050154 - Piazza Città di Lombardia 1 - 20124 Milano'); ?>
        </p>
      </div>
      <div class="link-credits">
        <a data-toggle="collapse" href="#linkCredits" role="button" aria-expanded="false" aria-controls="linkCredits">
          Informazioni legali e assistenza
        </a>
      </div>
    </div>
    <div class="collapse" id="linkCredits">
      <div class="card card-body">
        <ul class="list-inline">

          <?php
          if (!empty($fromBackend)) { ?>
            <li><a class="nav-link" href="<?= \Yii::$app->params['platform']['frontendUrl'] . '/responsabile-della-pubblicazione-credits-e-contatti' ?>" title="<?= \Yii::t('app', 'Responsabile Della Pubblicazione, Credits E Contatti') ?>"><?= \Yii::t('app', 'Responsabile Della Pubblicazione, Credits E Contatti') ?></a></li>
            <li><a class="nav-link" href="<?= \Yii::$app->params['platform']['frontendUrl'] . '/privacy' ?>" title="<?= \Yii::t('app', 'Privacy') ?>"><?= \Yii::t('app', 'Privacy') ?></a></li>
            <li><a class="nav-link" href="<?= \Yii::$app->params['platform']['frontendUrl'] . '/note-legali-e-cookie-policy' ?>" title="<?= \Yii::t('app', 'Note Legali E Cookie Policy') ?>"><?= \Yii::t('app', 'Note Legali E Cookie Policy') ?></a></li>
          <?php } else {
            $numItems = count(Yii::$app->menu->findAll(['depth' => 1, 'container' => 'footer']));
            $i = 0;
          ?>

            <?php foreach (Yii::$app->menu->findAll(['depth' => 1, 'container' => 'footer']) as $item) : /* @var $item \luya\cms\menu\Item */ ?>
              <li class="list-inline-item">
                <a href="<?= $item->link; ?>" target="_blank" title="<?= $item->title ?>"><?= $item->title; ?></a>
              </li>
          <?php endforeach;
          } ?>
          <li class="list-inline-item"><a href="<?= \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/personalize-cookie?urlRedirect=' . urlencode(\Yii::$app->request->absoluteUrl) ?>" title="<?= \Yii::t('app', 'Impostazione Cookie') ?>"><?= \Yii::t('app', 'Impostazione Cookie') ?></a>
          </li>
          <li class="list-inline-item"><a href="<?= \Yii::$app->params['platform']['frontendUrl'] . '/tickets/contacts' ?>" target="_blank" title="<?= \Yii::t('app', 'Assistenza tecnica') ?>"><?= \Yii::t('app', 'Assistenza tecnica') ?></a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php ?>