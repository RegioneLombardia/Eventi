<?php

/**
 * @var $model \open20\amos\favorites\models\Favorite
 */

use open20\amos\favorites\AmosFavorites;
use yii\helpers\Html;
$event = \open20\amos\events\models\Event::findOne($model->content_id);
$eventDate = '';
$eventLocation = '';
if($event){
    $eventDate = $event->getMatchedAttribute('event_date');
    $eventLocation = $event->getMatchedAttribute('location_address');
}
?>
<div class="item-list-preferiti">
    <div class="row">
        <div class="col-sm-11 col-xs-10">
            <span class="title">
                <?php
                $type = $model->getFavoriteType(false);
                if (!empty($type)) { ?>
                    <span class="badge badge-secondary"><?= $type ?></span>
                <?php } ?>
                <a class="" href="<?= $model->url ?>" title="<?= AmosFavorites::t('amosfavorites', 'Visualizza') . ' ' . $model->title . ' ' . AmosFavorites::t('amosfavorites', '[Apre in nuova finestra]') ?>" target="_blank"><?= $model->title ?></a>
            </span>
            <div class="info-evento">
                <small><?= $eventDate ?> - <?= $eventLocation?></small>
            </div>
        </div>
        <div class="col-sm-1 col-xs-2 text-right">
            <?php
            $icon = '<span class="it-close sr-only">' . AmosFavorites::t('amosfavorites', "Rimuovi dai segnalibri") . '</span><span class="text-danger mdi mdi-close-circle-outline"></span>' ?>
            <?= Html::button($icon, [
                'data-toggle' => 'modal',
                'data-target' => '#favoriteModal-' . $model->id,
                'class' => 'btn btn-icon p-0',
                'title' => AmosFavorites::t('amosfavorites', "Rimuovi dai segnalibri")
            ]) ?>

            <!-- Modal -->
            <div class="modal fade" id="favoriteModal-<?= $model->id ?>" tabindex="-1" role="dialog" aria-labelledby="favoriteModalLabel-<?= $model->id ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="favoriteModalLabel-<?= $model->id ?>">
                                <?= AmosFavorites::t('amosfavorites', "Rimuovi segnalibro") ?>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-left">
                            <?= AmosFavorites::t('amosfavorites', 'Sei sicuro di voler rimuovere "{x}" dai segnalibri?', [
                                'x' => $model->title
                            ]); ?>
                        </div>
                        <div class="modal-footer mt-3">
                            <?= Html::button(AmosFavorites::t('amosfavorites', "Annulla"), [
                                'data-dismiss' => 'modal',
                                'class' => 'btn btn-sm btn-secondary',
                                'title' => AmosFavorites::t('amosfavorites', "Annulla")
                            ]); ?>
                            <?= Html::button(AmosFavorites::t('amosfavorites', "Rimuovi"), [
                                'id' => 'delete-favorite-confirm-' . $model->id,
                                'class' => 'delete-favorite-confirm btn btn-sm btn-primary',
                                'data-key' => $model->id,
                                'title' => AmosFavorites::t('amosfavorites', "Rimuovi dai segnalibri")
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>