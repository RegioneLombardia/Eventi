<?php
namespace frontend\bootstrap;

use app\modules\uikit\Uikit;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\View;
use yii\helpers\Json;


class BeforeRenderViewEvent extends \yii\base\Event implements BootstrapInterface
{
    /**
     * @param $app
     */
    public function bootstrap($app)
    {
        Event::on(\luya\cms\base\PhpBlockView::className(), \luya\cms\base\PhpBlockView::EVENT_BEFORE_RENDER, [$this, 'beforeRender']);
    }

    public function beforeRender($event){
        $phpView = $event->sender;
        if(get_class($phpView->context) == 'trk\uikit\blocks\TextBlock'){
//            pr($phpView);
            $configs = $phpView->context->getValues();
//            pr(\Yii::$app->controller->view->params);
//            \Yii::$app->controller->view->params['content'] = 'ciao';
//            pr( Uikit::configs($configs),'cfg');
//            $configsextra = $phpView->context->extraVars();
//            pr($configs);
//            pr($configsextra,'extra');
//            pr($phpView->params);

//            die;
        }
//        pr($phpView->json_config_cfg_values);
////pr($event->sender,'hhhh');
//die;
//        $myfile = fopen("debug_luya.txt", "a") or die("Unable to open file!");
//        $txt = get_class($phpView->context)."\n";
//        fwrite($myfile, $txt);
//        fclose($myfile);
    }


}