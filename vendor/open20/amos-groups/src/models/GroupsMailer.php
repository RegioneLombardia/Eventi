<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 13/12/2017
 * Time: 12:41
 */

namespace open20\amos\groups\models;


use open20\amos\core\utilities\Email;
use open20\amos\groups\Module;
use yii\base\Exception;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class GroupsMailer extends Model
{
    public $idGroup;
    public $email;
    public $subject;
    public $text;

    public function rules()
    {
        return [
            ['idGroup', 'integer'],
            ['email', 'safe'],
            [['subject', 'text'], 'required'],
        ];
    }

    /**
     * @param $idGroup
     * @param $subject
     * @param $text
     * @param array $files
     * @param bool $queue
     * @throws NotFoundHttpException
     */
    public static function sendEmail($idGroup, $subject, $text, $files = [], $queue = false){
//        $groupsModule = \Yii::$app->getModule(Module::getModuleName());
        /** @var  $model Groups */
        $model = Groups::findOne($idGroup);
        if(empty($model)) {
            throw new NotFoundHttpException();
        }

        $userMembers = $model->users;
        $emailList = [];
        foreach ($userMembers as $user) {
            $emailList []= $user->email;
        }
        $emaiFrom = \Yii::$app->params['supportEmail'];
        if(!empty(\Yii::$app->getModule(Module::getModuleName())->email)) {
            $emaiFrom = \Yii::$app->getModule(Module::getModuleName())->email;
        }

        /** @var  $email Email*/
        try {
            $emailModule = \Yii::$app->getModule('email');
            $email = new Email();
//                if(!empty($groupsModule->layoutEmail)) {
//                    $emailModule->defaultLayout = $groupsModule->layoutEmail;
//                }
            $sent = $email->sendMail(
                $emaiFrom,
                $emailList,
                $subject,
                $text,
                $files,
                [],
                [],
                0,
                $queue
            );
            return $sent;
        }catch( Exception $e) {
            return false;
//                pr($e->getTrace());
        }
    }

}