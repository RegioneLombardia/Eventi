<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\base
 * @category   CategoryName
 */
namespace open20\elasticsearch\models\search;

use open20\amos\core\interfaces\CmsModelInterface;
use open20\amos\core\record\CmsField;
use open20\elasticsearch\base\ElasticDataProvider;
use open20\elasticsearch\models\ElasticModel;
use open20\elasticsearch\models\ElasticQuery;
use open20\elasticsearch\Module;
use Yii;

class ElasticModelSearch extends ElasticModel implements CmsModelInterface 
{
    private $module;
    
    
    public function init()
    {
        $this->module = Module::getInstance();
    }
    
    
    public function cmsIsVisible($id) 
    {
        $retValue = true;
        return $retValue;
    }
    
    public function cmsSearch($params, $limit) 
    {
        $filter = '';
        
        $params = array_merge($params, Yii::$app->request->get());
        $query = new ElasticQuery();
        $query->matchAllQuery();
        $tags = $this->composeTagSearch($params);
        if(count($tags))
        {
            $query->boolQuery()->addShould('tags', $tags);
        }
        $cats = $this->composeCategorySearch($params);
        if(count($cats) > 0)
        {
            $query->boolQuery()->addAsBoolFilter('content_type', $cats, 'match');
        }
        if(!empty($params['searchtext']))
        {
            $query->boolQuery()->addMust("query", "*" . $this->module->folding->folding($params['searchtext']), "query_string", "open20_italian", ["title^5","*^1"]);
        }
        
        $dataProvider = new ElasticDataProvider([
            'query' => $query,
        ]);
        if ($params["withPagination"]) {
            $dataProvider->setPagination(['pageSize' => $limit]);
            $query->limit(null);
        }else{
            $query->limit($limit);
        }        
        return $dataProvider;
    }

    public function cmsSearchFields() 
    {
        $searchFields = [];

        array_push($searchFields, new CmsField("title", "TEXT"));
        array_push($searchFields, new CmsField("description", "TEXT"));

        return $searchFields;
    }

    public function cmsViewFields() 
    {
        return [
            new CmsField('title', 'TEXT', 'amoselasticsearch', $this->attributeLabels()['title']),
            new CmsField('description', 'TEXT', 'amoselasticsearch', $this->attributeLabels()['description']),
        ];
    }

    
    /**
     * 
     * @param type $params
     */
    private function composeTagSearch($params)
    {
        $tags = [];
        $filtered = array_filter(
                $params,
                function ($val, $key) { 
                    return strpos($key, 'TAG_') !== false ;
                },
                ARRAY_FILTER_USE_BOTH
                ); 
        foreach($filtered  as $key=>$value)
        {
            $tags[] = str_replace('TAG_', '', $key);
        }
        
        return $tags;
    }
    
    /**
     * 
     * @param type $params
     * @return type
     */
    private function composeCategorySearch($params)
    {
        $category = [];
        $filtered = array_filter(
                $params,
                function ($val, $key) { 
                    return strpos($key, 'CATEGORY_') !== false ;
                },
                ARRAY_FILTER_USE_BOTH
                ); 
        foreach($filtered  as $key=>$value)
        {
            $category[] = str_replace(' ', '_',str_replace('CATEGORY_', '', $key));
        }
        return $category;
    }

}
