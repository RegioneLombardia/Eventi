<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\base
 * @category   CategoryName
 */
namespace open20\elasticsearch\models;

use open20\amos\core\utilities\ClassUtility;
use open20\elasticsearch\Module;
use yii\base\ArrayableTrait;
use yii\base\BaseObject;

class ElasticIndex extends BaseObject
{
    use ArrayableTrait;
    
    private $module;
    private $model = null;
    private $attributes = [];
    
    public $index;
    public $type;
    public $id;
    public $routing;
    public $timestamp;
    public $body;
    
    
    public function getModel()
    {
        return $this->model;
    }
    
    public function setModel($model)
    {
        $this->model = $model;
    }
    
    public function init()
    {
        $this->module = Module::instance();
        $this->rules();
    }
    
    /**
     * 
     */
    protected function rules()
    {
        $this->attributes = [
            'index','type','id',/*'routing','timestamp',*/'body'
        ];
    }
    
    /**
     * 
     * @return type
     */
    public function save()
    {
        $ret = false;
        if($this->model->canSaveIndex())
        {
            $this->index = strtolower(ClassUtility::getClassBasename($this->model));
            $this->type = self::purifyIndexType($this->model->className());
            $this->id = $this->model->primaryKey;
            //$this->routing = 'company_xyz';
            $this->timestamp = strtotime("-1d");
            $this->body = $this->model->toElasticArray();

            $ret = $this->module->client->index($this->toArray($this->attributes));
            //$this->setSettings($this->index);
        }
        return $ret;
    }
    
    /**
     * 
     * @return type
     */
    public function delete()
    {
        
        $this->index = strtolower(ClassUtility::getClassBasename($this->model));
        $this->type = self::purifyIndexType($this->model->className());
        $this->id = $this->model->primaryKey;        
        
        return $this->module->client->delete($this->toArray($this->attributes));
    }
    
    /**
     * 
     * @param string $type
     * @return string
     */
    public static function purifyIndexType($type)
    {
        return \open20\amos\core\utilities\StringUtils::replace($type, "\\", "_");
    }
    
    
    public static function classFromIndexType($type)
    {
        return \open20\amos\core\utilities\StringUtils::replace($type, "_", "\\");
    }
    
    /**
     * 
     * @param type $index
     * @return type
     */
    protected function setSettings($index)
    {
        if(!is_null($this->module->defualtIndexSettings))
        {
            $params = [
                    'index' => $index,
                    'body' => [
                        'settings' => $this->module->defualtIndexSettings
                    ]
            ];

            $result = $this->module->client->indices()->putSettings($params);
        }
    }
}
