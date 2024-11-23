<?php
use open20\amos\admin\AmosAdmin;
use open20\amos\core\helpers\Html;
use yii\bootstrap\Modal;

?>
<?php
if (!Yii::$app->getRequest()->getCookies()->has('dl_semplification_modal_cookie')) {
    $js = <<<JS
    $('#modal-dl-semplification').modal('show');
    $('#modal-dl-semplification-dont-show-again-link').on('click', function(event) {
        event.preventDefault();
        $.ajax({
            url: '/amosadmin/security/set-dl-semplification-modal-cookie',
            type: 'post',
            success: function (data) {
                $('#modal-dl-semplification').modal('hide');
            }
        });
    });
JS;
    $this->registerJs($js);
}
$btnLabel = AmosAdmin::t('amosadmin', '#dl_semplification_modal_btn_label');
Modal::begin([
    'id' => 'modal-dl-semplification',
    'header' => '<h4 class="nom modal-title">'.AmosAdmin::t('amosadmin', '#dl_semplification_modal_header').'</h4>',
]);
echo Html::tag('div',
    Html::tag('p', AmosAdmin::t('amosadmin', '#dl_semplification_modal_text'))
);
$dontshow = Html::a(
    AmosAdmin::t('amosadmin', '#dl_semplification_modal_dont_show_again'), null,
    [
        'id' => 'modal-dl-semplification-dont-show-again-link',
        'title' => AmosAdmin::t('amosadmin', '#dl_semplification_modal_dont_show_again'),
        'target' => '_blank',
        'class' => 'btn btn-link',
    ]);
echo Html::tag('div', $dontshow.'  '.Html::a($btnLabel, null, ['class' => 'btn btn-primary', 'data-dismiss' => 'modal']), ['class' => 'text-right m-15-0']);

Modal::end();
?>