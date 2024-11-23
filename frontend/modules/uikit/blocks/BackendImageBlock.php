<?php


namespace app\modules\uikit\blocks;


use app\modules\cmsapi\frontend\utility\CmsBlocksBuilder;
use app\modules\uikit\Module;
use luya\cms\frontend\blockgroups\MediaGroup;
use luya\cms\frontend\blockgroups\TextGroup;
use app\modules\uikit\Uikit;
use yii\helpers\Json;
use app\modules\cmsapi\frontend\models\PostCmsCreatePage;

class BackendImageBlock extends \app\modules\uikit\BaseUikitBlock
{

    public $cacheEnabled = false;
    /**
     * @inheritdoc
     */
    protected $component = "backendimage";

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
        return Module::t('Immagine backend');
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
        if (!empty($data['model_classname']) && !empty($data['record_id'])) {
            $Classname = $data['model_classname'];
            $record_id = $data['record_id'];
        }

        $model = $Classname::findOne($record_id);
        if (!empty($model)) {
            if (!empty($data['attribute'])) {
                $attribute = $data['attribute'];
                if ($attribute == 'eventLogo') {
                    $data['url'] = $model->getMainImageEvent();
                } else {
                    $file = $model->$attribute;
                    if (!empty($file)) {
                        $data['url'] = $file->getWebUrl();
                    }
                }
            }
            $data['model'] = $model;
            $data['options'] = [
                'class' => $data['class'],
                'title' => $data['title'],
                'id' => get_class($model) . '-' . $model->id
            ];
        }

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