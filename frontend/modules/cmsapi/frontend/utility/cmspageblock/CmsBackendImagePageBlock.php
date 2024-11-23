<?php

namespace app\modules\cmsapi\frontend\utility\cmspageblock;

use app\modules\cmsapi\frontend\models\PostCmsCreatePage;
use app\modules\cmsapi\frontend\utility\CmsBlocksBuilder;
use yii\helpers\Json;

class CmsBackendImagePageBlock extends CmsPageBlock
{

    public function buildValues(PostCmsCreatePage $postCmsPage)
    {

        $values                    = Json::decode($this->json_config_values);
        $values['model_classname'] = 'open20\amos\\events\models\Event';
        $values['record_id'] = $postCmsPage->event_data->event_id;
        $values['title'] = $postCmsPage->event_data->title;
        $values['class'] = 'img-event';
        $this->json_config_values  = Json::encode($values);
    }

    /**
     *
     * @param type $nav_item_page_id
     * @return array
     */
    public static function findBlocks($nav_item_page_id)
    {
        $id_block = static::findBlock(CmsBlocksBuilder::BACKENDIMAGE);
        $blocks   = static::find()->
            andWhere(['nav_item_page_id' => $nav_item_page_id])->
            andWhere(['block_id' => $id_block->id])
            ->all();
        return $blocks;
    }


}