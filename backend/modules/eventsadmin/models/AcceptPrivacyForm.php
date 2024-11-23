<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 23/03/2021
 * Time: 14:58
 */

namespace backend\modules\eventsadmin\models;


use open20\amos\admin\AmosAdmin;
use yii\base\Model;

class AcceptPrivacyForm extends Model
{

    public $privacy;
    public $privacy_2;
    public $profile;

    public function rules()
    {
       return [
           [['privacy'], 'required', 'requiredValue' => 1, 'message' => AmosAdmin::t('amosadmin', "#register_privacy_alert_not_accepted")],
           [['privacy_2'], 'safe'],
       ];
    }
}