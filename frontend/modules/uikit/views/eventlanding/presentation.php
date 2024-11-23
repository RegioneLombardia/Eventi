<?php
/**
 * @var $model \open20\amos\events\models\Event
 */
?>
<div id="layoutsection-f1f" class="text-intro uk-section- uk-visible@xl uk-section">
    <div class="uk-container">
        <div class="uk-container uk-grid-margin">
            <div class="content-program uk-grid uk-grid-stack" uk-grid="">
                <div id="993" class="uk-width-1-1@m uk-first-column">
                    <h2 id="headline-f87">
                        <?= $model->getMatchedAttribute('title_presentation') ?>
                    </h2>
                    <?= $model->getMatchedAttribute('presentation')?>
                </div>
            </div>
        </div>
    </div>
</div>
