<?php

/**
 * @package   yii2-datecontrol
 * @version   1.9.9
 */

namespace kartik\datecontrol\controllers;

use DateTimeZone;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use kartik\datecontrol\DateControl;

/**
 * ParseController class manages the actions for date conversion via ajax from display to save.
 */
class ParseController extends Controller
{
    /**
     * Convert display date for saving to model.
     *
     * @return array JSON encoded HTML output
     */
    public function actionConvert()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $req = Yii::$app->request;
        $post = $req->post();
        if (!isset($post['displayDate'])) {
            return ['status' => 'error', 'output' => 'No display date found'];
        }
        $saveFormat = $req->post('saveFormat', '');
        $saveTimezone = $req->post('saveTimezone', '');
        $dispFormat = $req->post('dispFormat', '');
        $dispTimezone = $req->post('dispTimezone', '');
        $displayDate = $req->post('displayDate', '');
        $settings = $req->post('settings', []);
        $date = DateControl::getTimestamp($displayDate, $dispFormat, $dispTimezone, $settings);
        if (empty($date) || !$date) {
            $value = '';
        } elseif ($saveTimezone != null) {
            $value = $date->setTimezone(new DateTimeZone($saveTimezone))->format($saveFormat);
        } else {
            $value = $date->format($saveFormat);
        }
        return ['status' => 'success', 'output' => $value];
    }
}