<?php

/**
 * TODO rendere automatico con match tra icone e tipologia evento
 */

use open20\amos\community\AmosCommunity;
use open20\amos\core\icons\AmosIcons;

?>
<div class="community-legenda callout callout-info m-b-30">
    <div class="callout-title">
        <span><?= AmosCommunity::t('amoscommunity', 'Legenda') ?></span>
    </div>
    <div class="flexbox flexbox-row flexbox-wrap">
        <div class="community-status community-open">
            <p><?= AmosIcons::show('lock-open') . ' ' . AmosCommunity::t('amoscommunity', 'Community aperta') ?></p>
        </div>
        <div class="community-status community-private">
            <p><?= AmosIcons::show('lock-outline') . ' ' . AmosCommunity::t('amoscommunity', 'Community riservata ai partecipanti') ?></p>
        </div>
        <div class="community-status community-closed">
            <p><?= AmosIcons::show('eye-off') . ' ' . AmosCommunity::t('amoscommunity', 'Community ristretta ai partecipanti') ?></p>
        </div>
    </div>
</div>