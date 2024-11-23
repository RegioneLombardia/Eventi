<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 11/05/2021
 * Time: 12:34
 */

namespace backend\modules\eventsadmin\widgets;


use backend\modules\eventsadmin\models\BannerCookieForm;
use yii\base\Widget;

class BannerCookieWidget extends Widget
{

    public function run()
    {
        $model = new BannerCookieForm();
        if (\Yii::$app->controller->action->id != 'personalize-cookie') {
            if ($model->showBannerCookie()) {
                return $this->render('banner_cookie');
            }
        }
        return '';
    }

}