<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 11/05/2021
 * Time: 15:10
 */

namespace backend\modules\eventsadmin\models;


use http\Url;
use openinnovation\landing\Landing;
use yii\base\Model;
use yii\web\Cookie;

class BannerCookieForm extends Model
{
    public $cookieAnalytics = 0;
    public $cookieProfilazione = 0;
    public $cookieAcceptAll = 0;
    public $cookieRejectAll = 0;
    public $code;

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['cookieAnalytics', 'cookieProfilazione', 'cookieAcceptAll', 'cookieRejectAll'], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function formattedValues()
    {
        $values = [
            'cookieAnalytics' => $this->cookieAnalytics,
            'cookieProfilazione' => $this->cookieProfilazione,
            'cookieAcceptAll' => $this->cookieAcceptAll,
            'cookieRejectAll' => $this->cookieRejectAll,
        ];
        return json_encode($values);
    }

    /**
     * @param $cookie
     */
    public function loadCookie()
    {
        $cookie = null;
        if (!empty($_COOKIE['cookie_privacy'])) {
            $explode = explode('::', $_COOKIE['cookie_privacy']);
            if (count($explode) == 2) {
                $cookie = (array)json_decode($explode[1]);

            }
        }
        if ($cookie) {
            if (isset($cookie['cookieAnalytics'])) {
                $this->cookieAnalytics = $cookie['cookieAnalytics'];
            }

            if (isset($cookie['cookieProfilazione'])) {
                $this->cookieProfilazione = $cookie['cookieProfilazione'];
            }

            if (!empty($this->cookieProfilazione) && !empty($this->cookieAnalytics)) {
                $this->cookieAcceptAll = 1;
            }
//            if (isset($cookie['cookieAcceptAll'])) {
//                $this->cookieAcceptAll = $cookie['cookieAcceptAll'];
//            }
            if (isset($cookie['cookieRejectAll'])) {
                $this->cookieRejectAll = $cookie['cookieRejectAll'];
            }
        }
    }

    /**
     *
     */
    public function setCookie()
    {

        $code = uniqid('', true);
        $cookie = new Cookie([
            'name' => 'cookie_privacy',
            'value' => $code . '::' . $this->formattedValues(),
            'expire' => time() + 86400 * 183, // 6 mesi
            'domain' => \Yii::$app->params['domain'],
            'secure' => true
        ]);

        \Yii::$app->getResponse()->getCookies()->add($cookie);

        $now = new \DateTime();
        $expire = $now->add(new \DateInterval('P183D'));
        $now2 = new \DateTime();
        $expireServer = $now2->add(new \DateInterval('P365D'));
        $model = new LogCookie();
        $model->name = $cookie->name;
        $model->value = $cookie->value;
        $model->expire_date_cookie = $expire->format('Y-m-d H:i:s');
        $model->expire_date_server = $expireServer->format('Y-m-d H:i:s');
        $model->code = $code;
        $model->save(false);


    }

    /**
     * @return bool
     */
    public function showBannerCookie()
    {
        $controller =\Yii::$app->controller->id ;
        $action = \Yii::$app->controller->action->id;
        if($controller.'/'.$action != 'security/register') {
            $this->loadCookie();
            if ($this->cookieProfilazione || $this->cookieAnalytics || $this->cookieRejectAll) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    public function canSeeMedia($type)
    {
        switch ($type) {
            case \open20\amos\events\models\EventLanding::STREAMING_TYPE_YOUTUBE:
                if ($this->cookieRejectAll) {
                    return false;
                }
                if ($this->cookieAnalytics && $this->cookieProfilazione) {
                    return true;
                }
                return false;
                break;
            case \open20\amos\events\models\EventLanding::STREAMING_TYPE_FACEBOOK:
                if ($this->cookieRejectAll) {
                    return false;
                }
                if ($this->cookieAnalytics && $this->cookieProfilazione) {
                    return true;
                }
                return false;
                break;
            case \open20\amos\events\models\EventLanding::STREAMING_TYPE_MEDIAPORTAL:
                if ($this->cookieRejectAll) {
                    return false;
                }
                if ($this->cookieAnalytics) {
                    return true;
                }
                return false;
                break;
            case \open20\amos\events\models\EventLanding::MEDIA_MAPS:
                if ($this->cookieRejectAll) {
                    return false;
                }
                if ($this->cookieAnalytics) {
                    return true;
                }
                return false;
                break;
            case \open20\amos\events\models\EventLanding::MEDIA_INSTAGRAM:
                if ($this->cookieRejectAll) {
                    return false;
                }
                if ($this->cookieAnalytics) {
                    return true;
                }
                return false;
                break;
        }
        return false;

    }

    /**
     * @param $videoType
     * @param $url
     * @return string
     */
    public function alternativeTextVideo($videoType, $url, $redirectUrl = null)
    {
        $textVideo = '';
        switch ($videoType) {
            case \open20\amos\events\models\EventLanding::STREAMING_TYPE_YOUTUBE:
                $textVideo = 'Youtube';
                break;
            case \open20\amos\events\models\EventLanding::STREAMING_TYPE_FACEBOOK:
                $textVideo = 'Facebook';
                break;
            case \open20\amos\events\models\EventLanding::STREAMING_TYPE_MEDIAPORTAL:
                $textVideo = 'Mediaportal';
                break;
        }
        if (empty($redirectUrl)) {
            $redirectUrl = \Yii::$app->request->absoluteUrl;
        }
        $text = \Yii::t('site', "Guarda il video su <a target='_blank' href='{urlVideo}'>{videoType}</a> oppure modifica le impostazioni relative ai cookie analytics e di profilazione cliccando <a href='{url}'>qui</a>", [
            'videoType' => $textVideo,
            'urlVideo' => $url,
            'url' => \Yii::$app->params['platform']['backendUrl'] . '/admin/user-profile/personalize-cookie?urlRedirect=' . urlencode($redirectUrl),
        ]);
        return $text;
    }


}