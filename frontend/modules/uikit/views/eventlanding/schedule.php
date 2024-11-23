<?php
/**
 * @var $model \open20\amos\events\models\Event
 */
?>
<div id="layoutsection-1af" class="section-programma uk-section-default uk-visible@xl uk-section">
    <div class="uk-container">
        <div class="uk-container uk-grid-margin">
            <div class="content-programma text-programma uk-grid uk-grid-stack" uk-grid="">
                <div id="ca5" class="uk-width-1-1@m uk-first-column">
                    <h2 id="headline-ebd"><?= $model->getMatchedAttribute('title_agenda')?></h2>
                    <div id="text-298" class="testo-programma uk-margin">
                        <?= $model->getMatchedAttribute('program') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
