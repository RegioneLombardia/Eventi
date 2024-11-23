<?php

$url = \Yii::$app->params['platform']['backendUrl'];
if (!empty($fromBackend)) {
  $currentUrl = \yii\helpers\Url::current();
}
?>
<div id="megamenu-slim-eventi">
  <div class="content-top-megamenu">
    <div class="left-megamenu">
      <amp-accordion class="accordion-menu">
        <section>

          <h4 id="hamburger">
            <div class="content-left">
              <span class="material-icons">
                menu
              </span>
              <span class="title-page-footer">
                <!-- < ?php if (empty($fromBackend)) { ?>
                  <span class="uk-margin-small-right"><?= $this->title ?> </span>
                  < ?php if (!empty($fromBackend) || Yii::$app->controller->id . '/' . Yii::$app->controller->action->id == 'amp/palazzo-lombardia') { ?>
                    <a href="#ModalInfoPalazzo" data-toggle="modal" data-target="#ModalInfoPalazzo"><span class="material-icons">information</span></a>
                  < ?php } ?>
                  < ?php if (!empty($fromBackend) || Yii::$app->controller->id . '/' . Yii::$app->controller->action->id == 'amp/grattacielo-pirelli') { ?>
                    <a href="#ModalInfoGp" data-toggle="modal" data-target="#ModalInfoGp"><span class="mdi mdi-information"></span></a>
                  < ?php } ?>
                < ?php } else { ?> -->
                  <span class="uk-margin-small-right"><?= $this->title ?> </span>

                <!-- < ?php  } ?> -->
                <!-- <button on="tap:quote-lb">See Quote</button>
                <amp-lightbox id="quote-lb" layout="nodisplay">
                  <blockquote>
                    "Don't talk to me about JavaScript fatigue" - Horse JS
                  </blockquote>
                  <button on="tap:quote-lb.close">Nice!</button>
                </amp-lightbox> -->
              </span>
            </div>

          </h4>
          <div class="content-accordion">

            <div class="content-bottom-megamenu-slim collapse in" id="sub-megamenu">
              <div class="menu-bottom">

                <div class="link-menu">
                  <?php if (!empty($fromBackend) || Yii::$app->controller->id . '/' . Yii::$app->controller->action->id !== 'amp/palazzo-lombardia') { ?>
                    <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/palazzo-lombardia/amp" ?>" title="<?= \Yii::t('app', "Palazzo Lombardia") ?>" class="current-page"><?= \Yii::t('app', "Palazzo Lombardia") ?></a>
                  <?php } ?>
                  <?php if (!empty($fromBackend) || Yii::$app->controller->id . '/' . Yii::$app->controller->action->id !== 'amp/grattacielo-pirelli') { ?>
                    <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/grattacielo-pirelli/amp" ?>" title="<?= \Yii::t('app', "Grattacielo Pirelli") ?>" class="current-page"><?= \Yii::t('app', "Grattacielo Pirelli") ?></a>
                  <?php } ?>
                  <?php if (!empty($fromBackend) || Yii::$app->controller->id . '/' . Yii::$app->controller->action->id !== 'amp/esplora-eventi') { ?>
                    <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/esplora-eventi/amp" ?>" title="<?= \Yii::t('app', "Tutti gli eventi") ?>" class="current-page"><?= \Yii::t('app', "Tutti gli eventi") ?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="footer-megamenu">
                <div class="copyleft">
                  <p>
                    © <?= \Yii::t('app', 'Copyleft Regione Lombardia tutti i diritti riservati - 80050050154 - Piazza Città di Lombardia 1 - 20124 Milano'); ?>
                  </p>
                </div>
                <amp-accordion class="link-credits">
                  <section>
                    <h4 class="link"><span>Informazioni legali e assistenza</span></h4>
                    <div id="linkCredits">
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
                  </section>
                </amp-accordion>

              </div>

            </div>
          </div>
        </section>
      </amp-accordion>
    </div>
    <div class="right-megamenu">

      <a class="logo-regione" href="https://www.regione.lombardia.it/" target="_blank" title="<?= \Yii::t('app', 'Regione Lombardia'); ?>">
        <amp-img src="/amp-resources/img/logo_regione_lombardia-bianco.svg" alt="<?= \Yii::t('app', 'Logo Regione Lombardia'); ?>" class="logo-bianco img-responsive" width="138" height="40" noloading>
      </a>
    </div>
  </div>
</div>