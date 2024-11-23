<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    landing
 * @category   CategoryName
 *
 * @var $this \yii\web\View
 * @var $assetBundle \yii\web\AssetBundle
 *
 */

?>

<a class="gototop"><span class="sr-only">go to top</span><span class="glyphicon glyphicon-chevron-up"></span></a>

<footer class="footer" style="display: none;">
   

    <div class="uk-container">
        <div class="container-fluid">
            <div class="row">

                <div class="menu-footer">
                    <ul>
                    <?php
                        $numItems = count(Yii::$app->menu->findAll(['depth' => 1, 'container' => 'footer']));
                        $i = 0;
                        ?>

                        <?php foreach (Yii::$app->menu->findAll(['depth' => 1, 'container' => 'footer']) as $item) : /* @var $item \luya\cms\menu\Item */ ?>                            
                            <li><a class="nav-link" href="<?= $item->link; ?>" target="_blank" title="<?= $item->title ?>"><?= $item->title; ?></a></li>

                        <?php endforeach; ?>
                        <li><a class="nav-link" href="<?= \Yii::$app->params['platform']['backendUrl'].'/admin/user-profile/personalize-cookie?urlRedirect='.urlencode(\Yii::$app->request->absoluteUrl) ?>" title="<?=\Yii::t('app', 'Impostazione Cookie')?>"><?=\Yii::t('app', 'Impostazione Cookie')?></a></li>
                        <li><a class="nav-link" href="<?= '/tickets/contacts' ?>" target="_blank" title="<?=\Yii::t('app', 'Assistenza tecnica')?>"><?=\Yii::t('app', 'Assistenza tecnica')?></a></li>
                    </ul>

                </div>
                <div class="copyleft">
                    <p> © <?= \Yii::t('app', 'Copyleft Regione Lombardia tutti i diritti riservati - 80050050154 - Piazza Città di Lombardia 1 - 20124 Milano
                '); ?>
                        

                    </p>

                </div>
            </div>

        </div>
    </div>
</footer>

<?php
//$socialModule = \Yii::$app->getModule('social');
//if (!empty($socialModule) && class_exists('\kartik\social\GoogleAnalytics')) :
//    if (YII_ENV_PROD && !empty($socialModule->googleAnalytics)) :
//        echo \kartik\social\GoogleAnalytics::widget([]);
//    endif;
//endif;
//?>