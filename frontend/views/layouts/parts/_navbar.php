<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    landing
 * @category   CategoryName
 *
 *
 * @var $this \yii\web\View
 * @var $assetBundle \yii\web\AssetBundle
 *
 */

use app\assets\ResourcesAsset;
use app\components\CmsHelper;
use luya\cms\menu\Item;
use luya\cms\widgets\LangSwitcher;
use yii\helpers\Html;

$resourceAsset = ResourcesAsset::register($this);
$redir = urlencode(\Yii::$app->request->absoluteUrl);

$js = <<<JS
    var sectionprogram = $('.content-program');
if(sectionprogram === undefined || sectionprogram.length <= 0){
    $('#btn-hamburger').hide();
}
JS;
$this->registerJs($js);
$module = \Yii::$app->getModule('events');
echo $this->render('_megamenu-slim', ['assetBundle' => $assetBundle, 'fromBackend' => $fromBackend]);
?>

    <div uk-sticky="animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky; top: 200"
         class="header">

        <nav class="uk-navbar-container navbar-new-eventi uk-navbar-transparent">
            <div class="uk-container uk-container-expand">
                <div uk-navbar>

                    <div class="uk-navbar-right">
                        <ul class="uk-navbar-nav">
                            <?php if (!\Yii::$app->user->isGuest) {
                                $profile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
                                $nomeCognome = $profile->nomeCognome;
                                ?>
                                <li>
                                    <a href="#" type="button" title="Apri sottomenu utente"><span
                                                class="mdi mdi-account-circle uk-text-lead" aria-hidden></span><span
                                                class="uk-visible@s"><?= $nomeCognome ?></span><i
                                                uk-icon="icon: chevron-down"></i></a>


                                    <div uk-dropdown="mode: click; pos: bottom-right">
                                        <ul class="uk-nav uk-navbar-dropdown-nav">
                                            <li>
                                                <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/admin/user-profile/update?id=" . $profile->id ?>"
                                                   title="<?= \Yii::t('app', "Il mio profilo") ?><"><?= \Yii::t('app', "Il mio profilo") ?></a>
                                            </li>
                                            <?php if (\Yii::$app->user->can('EVENT_DG_OPERATOR') || \Yii::$app->user->can('SUPER_USER_EVENT')) { ?>
                                                <li>
                                                    <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/dashboard" ?>"
                                                       title="<?= \Yii::t('app', "Accedi all'area riservata") ?><"><?= \Yii::t('app', "Accedi all'area riservata") ?></a>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/events/event-dashboard/my-events-home" ?>"
                                                   title="<?= \Yii::t('app', "I miei eventi") ?><"><?= \Yii::t('app', "I miei eventi") ?></a>
                                            </li>

                                            <?php if ($module->enableFavorites) { ?>
                                                <li>
                                                    <a href="<?= \Yii::$app->params['platform']['frontendUrl'] . "/events/favourites/index" ?>"
                                                       title="<?= \Yii::t('app', "I miei Preferiti") ?><"><?= \Yii::t('app', "I miei preferiti") ?></a>
                                                </li>
                                            <?php } ?>


                                            <li>
                                                <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/site/logout-frontend?redir=" . $redir ?>"
                                                   title="<?= \Yii::t('app', "Logout") ?>"><?= \Yii::t('app', "Logout") ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>


                            <?php } else {
                                ?>
                                <li>
                                    <!-- <a href="javascript:void(0)" class="uk-icon-button icon-apps" title="Menu di navigazione"><span class="mdi mdi-apps"></span></a> -->
                                    <a data-method="post"
                                       href="<?= \Yii::$app->params['platform']['backendUrl'] . "/admin/security/login-frontend?redirect_post_reg=" . $redir ?>"><span
                                                class="mdi mdi-account-circle uk-text-lead" aria-hidden></span><span
                                                class="uk-visible@s"> <?= \Yii::t('app', "Accedi / Registrati") ?></span></a>

                                </li>
                            <?php } ?>
                        </ul>


                    </div>
                </div>
            </div>
        </nav>
    </div>
<?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() == 'palazzo-lombardia') { ?>
    <!-- Modal Palazzo Lombardia -->
    <div class="modal fade" id="ModalInfoPalazzo" tabindex="-1" role="dialog" aria-labelledby="ModalInfoPalazzo">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <p class="modal-title h3">Palazzo Lombardia</p>
                </div>
                <div class="modal-body">
                    <p>Palazzo Lombardia , sede della Giunta regionale, è stato progettato dallo studio Pei Cobb
                        Freed&Partners di New York, in collaborazione con le milanesi Caputo Partnership e Sistema
                        Duemila,
                        ed è costituito da edifici curvilinei di 7 e 9 piani e dalla Torre di 39.

                        Ai piedi dell’edificio, una splendida piazza, denominata "Piazza Città di Lombardia", ospita
                        diverse
                        attività, fra cui un ufficio postale, una scuola materna, un auditorium, diversi ristoranti e
                        caffetterie. L’area, con i suoi 3800 mq, è la piazza coperta più grande d'Europa.

                        Costruito in tempi record, dal 2007 al 2010, Palazzo Lombardia è un simbolo di architettura
                        urbana
                        bella, innovativa, sostenibile. Il Palazzo è stato nominato nel 2012 il miglior grattacielo
                        d’Europa
                        dal prestigioso Council of Tall Buildings and Urban Habitat (Ctbuh) di Chicago, che ne ha
                        esaltato
                        il design, la sostenibilità e l'innovazione. È stato il primo edificio italiano a ricevere
                        questo
                        premio.

                        Il Palazzo ospita anche un “Museo verticale”, una selezione di opere d’arte contemporanea che, a
                        partire dalle installazioni permanenti e temporanee ai piani inferiori, si snoda lungo tutta
                        l’altezza dell’edificio e incontra la sua vetta proprio al Belvedere del 39° piano.

                        Palazzo Lombardia è un magnifico esempio di come un edificio amministrativo possa accogliere i
                        suoi
                        cittadini e rendere periodicamente i propri spazi fruibili a tutti. Gli spazi di Regione
                        Lombardia
                        ospitano ogni anno eventi pubblici o privati e offrono location esclusive per iniziative di alto
                        profilo. Scopri in ogni sezione come fare a organizzare un evento a Palazzo Lombardia.</p>
                </div>

            </div>
        </div>
    </div>
<?php } ?>
<?php if (!empty($fromBackend) || Yii::$app->menu->current->getAlias() == 'grattacielo-pirelli') { ?>
    <!-- Modal Grattacielo Pirelli-->
    <div class="modal fade" id="ModalInfoGp" tabindex="-1" role="dialog" aria-labelledby="ModalInfoGp">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <p class="modal-title h3">Grattacielo Pirelli</p>
                </div>
                <div class="modal-body">
                    <p>Il Grattacielo Pirelli, chiamato comunemente Pirellone, è uno dei simboli di Milano. Sede del
                        Consiglio regionale della Lombardia, con i suoi 127 metri è stato per 50 anni l'edificio più
                        alto
                        della città, superato solo nel 2010 da Palazzo Lombardia.
                        Costruito nel 1960 dall’architetto Gio Ponti per ospitare gli uffici dell'azienda di pneumatici
                        Pirelli, nel 1978 fu acquistato dalla Regione per farne la propria sede principale.</p>
                    <p> L'intera struttura portante è in calcestruzzo armato, una scelta insolita per edifici di tale
                        altezza -ben 31 piani- che ne sottolinea l'unicità. Il grattacielo ospita varie sale
                        istituzionali e
                        culmina all'ultimo piano con il magnifico belvedere dedicato a Enzo Jannacci, che offre una
                        vista
                        unica sulla città.
                    </p>
                </div>

            </div>
        </div>
    </div>
<?php } ?>