<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\base
 * @category   CategoryName
 */

namespace common\modules\elasticsearch;

use open20\amos\core\interfaces\CmsModelInterface;
use open20\amos\core\record\CmsField;
use open20\elasticsearch\base\ElasticDataProvider;
use open20\elasticsearch\models\ElasticModel;
use open20\elasticsearch\models\ElasticQuery;
use open20\elasticsearch\models\ItemConditionBuilder;
use Yii;
use yii\data\ActiveDataProvider;
use open20\elasticsearch\Module;

class ElasticModelSearch extends ElasticModel implements CmsModelInterface
{
    private $module;


    public function cmsIsVisible($id)
    {
        $retValue = true;
        return $retValue;
    }

    public function init()
    {
        $this->module = Module::getInstance();
    }

    public function cmsSearch($params, $limit)
    {
        $filter       = '';
        $dataProvider = null;
        $params       = array_merge($params, Yii::$app->request->get());
        if (isset($params['searchtext'])) {
            if (empty($params['searchtext'])) {
                $params['searchtext'] = ' ';
            }
            $query             = new ElasticQuery();
            $query->searchText = $params['searchtext'];
            $query->matchAllQuery();

            $query->boolQuery()->addFilter('public', "1");


//            if (\open20\amos\core\utilities\CurrentUser::isPlatformGuest()) {
//                $query->boolQuery()->addFilter('public', "1");
//            }

            $query->minimum_should_match = 0;

            if (!empty($params['searchtext'])) {
                $allWords = explode(' ', $params['searchtext']);
                foreach ($allWords as $singleWord) {

                    $textSearch = $this->module->folding->folding(strtolower($this->purifyText($singleWord)));
                    if (strlen($textSearch) > 1) {
                        $query->boolQuery()->addMust("query", "*".$textSearch, "query_string", "open20_italian",
                            ["title^5", "location^3", "description^2", "*^1"]);
                    }
                }
            }


//            $functions = [
//                [
//                    'gauss' => ['start_publication' => ['scale' => '365d']],
//                ]
//            ];
//
//            $query->functionScoreQuery('bool', $functions, 'sum');


            $dataProvider = new ElasticDataProvider([
                'query' => $query,
            ]);
            if ($params["withPagination"]) {
                $dataProvider->setPagination(['pageSize' => $limit]);
                $query->limit(null);
            } else {
                $query->limit($limit);
            }
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



    private function purifyText($text)
    {
        $retValue = urldecode(html_entity_decode(strip_tags($text)));
        $retValue = str_replace([',', "'", '  ', '.', ';', ':', '(', ')', '[', ']', '{', '}', '!', '?', '£', '$', "\\", '/',
            '%', '&', '=', '^', '*', '§', '°', '#', '@', 'ç', '>', '<', '¿'], ' ', $retValue);
        $retValue = str_replace(['  '], '', $retValue);
        return $retValue;
    }
}