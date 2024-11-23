<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 20/05/2021
 * Time: 09:20
 */


namespace app\modules\uikit\blocks;


use app\modules\cmsapi\frontend\utility\CmsBlocksBuilder;
use app\modules\uikit\Module;
use luya\cms\frontend\blockgroups\MediaGroup;
use luya\cms\frontend\blockgroups\TextGroup;
use app\modules\uikit\Uikit;
use yii\helpers\Json;
use app\modules\cmsapi\frontend\models\PostCmsCreatePage;


class ContactFormSaleBlock extends \app\modules\uikit\BaseUikitBlock
{


    public $cacheEnabled = false;
    /**
     * @inheritdoc
     */
    protected $component = "contactformsale";

    public function init()
    {
        parent::init();
        $this->cacheEnabled = false;
    }

    /**
     * @inheritdoc
     */
    public function blockGroup()
    {
        return MediaGroup::class;
    }

    /**
     * @inheritdoc
     */
    public function name()
    {
        return Module::t('Contatti Sale');
    }


    /**
     * @inheritdoc
     */
    public function icon()
    {
        return 'view_module';
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function frontend(array $params = array())
    {
        $data = [];
        if (!Uikit::element('data', $params, '')) {
            $configs = $this->getValues();
            $configs["id"] = Uikit::unique($this->component);
            $params['data'] = Uikit::configs($configs);
            $params['debug'] = $this->config();
            $params['filters'] = $this->filters;
            $data = $params['data'];
        }

        $model = new \frontend\models\ContactFormSale;
        $model->emailToSend = $data['email'];
        $data['model'] = $model;
        $messageOk = '';

        if(\Yii::$app->request->isPost && $model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->sendEmail();
            \Yii::$app->session->addFlash('success', "Messaggio inviato correttamente");
            $messageOk = \Yii::t('app', "Messaggio inviato correttamente");
        }
        $data['messageOk'] = $messageOk;


        return $this->view->render($this->getViewFileName('php'), $data, $this);
    }

    /**
     * @inheritdoc
     */
    public function admin()
    {
        return '';
    }


}