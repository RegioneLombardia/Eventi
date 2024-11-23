
<?= \open20\amos\core\views\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '@vendor/open20/amos-events/src/views/event-dashboard/_parts/webmeeting/_item_participant',
    'masonry' => false,
    'viewParams' => ['participants' => $participants, 'participants_user_ids' => $participants_user_ids, 'readOnly' => $readOnly,],

]) ?>
