<?php



namespace app\modules\seo\frontend\behaviors;

use open20\amos\events\utility\EventsUtility;
use luya\web\View;
use Yii;
use yii\base\Behavior;
use yii\base\Event;


class LuyaSeoBehavior extends Behavior {

    private $isRendered = false;
    private $sender;
    public static $renderedSeo = false;

    public function events() {
        return [
            View::EVENT_BEFORE_RENDER => 'attachSeoMetadata',
        ];
    }

    /**
     * @param Event $event
     * @throws \yii\base\InvalidConfigException
     */
    public function attachSeoMetadata(Event $event) {
        if(!self::$renderedSeo) {
            $this->sender = $event->sender;
            $navId = Yii::$app->menu->current->navId;
            /** @var  $event \open20\amos\events\models\Event */
            $event = \open20\amos\events\models\Event::find()
                ->innerJoin('event_landing', 'event_landing.event_id = event.id')
                ->andWhere(['event_landing.luya_page_id' => $navId])->one();
            if (!empty($event)) {
                $ogTitle = $event->title;
                $ogDescription = $event->description;
                $ogType = 'website';
                $ogImageUrl = $event->getMainImageEvent('big');
                $ogImageUrl = str_replace('/it/', '', $ogImageUrl);
                $ogUrl = \Yii::$app->request->absoluteUrl;
                if ($event->getMetaTitle()) {
                    $ogTitle = $event->getMetaTitle();
                }
                if ($event->getOgDescription()) {
                    $ogDescription = $event->getOgDescription();
                }
                if ($event->eventLanding->ogImageFile) {
                    $ogImageUrl = $event->eventLanding->ogImageFile->getWebUrl(EventsUtility::getImageCrops('big'));
                    if(strpos($ogImageUrl,'https:') === false){
                        $ogImageUrl = \Yii::$app->urlManager->createAbsoluteUrl($ogImageUrl);
                        $ogImageUrl = str_replace('/it', '', $ogImageUrl);
                    }
                }
            } else {
                $ogTitle = Yii::$app->menu->current->getProperty('ogTitle');
                $ogDescription = Yii::$app->menu->current->getProperty('ogDescription');
                $ogType = Yii::$app->menu->current->getProperty('ogType');
                $ogImageUrl = Yii::$app->menu->current->getProperty('ogImageUrl');
                $metaRobots = self::stringifyArrayProperty(Yii::$app->menu->current->getProperty('metaRobots'));
                $metaGooglebot = self::stringifyArrayProperty(Yii::$app->menu->current->getProperty('metaGooglebot'));
            }

            $this->sender->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image'], 'twitterCard');

            if ($ogUrl) {
                $this->sender->registerMetaTag(['property' => 'og:url', 'content' => $ogUrl], 'ogUrl');
                $this->sender->registerMetaTag(['name' => 'url', 'property' => 'og:url', 'content' => $ogUrl], 'ogUrlLinkedin');
            }
            if ($ogTitle) {
                $this->sender->registerMetaTag(['name' => 'og:title', 'content' => $ogTitle], 'fbTitle');
                $this->sender->registerMetaTag(['property' => 'og:title', 'content' => $ogTitle], 'fbTitle');
            }
            if ($ogDescription) {
                $this->sender->registerMetaTag(['name' => 'description', 'property' => 'og:description', 'content' => $ogDescription], 'fbffDescriptionname');
                $this->sender->registerMetaTag(['name' => 'og:description', 'content' => $ogDescription], 'fbDescriptionname');
                $this->sender->registerMetaTag(['property' => 'og:description', 'content' => $ogDescription], 'fbDescription');
            }
            if ($ogType) {
                $this->sender->registerMetaTag(['name' => 'og:type', 'content' => $ogType], 'ogType');
                $this->sender->registerMetaTag(['property' => 'og:type', 'content' => $ogType], 'ogType');
            }
            if ($ogImageUrl) {
                $this->sender->registerMetaTag(['property' => 'og:image', 'content' => $ogImageUrl], 'ogImage');
                $this->sender->registerMetaTag(['name' => 'image', 'property' => 'og:image', 'content' => $ogImageUrl], 'ogImagLinkedin');
//            $this->sender->registerMetaTag(['name' => 'og:image', 'content' => $ogImageU rl], 'ogImageName');
                $this->sender->registerMetaTag(['name' => 'twitter:image', 'content' => $ogImageUrl], 'twitterImage');

            }
            if ($metaRobots) {
                $this->sender->registerMetaTag(['name' => 'robots', 'content' => $metaRobots], 'metaRobots');
                $this->sender->registerMetaTag(['property' => 'robots', 'content' => $metaRobots], 'metaRobots');
            }
            if ($metaGooglebot) {
                $this->sender->registerMetaTag(['name' => 'googlebot', 'content' => $metaGooglebot], 'metaGoogleBot');
                $this->sender->registerMetaTag(['property' => 'googlebot', 'content' => $metaGooglebot], 'metaGoogleBot');
            }

            $this->schemaOrg($event);
            self::$renderedSeo = true;
        }
    }
    
    private static function stringifyArrayProperty($arrayProperty){
        if(!$arrayProperty){
            return null;
        }
        
        $properties = json_decode($arrayProperty->value);
        $values = [];
        foreach($properties as $property){
            $values[] = $property->value;
        }
        return count($values) > 0 ? implode(',',$values) : null;
    }

    /**
     * @param $event \open20\amos\events\models\Event
     * @return void
     * @throws \yii\base\InvalidConfigException
     */
    public function schemaOrg($event){
        if(!$this->isRendered) {
            if(!empty($event)) {
                $event->getSchema();
                $this->isRendered = true;
            }
        }
    }
}
