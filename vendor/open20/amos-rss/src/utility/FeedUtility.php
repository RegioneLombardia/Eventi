<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos\rss\utility
 * @category   CategoryName
 */

namespace amos\rss\utility;

use amos\rss\Module;
use open20\amos\core\record\ContentModel;
use open20\amos\core\utilities\StringUtils;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class FeedUtility
 * @package amos\rss\utility
 */
class FeedUtility extends \yii\base\BaseObject
{
    private $module = null;

    /**
     * @return string
     */
    public function getListUrl()
    {
        return '/rss/rss/rss';
    }

    /**
     * @return string
     */
    public function getReadUrl()
    {
        return '/rss/rss/rss-feed';
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = array())
    {
        $this->module = Module::instance();
        parent::__construct($config);
    }

    /**
     * @return \Zend\Feed\Writer\Feed
     */
    public function createFeedWriter()
    {
        $feed = Yii::$app->rss->writer();

        $feed->setTitle($this->module->title);
        $feed->setLink(Yii::$app->params['platform']['backendUrl']);
        $feed->setFeedLink($this->module->url, 'rss');
        $feed->setDescription($this->module->description);
        $feed->setGenerator($this->module->url);
        $feed->setDateModified(time());

        $appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
        $logo = $appLink . Yii::$app->params['logo'];

        $urls = ['uri' => $logo,
            'title' => Yii::$app->name,
            'link' => $appLink];
        $feed->setImage($urls);

        return $feed;
    }

    /**
     * @param \Zend\Feed\Writer\Feed $feed
     * @param ActiveRecord|ContentModel $entity
     * @param integer $key
     */
    public function addEntryInFeed($feed, $entity, $key)
    {
        $entry = $feed->createEntry();
        $entry->setTitle($entity->getTitle());
        if(method_exists($entity, 'getContentAbsoluteUrl')){
           $url = $entity->getContentAbsoluteUrl();
        }else{
            $url = Yii::$app->urlManager->createAbsoluteUrl([$this->getReadUrl(), 'id' => $entity->id, 'type' => $key]);
        }

        $entry->setLink($url);
        $entry->setCommentLink($url);
        $entry->setDateModified(new \DateTime($entity->updated_at));
        $entry->setDateCreated(new \DateTime($entity->created_at));
        $description = $entity->getDescription(null);

        $entry->setContent(
            (StringUtils::isEmpty($description) ? '--' : $description)
        );
        $entry->setDescription((StringUtils::isEmpty($description) ? '--' : $description));
        $image = $entity->getModelImage();

        if (!is_null($image)) {
            $entry->setEnclosure(
                [
                    'uri' => $image->getWebUrl('original', true),
                    'type' => $image->mime,
                    'length' => $image->size
                ]
            );
        }

        $entry->addCategory(['term' => $entity::className(),]);

        $feed->addEntry($entry);
    }
}
