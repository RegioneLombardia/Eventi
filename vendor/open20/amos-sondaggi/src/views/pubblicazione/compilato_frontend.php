<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\views\pubblicazione
 * @category   CategoryName
 */
use open20\amos\core\icons\AmosIcons;
use open20\amos\sondaggi\AmosSondaggi;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\sondaggi\models\search\SondaggiSearch $searchModel
 * @var \yii\db\ActiveQuery $pubblicazioni
 */
$this->title                   = AmosSondaggi::t('amossondaggi', 'Sondaggio terminato');
$this->params['breadcrumbs'][] = $this->title;
if (!empty($forzato)) {
    \Yii::$app->language = $forzato;
}
?>
<div class="sondaggi-index text-center sondaggi-success">
    <?php
    if (!empty($sondaggio->titolo_fine_sondaggio_front) && strlen(trim($sondaggio->titolo_fine_sondaggio_front))) {
        ?>
        <?=
        AmosIcons::show('check-circle', [
            'class' => 'am-4 success m-t-15'
        ])
        ?>
        <h2 class="p-t-5 nom-b"><?= $sondaggio->titolo_fine_sondaggio_front ?></h2>
        <?php
    } else {
        ?>
        <?=
        AmosIcons::show('check-circle', [
            'class' => 'am-4 success m-t-15'
        ])
        ?>
        <h2 class="p-t-5 nom-b"><?= AmosSondaggi::tHtml('amossondaggi', 'Grazie per aver compilato il questionario.')
        ?></h2>
        <?php } ?>

    <?php
    if (!empty($sondaggio->testo_fine_sondaggio_front) && strlen(trim($sondaggio->testo_fine_sondaggio_front))) {
        if ($pubblicazioni->one()->text_end_html == 1) {
            ?>
            <?= $sondaggio->testo_fine_sondaggio_front ?>
            <?php
        } else {
            ?>
            <h3><?= \Yii::$app->formatter->asNtext($sondaggio->testo_fine_sondaggio_front) ?></h3>
            <?php
        }
    } else {
        ?>
        <h3><?=
            AmosSondaggi::tHtml('amossondaggi', 'Ti abbiamo inviato un’email di riepilogo con tutte le informazioni.')
            ?></h3>
    <?php } ?>
    <?=
    Html::a(AmosSondaggi::t('amossondaggi', 'Chiudi'), Url::previous(),
        [
        'class' => 'btn btn-secondary undo-edit'
    ]);
    ?>
</div>
