<?php

namespace open20\amos\events\bootstrap;


use amos\sitemanagement\models\SiteManagementSliderElem;
use open20\amos\documenti\models\Documenti;
use open20\amos\events\models\EventLanding;
use open20\amos\events\models\EventLandingDocuments;
use open20\amos\events\models\EventLandingNews;
use open20\amos\events\utility\EventsUtility;
use open20\amos\mobile\bridge\modules\v1\utility\EventUtility;
use open20\amos\news\models\News;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\rest\Controller;

class AfterInsertDocuments extends Event implements BootstrapInterface
{

    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        Event::on(Documenti::className(), Documenti::EVENT_AFTER_INSERT, [$this, 'connectDocsToEvent']);
        Event::on(Documenti::className(), Documenti::EVENT_AFTER_UPDATE, [$this, 'deleteDocsToEvent']);

        Event::on(News::className(), News::EVENT_AFTER_INSERT, [$this, 'connectNewsToEvent']);
        Event::on(News::className(), News::EVENT_AFTER_UPDATE, [$this, 'deleteNewsToEvent']);

        Event::on(SiteManagementSliderElem::className(), SiteManagementSliderElem::EVENT_AFTER_INSERT, [$this, 'afterUpdateSliderElem']);
        Event::on(SiteManagementSliderElem::className(), SiteManagementSliderElem::EVENT_AFTER_UPDATE, [$this, 'afterUpdateSliderElem']);

        Event::on(\open20\amos\favorites\models\Favorite::className(), \open20\amos\favorites\models\Favorite::EVENT_AFTER_INSERT, [$this, 'afterUpdateFavorite']);
        Event::on(\open20\amos\favorites\models\Favorite::className(), \open20\amos\favorites\models\Favorite::EVENT_AFTER_UPDATE, [$this, 'afterUpdateFavorite']);



    }

    /**
     * @return void
     */
    public function afterUpdateFavorite(){
        //set cookie for cache page variations
        EventsUtility::setCookieForCache();
    }

    /**
     * @param $event
     */
    public function connectDocsToEvent($event)
    {
        if (!(\Yii::$app->controller instanceof Controller)) {
            $document = $event->sender;
            if (!empty($_GET['urlRedirect'])) {
                $url = $_GET['urlRedirect'];
                if (strpos($_GET['urlRedirect'], "/events/event-dashboard/landing-manage-contents") >= 0) {
                    $urlParts = parse_url($url);
                    parse_str($urlParts['query'], $query);
                    if (!empty($query['id'])) {
                        $modelEvent = \open20\amos\events\models\Event::findOne($query['id']);
                        if ($modelEvent) {
                            /** @var  $landing EventLanding */
                            $landing = $modelEvent->eventLanding;
                            if ($landing) {
                                $docMm = new EventLandingDocuments();
                                $docMm->event_landing_id = $landing->id;
                                $docMm->documenti_id = $document->id;
                                $docMm->save(false);
                                EventsUtility::updateLuyaPageTimestampForCache($landing);
                                EventsUtility::flushCache();
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * @param $event
     */
    public function connectNewsToEvent($event)
    {
        if (!(\Yii::$app->controller instanceof Controller)) {
            $news = $event->sender;
            if (!empty($_GET['urlRedirect'])) {
                $url = $_GET['urlRedirect'];
                if (strpos($_GET['urlRedirect'], "/events/event-dashboard/landing-manage-contents") >= 0) {
                    $urlParts = parse_url($url);
                    parse_str($urlParts['query'], $query);
                    if (!empty($query['id'])) {
                        $modelEvent = \open20\amos\events\models\Event::findOne($query['id']);
                        if ($modelEvent) {
                            /** @var  $landing EventLanding */
                            $landing = $modelEvent->eventLanding;
                            if ($landing) {
                                $newsMm = new EventLandingNews();
                                $newsMm->event_landing_id = $landing->id;
                                $newsMm->news_id = $news->id;
                                $newsMm->save(false);
                                EventsUtility::updateLuyaPageTimestampForCache($landing);
                                EventsUtility::flushCache();
                            }
                        }
                    }
                }
            }
        }
    }

    public function deleteNewsToEvent($event)
    {
        $news = $event->sender;
        if ($news) {
            if (!empty($news->deleted_at)) {
                $landing = \open20\amos\events\models\EventLanding::find()
                    ->innerJoin('event_landing_news', 'event_landing_news.event_landing_id = event_landing.event_id')
                    ->andWhere(['news_id' => $news->id])->one();
                if ($landing) {
                    EventsUtility::updateLuyaPageTimestampForCache($landing);
                }
            }
        }
    }

    public function deleteDocsToEvent($event)
    {
        $document = $event->sender;
        if ($document) {
            if (!empty($document->deleted_at)) {
                $landing = \open20\amos\events\models\EventLanding::find()
                    ->innerJoin('event_landing_documents', 'event_landing_documents.event_landing_id = event_landing.event_id')
                    ->andWhere(['documenti_id' => $document->id])->one();
                if ($landing) {
                    EventsUtility::updateLuyaPageTimestampForCache($landing);
                }
            }
        }
    }

    /**
     * @param $event
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function afterUpdateSliderElem($event){
        $element = $event->sender;
        $landing = EventLanding::find()->andWhere([
            'OR',
            ['event_landing.image_slider_id' => $element->slider_id],
            ['event_landing.video_slider_id' => $element->slider_id],
            ['event_landing.instagram_video_slider_id' => $element->slider_id],
        ])->one();
        if($landing){
            EventsUtility::updateLuyaPageTimestampForCache($landing);
        }

    }



}