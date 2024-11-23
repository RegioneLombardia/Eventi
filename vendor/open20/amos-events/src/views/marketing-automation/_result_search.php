<?php

use open20\amos\events\AmosEvents;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\models\search\UserEventSearch;

$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
?>

<div id="form-results" class="mb-5 p-4 neutral-1-bg-a1">
    <div class="row">
        <div class="col-md-6">
            <div class="my-4 d-flex">
                <div class="mr-2">
                    <svg class="icon">
                        <use xlink:href="<?= $spriteAsset->baseUrl ?>/material-sprite.svg#ic_mail"></use>
                    </svg>
                </div>

                <h5 class="font-weight-bold ">
                    <?= AmosEvents::t('amosevents', 'Risultati della ricerca') ?>
                </h5>
            </div>


        </div>
        <div id="save-inputs" class="col-md-6">
            <p class="text-danger  mt-4">
                <strong id><?= AmosEvents::t('amosevents', "Sono stati trovati: <span id='count-found'>{n}</span> UTENTI", ['n' => $count]) ?></strong></p>
            <div>
                <?php if (!empty($saveAjax)) { ?>
                    <div class="col-sm-6 mt-2">
                        <!--                        --><?php //\yii\helpers\Html::a(AmosEvents::t('amosevents', "Save"), '', ['class' => 'btn btn-primary pull-left', 'id' => 'save-list']) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>