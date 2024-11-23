<?php

use kartik\datecontrol\DateControl;
use open20\amos\core\forms\ActiveForm;
use luya\admin\models\Lang;
use yii\db\Query;
USE yii\helpers\Html;
use open20\amos\events\AmosEvents;
use yii\widgets\ListView;
use yii\helpers\Url;


?>

<?php $js = <<<JS
var url_string = window.location.href;
var url = new URL(url_string);
var a = url.searchParams.get("event-page");
if(a != null && a.length > 0){
           $([document.documentElement, document.body]).animate({
        scrollTop: $("#coming-next").offset().top
    }, 1000);
}
JS;
$this->registerJs($js);

$moduleEvent = \Yii::$app->getModule('events');
$showFavorites = true;
if($moduleEvent){
    $showFavorites = $moduleEvent->enableFavorites;
}
$tagName = '';
if (!empty($tagPreference)) {
    $tag = \open20\amos\tag\models\Tag::findOne($tagPreference);
    if ($tag) {
        $tagName = $tag->nome . ' ';
    }
}

if (!empty($_GET['day'])) {
    $labelFilter = \open20\amos\events\utility\EventsUtility::getLabelSearchDate($_GET['day']) . ' ';
} else {
    $labelFilter = \open20\amos\events\utility\EventsUtility::getLabelSearchDate('highlights');
}

?>

<div id="event-coming-next-container" class="container-elenco-eventi">

    <div class="dynamic-title">
        <?= $tagName . $labelFilter ?>
    </div>
    <?php
    echo \yii\helpers\Html::hiddenInput('filterTagsEnabled', implode(',', $filtersEnabled), ['id' => 'filter-tags-enabled-id']);
    $dataProvider->pagination->pageSize = 10;
    $dataProvider->pagination->route = '/esplora-eventi';
    $dataProvider->pagination->pageParam = 'event-page';
    $dataProvider->pagination->page = \Yii::$app->request->get('event-page') - 1;

    $templates = \open20\amos\events\utility\EventsUtility::getCmsTemplates(Lang::getDefault()['id']);


    if($dataProvider->count == 0){ ?>
        <p><?= AmosEvents::t('amosevents', "Nessun evento trovato. Modifica la ricerca e riprova.")?></p>
    <?php } else {
        echo ListView::widget([
            'summary' => false,
            'dataProvider' => $dataProvider,
            'itemView' => '_item',
            'options' => [
                'class' => 'lista-eventi',
            ],
            'viewParams' => [
                'templates' => $templates,
                'showFavorites' => $showFavorites
            ]
        ]);
    }
    ?>
</div>


