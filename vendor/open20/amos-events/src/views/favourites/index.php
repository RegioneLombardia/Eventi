<?php

use open20\amos\favorites\AmosFavorites;
use yii\helpers\Html;

/**
 * @var $favorites
 */

$this->title = \open20\amos\events\AmosEvents::t('amosevents', "I miei preferiti");
?>
<div class="event-index container">
    <?php

    //    echo $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->session->get('previousUrl')]);
    //    echo $this->render('_order', ['model' => $model,'originAction' => Yii::$app->session->get('previousUrl')]);
    //$dataProvider->pagination->pageSize = 5;

    ?>
    <h1 class="mb-3"><?= $this->title ?></h1>

    <?php
    $js = <<<JS
    $('.delete-favorite-confirm').click(function(){
        var link = $(this);
        var id = $(this).attr('data-key');
        $('#favoriteModal-'+id).modal('hide');
        $.ajax({
                    url: '/favorites/favorite/select-unselect-favorite-url-ajax',
                    type: 'post',
                    data: {
                         favoriteId: id
                    },
                    success: function (data) {
                        if(data['action'] == 'unselected'){
                            var row = $(link).parents('.itemRowDashboardTab');
                            $(row).remove();
                        }
                    
                    }
                });
    });
JS;

    $this->registerJs($js);
    ?>

    <?php
    if ($dataProvider->totalCount > 0) {
            echo \open20\amos\core\views\ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_item_favourite',
            ]) ?>
    <?php } else { ?>
        <p class='no-favorites'><?= $noFavouriteLabel ?></p>
    <?php } ?>
</div>
