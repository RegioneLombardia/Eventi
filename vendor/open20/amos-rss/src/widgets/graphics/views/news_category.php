<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news\widgets\graphics\views
 * @category   CategoryName
 */

use amos\rss\assets\ModuleRssAsset;
use open20\amos\core\icons\AmosIcons;
use amos\rss\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Inflector;

ModuleRssAsset::register($this);

/**
 * @var View $this
 * @var string $toRefreshSectionId
 */
$limit = Module::getInstance()->limitRss;
if (sizeof($feedsfilter) == 1) {
    $limit = $limit * 2;
}
?>

<div class="grid-item grid-item--fullwidth">
    <div class="box-widget news-category-rss">
        <div class="box-widget-toolbar">
            <h1 class="box-widget-title col-xs-10 nop"><?= (empty($title) ? Module::t('amosrss', '#ultime_news') : $title ) ?></h1>
        </div>
        <section class="clearfixplus">
            <h2 class="sr-only"><?= (empty($title) ? Module::t('amosrss', '#ultime_news') : $title) ?></h2>
            <div class="rss-content col-xs-12">
                <?php
                foreach ($feedsfilter as $cat => $link):
                    ?>
                    <div class="rss-infos <?= (sizeof($feedsfilter) == 1) ? '' : 'rss-two-infos' ?> col-xs-12 nop">
                        <div class="rss-head col-xs-12">
                            <h2><?= str_replace('-', ' ', strtoupper($cat)) ?></h2>
                            <?php
                            if (isset($link) && !empty($link)):
                                ?>
                                <?=
                                Html::a(
                                    Module::t('amosrss', '#readmore') . AmosIcons::show('play'), Url::to($link[0], true), ['target' => '_blank', 'title' => Module::t('amosrss', '#readmore')]
                                );
                                ?>
                            <?php
                            endif;
                            ?>
                        </div>
                        <div class="rss-body col-xs-12">
                            <?php
                            foreach ($feeds as $feed):
                                foreach ($feed as $count => $element):
                                    if ($count >= $limit) {
                                        break;
                                    }
                                    $categories = $element->getCategories();
                                    if(is_null($category)){                                   
                                        if ($categories[0]['label'] != $cat) {
                                            continue;
                                        }
                                    }
                                    ?>

                                    <div class="single-item col-xs-12" role="option">
                                        <p class="date">
                                            <?= Yii::$app->getFormatter()->asDate($element->getDateCreated(), 'long'); ?>
                                        </p>
                                        <?php
                                        $title = '';
                                        if (strlen($element->getTitle()) > 55) {
                                            $stringCut = substr($element->getTitle(), 0, 55);
                                            if (strrpos($stringCut, ' ')) {
                                                $title = substr($stringCut, 0, strrpos($stringCut, ' ')) . '...';
                                            } else {
                                                $title = $stringCut . '... ';
                                            }
                                        } else {
                                            $title = $element->getTitle();
                                        }
                                        ?>
                                        <?=
                                        Html::a(
                                            $title, Url::to($element->getLink(), true), ['target' => '_blank', 'title' => $title, 'class' => 'title']
                                        );
                                        ?>
                                        <p class="text">
                                            <?php
                                            if (strlen($element->getContent()) > 200) {
                                                $stringCut = substr(strip_tags($element->getContent()), 0, 150);
                                                echo substr($stringCut, 0, strrpos($stringCut, ' ')) . '... ';
                                            } else {
                                                echo strip_tags($element->getContent());
                                            }
                                            ?>
                                        </p>
                                    </div>

                                <?php
                                endforeach;
                            endforeach;
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </section>
    </div>
</div>