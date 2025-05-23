<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 24/11/2021
 * Time: 10:50
 */

namespace app\modules\cms\models;


use open20\amos\events\models\base\EventLanding;
use open20\amos\events\models\Event;
use luya\cms\models\Block;
use luya\cms\models\Layout;
use luya\cms\models\NavItemPageBlockItem;
use Yii;
use yii\db\Query;
use yii\base\InvalidConfigException;
use yii\base\ViewContextInterface;
use luya\cms\base\NavItemType;
use luya\cms\base\NavItemTypeInterface;
use luya\cms\admin\Module;
use luya\traits\CacheableTrait;

/**
 * Represents the type PAGE for a NavItem.
 *
 * @property integer $id
 * @property integer $layout_id
 * @property integer $nav_item_id
 * @property integer $timestamp_create
 * @property integer $create_user_id
 * @property string $version_alias
 * @property Layout $layout
 * @property NavItem $forceNavItem
 *
 * @since 1.0.0
 */
class NavItemPage extends NavItemType implements NavItemTypeInterface, ViewContextInterface
{
    use CacheableTrait;

    public $event;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_DELETE, function ($event) {
            $columns = $event->sender->getNavItemPageBlockItems()->select(['id'])->column();
            NavItemPageBlockItem::deleteAll(['in', 'id', $columns]);

            if ($event->sender->forceNavItem) {
                $event->sender->forceNavItem->updateTimestamp();
            }
        });

        $this->on(self::EVENT_AFTER_UPDATE, function ($event) {
            $event->sender->forceNavItem->updateTimestamp();
        });

        $this->on(self::EVENT_BEFORE_VALIDATE, function () {
            if ($this->isNewRecord) {
                $this->timestamp_create = time();
            }
        });
    }

    /**
     * @inheritdoc
     */
    public static function getNummericType()
    {
        return NavItem::TYPE_PAGE;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_nav_item_page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['layout_id', 'timestamp_create', 'create_user_id'], 'required', 'isEmpty' => function ($value) {
                return empty($value);
            }],
            [['layout_id', 'timestamp_create', 'create_user_id', 'nav_item_id'], 'integer'],
            [['version_alias'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'layout_id' => Module::t('model_navitempage_layout_label'),
        ];
    }

    /**
     * @return \luya\cms\models\Layout
     */
    public function getLayout()
    {
        return $this->hasOne(Layout::class, ['id' => 'layout_id']);
    }

    /**
     * Get the list of version/pages for a specific nav item id
     *
     * @param integer $navItemId
     * @return NavItemPage[]
     */
    public static function getVersionList($navItemId)
    {
        return self::find()->where(['nav_item_id' => $navItemId])->with('layout')->indexBy('id')->orderBy(['id' => SORT_ASC])->all();
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields['contentAsArray'] = 'contentAsArray';
        $fields['version_alias'] = function ($model) {
            return Module::t($model->version_alias);
        };
        return $fields;
    }

    /**
     * The folder where all cms layouts are stored in order to enable partial rendering.
     *
     */
    public function getViewPath()
    {
        return '@app/views/cmslayouts';
    }

    /**
     * @throws InvalidConfigException
     */
    public function setEvent()
    {
        $eventLanding = EventLanding::find()
            ->andWhere(['OR',
                ['luya_page_id' => Yii::$app->menu->current->navId],
                ['nav_id_tks_page' => Yii::$app->menu->current->navId],
                ['nav_id_wating_page' => Yii::$app->menu->current->navId],
                ['nav_id_already_present_page' => Yii::$app->menu->current->navId],
            ])->one();
        if ($eventLanding) {
            $this->event = $eventLanding->event;
        }
    }

    /**
     * Frontend get Content returns the rendered content for this nav item page based on the page logic (placeholders, blocks)
     *
     * {@inheritDoc}
     */
    public function getContent()
    {

        $this->setEvent();
        if (!$this->layout) {
            throw new InvalidConfigException("Unable to find the requested cms layout '{$this->layout_id}' for nav item page id '{$this->id}'. Make sure your page does not have an old inactive/deleted cms layout selected.");
        }

        $placholders = [];
        foreach ($this->layout->getJsonConfig('placeholders') as $row) {
            foreach ($row as $item) {
                $placholders[$item['var']] = $this->renderPlaceholder($item['var']);
            }
        }

        // check whether the layout view file should be renderd trough active context or as file.
        // if the view_file starts with / or @ it can be renderd as renderFile() otherwise using render() with active context.
        $viewFile = $this->layout->view_file;
        $start = substr($viewFile, 0, 1);
        if ($start == '/' || $start == '@') {
            return Yii::$app->view->renderFile($this->layout->view_file, [
                'placeholders' => $placholders,
            ], $this);
        }

        return Yii::$app->view->render($this->layout->view_file, [
            'placeholders' => $placholders,
        ], $this);
    }

    /**
     * Render as the cmslayout placeholder for this model page.
     *
     * If we assume you have a placeholder variable `foobar` in the cmslayout for this page:
     *
     * ```php
     * <div>
     *     <?= $placeholders['foobar']; ?>
     * </div>
     * ```
     *
     * You can access the content of foobar with
     *
     * ```php
     * return Nav::findOne(ID_OF_THE_PAGE)->activeLanguageItem->type->renderPlaceholder('content'));
     * ```
     *
     * @param string $placeholderName The Cmslayout placeholder identifier
     * @return string
     */
    public function renderPlaceholder($placeholderName)
    {
        return $this->renderPlaceholderRecursive($this->id, $placeholderName, 0);
    }

    /**
     * Returns whether admin user is working in frontend context.
     *
     * @return boolean Whether caching should be enabled or not.
     * @since 3.5.0
     */
    private function isGuest()
    {
        return Yii::$app->has('adminuser') ? Yii::$app->adminuser->isGuest : true;
    }

    /**
     * @param $json_config_values
     * @param $json_config_cfg_values
     * @param $className
     * @return mixed
     */
    public function parseBlockDynamicVariable($json_config_values, $json_config_cfg_values, $className, $blockObject)
    {
        $availableBlock = [
            'trk\uikit\blocks\TextBlock',
            'trk\uikit\blocks\HeadlineBlock',
            'luya\cms\frontend\blocks\HtmlBlock',
            'luya\generic\blocks\WysiwygBlock',
            'app\modules\uikit\blocks\PhpPanelBlock',
        ];
        if (in_array($className, $availableBlock)) {
//            $blockObject->cacheEnabled = false;
//            pr($json_config_values, $className);

            // dynamic values from events
            if (!empty($this->event)) {
                $matches = [];
                $res = preg_match('/{{(?P<attribute>.+)}}/', $json_config_cfg_values['first_content'], $matches);
                if ($res) {
                    $attribute = $matches['attribute'];
                    $json_config_values['content'] = $this->event->getMatchedAttribute($attribute);
                }
            }

           //pr($json_config_values, $className. ' BIS');

            $content = 'content';
            if ($className == 'luya\cms\frontend\blocks\HtmlBlock') {
                $content = 'html';
            }
            // static values
            $matches_2 = [];
            $res = preg_match('/{{(?P<attribute>.+)}}/', $json_config_values[$content], $matches_2);
            if (!empty($matches_2['attribute'])) {
                $json_config_values[$content] = str_replace($matches_2[0], $this->event->getMatchedAttribute($matches_2['attribute']), $json_config_values[$content]);
            }
        }
        return $json_config_values;
    }

    /**
     * Render a placeholder recursive based on navItemPageId, a placeholder variable and a previous id.
     *
     * @param integer $navItemPageId
     * @param string $placeholderVar
     * @param integer $prevId
     * @return string
     */
    private function renderPlaceholderRecursive($navItemPageId, $placeholderVar, $prevId, $layoutBlockInstance = null)
    {
        $string = '';
        $i = 0;
        $equalIndex = 1;
        $placeholders = $this->getPlaceholders($navItemPageId, $placeholderVar, $prevId);
        $blocksCount = count($placeholders);
        $variations = Yii::$app->getModule('cmsadmin')->blockVariations;

        // foreach all placeholders but preserve varaibles above to make calculations
        foreach ($placeholders as $key => $placeholder) {
            $i++;
            $prev = $key - 1;
            $next = $key + 1;
            $cacheKey = ['blockcache', (int)$placeholder['id']];

            /** @var $blockObject \luya\cms\base\InternalBaseBlock */
            $blockObject = Block::createObject($placeholder['class'], $placeholder['block_id'], $placeholder['id'], 'frontend', $this->getNavItem());

            if ($blockObject) {

                $isCachingEnabled = $blockObject->getIsCacheEnabled() && $this->isGuest();

                $blockResponse = $isCachingEnabled ? $this->getHasCache($cacheKey) : false;

                if ($blockResponse === false) {
                    $className = get_class($blockObject);
                    // insert var and cfg values from database
                    $json_config_values = $this->jsonToArray($placeholder['json_config_values']);
                    $json_config_cfg_values = $this->jsonToArray($placeholder['json_config_cfg_values']);

                    $json_config_values = $this->parseBlockDynamicVariable($json_config_values, $json_config_cfg_values, $className, $blockObject);

                    $blockObject->setVarValues($json_config_values);
                    $blockObject->setCfgValues($json_config_cfg_values);

                    // inject variations variables
                    $possibleVariations = isset($variations[$className]) ? $variations[$className] : false;
                    $ensuredVariation = false;

                    if ($possibleVariations && isset($possibleVariations[$placeholder['variation']])) {
                        $ensuredVariation = $possibleVariations[$placeholder['variation']];
                    } elseif ($possibleVariations) {
                        foreach ($possibleVariations as $name => $content) {
                            if ($content['is_default']) {
                                $ensuredVariation = $content;
                            }
                        }
                    }
                    if ($ensuredVariation) {
                        // otherwise foreach the configuration variation and assign.
                        foreach ($ensuredVariation as $type => $typeContent) {
                            if (!empty($typeContent)) {
                                $type = strtolower($type);
                                switch ($type) {
                                    case "vars":
                                        $blockObject->setVarValues($typeContent);
                                        break;
                                    case "cfgs":
                                        $blockObject->setCfgValues($typeContent);
                                        break;
                                    case "extras":
                                        foreach ($typeContent as $extraKey => $extraValue) {
                                            $blockObject->addExtraVar($extraKey, $extraValue);
                                        }
                                        break;
                                }
                            }
                        }
                    }
                    // set env options from current object environment
                    foreach ($this->getOptions() as $optKey => $optValue) {
                        $blockObject->setEnvOption($optKey, $optValue);
                    }

                    $blockObject->setEnvOption('index', $i);
                    $blockObject->setEnvOption('itemsCount', $blocksCount);
                    $blockObject->setEnvOption('isFirst', ($i == 1));
                    $blockObject->setEnvOption('isLast', ($i == $blocksCount));
                    $prevIsEqual = array_key_exists($prev, $placeholders) && $placeholder['block_id'] == $placeholders[$prev]['block_id'];
                    $blockObject->setEnvOption('isPrevEqual', $prevIsEqual);
                    $blockObject->setEnvOption('isNextEqual', array_key_exists($next, $placeholders) && $placeholder['block_id'] == $placeholders[$next]['block_id']);

                    if (!$prevIsEqual) {
                        $equalIndex = 1;
                    } else {
                        $equalIndex++;
                    }
                    $blockObject->setEnvOption('equalIndex', $equalIndex);

                    // render sub placeholders and set into object
                    $insertedHolders = [];
                    foreach ($blockObject->getConfigPlaceholdersExport() as $item) {
                        $insertedHolders[$item['var']] = $this->renderPlaceholderRecursive($navItemPageId, $item['var'], $placeholder['id'], $blockObject);
                    }
                    $blockObject->setPlaceholderValues($insertedHolders);
                    // output buffer the rendered frontend method of the block
                    if ($layoutBlockInstance) {
                        $blockResponse = $layoutBlockInstance->placeholderRenderIteration($blockObject);
                    } else {
                        $blockResponse = $blockObject->renderFrontend();
                    }


                    if ($isCachingEnabled) {
                        $this->setHasCache($cacheKey, $blockResponse, null, $blockObject->getCacheExpirationTime());
                        $blockObject->onRegister();
                    }
                } else {
                    $blockObject->onRegisterFromCache();
                }

                $string .= $blockResponse;
                unset($blockResponse);
            }
        }

        unset($variations);
        unset($placeholders);

        return $string;
    }

    /**
     * Get all placeholders as array for a given pageId, placeholder and prevId.
     *
     * @param integer $navItemPageId
     * @param string $placeholderVar
     * @param integer $prevId
     * @return array
     */
    private function getPlaceholders($navItemPageId, $placeholderVar, $prevId)
    {
        return (new Query())
            ->from('cms_nav_item_page_block_item t1')
            ->select(['t1.id', 't1.block_id', 't1.json_config_values', 't1.json_config_cfg_values', 't1.variation', 't2.class'])
            ->innerJoin('cms_block t2', 't2.id=t1.block_id')
            ->where(['nav_item_page_id' => $navItemPageId, 'placeholder_var' => $placeholderVar, 'prev_id' => $prevId, 'is_hidden' => 0])
            ->orderBy(['sort_index' => SORT_ASC])
            ->all();
    }

    /**
     * Convert a json string to an array.
     *
     * @param string $json
     * @return array
     */
    private function jsonToArray($json)
    {
        $response = json_decode($json, true);

        return (empty($response)) ? [] : $response;
    }

    /**
     * Get the full array content from all the blocks, placeholders, vars configs and values recursiv for this current NavItemPage (which is layout version for a nav item)
     * @return array
     */
    public function getContentAsArray()
    {
        //$nav_item_page = (new \yii\db\Query())->select('*')->from('cms_nav_item_page t1')->leftJoin('cms_layout', 'cms_layout.id=t1.layout_id')->where(['t1.id' => $this->id])->one();
        $nav_item_page = $this;

        if (!$nav_item_page->layout) {
            return [];
        }

        $return = [
            'nav_item_page' => ['id' => $nav_item_page->id, 'layout_id' => $nav_item_page->layout_id, 'layout_name' => $nav_item_page->layout->name],
            '__placeholders' => [],
        ];

        $config = json_decode($nav_item_page->layout->json_config, true);

        if (isset($config['placeholders'])) {
            foreach ($config['placeholders'] as $rowKey => $row) {
                foreach ($row as $placeholderKey => $placeholder) {
                    $placeholder['nav_item_page_id'] = $this->id;
                    $placeholder['prev_id'] = 0;
                    $placeholder['__nav_item_page_block_items'] = [];
                    if (!isset($placeholder['cols'])) {
                        $placeholder['cols'] = '12';
                    }

                    $return['__placeholders'][$rowKey][$placeholderKey] = $placeholder;

                    $placeholderVar = $placeholder['var'];

                    $return['__placeholders'][$rowKey][$placeholderKey]['__nav_item_page_block_items'] = self::getPlaceholder($placeholderVar, 0, $this);
                }
            }
        }

        return $return;
    }

    /**
     * Get the blocks for a given placeholder, **without recursion**.
     *
     * @param string $placeholderVar
     * @param integer $prevId
     * @param NavItemPage $navItemPage
     * @return array
     */
    public static function getPlaceholder($placeholderVar, $prevId, NavItemPage $navItemPage)
    {
        $nav_item_page_block_item_data = NavItemPageBlockItem::find()
            ->where(['prev_id' => $prevId, 'nav_item_page_id' => $navItemPage->id, 'placeholder_var' => $placeholderVar])
            ->orderBy(['sort_index' => SORT_ASC])
            ->with(['block'])
            ->all();

        $data = [];

        foreach ($nav_item_page_block_item_data as $blockItem) {
            $item = self::getBlockItem($blockItem, $navItemPage);
            if ($item) {
                $data[] = $item;
            }

            unset($item);
        }

        return $data;
    }

    /**
     *
     * @param integer $blockId
     * @return array
     * @deprecated Deprecated since 1.0.6 use getBlockItem() instead. Will be removed in 4.0
     */
    public static function getBlock($blockId)
    {
        trigger_error('deprecated, use getBlockItem() instead.', E_USER_DEPRECATED);
        $blockItem = NavItemPageBlockItem::findOne($blockId);

        return self::getBlockItem($blockItem, $blockItem->navItemPage);
    }

    /**
     * Get the arrayable values from a specific block id.
     *
     * @param integer $blockId
     * @return array
     */
    public static function getBlockItem(NavItemPageBlockItem $blockItem, NavItemPage $navItemPage)
    {
        // if the block relation could be found, return false.
        if (!$blockItem->block) {
            return false;
        }

        $blockObject = $blockItem->block->getObject($blockItem->id, 'admin', $navItemPage);
        if ($blockObject === false) {
            return false;
        }

        $blockItem['json_config_values'] = json_decode($blockItem['json_config_values'], true);
        $blockItem['json_config_cfg_values'] = json_decode($blockItem['json_config_cfg_values'], true);

        $blockValue = $blockItem['json_config_values'];
        $blockCfgValue = $blockItem['json_config_cfg_values'];

        $blockObject->setVarValues((empty($blockValue)) ? [] : $blockValue);
        $blockObject->setCfgValues((empty($blockCfgValue)) ? [] : $blockCfgValue);

        $placeholders = [];

        foreach ($blockObject->getConfigPlaceholdersByRowsExport() as $rowKey => $row) {
            foreach ($row as $pk => $pv) {
                $pv['nav_item_page_id'] = $blockItem['nav_item_page_id'];
                $pv['prev_id'] = $blockItem['id'];
                $placeholderVar = $pv['var'];

                $pv['__nav_item_page_block_items'] = static::getPlaceholder($placeholderVar, $blockItem['id'], $navItemPage);

                $placeholder = $pv;

                $placeholders[$rowKey][] = $placeholder;
            }
        }

        if (empty($blockItem['json_config_values'])) {
            $blockItem['json_config_values'] = ['__e' => '__o'];
        }

        if (empty($blockItem['json_config_cfg_values'])) {
            $blockItem['json_config_cfg_values'] = ['__e' => '__o'];
        }

        $variations = Yii::$app->getModule('cmsadmin')->blockVariations;

        $className = get_class($blockObject);
        $shortName = (new \ReflectionClass($blockObject))->getShortName();

        return [
            'is_dirty' => (bool)$blockItem['is_dirty'],
            'is_container' => (int)$blockObject->getIsContainer(),
            'id' => $blockItem['id'],
            'block_id' => $blockItem['block_id'],
            'block_class' => $className,
            'block_class_name' => $shortName,
            'is_hidden' => $blockItem['is_hidden'],
            'name' => $blockObject->name(),
            'icon' => $blockObject->icon(),
            'full_name' => ($blockObject->icon() === null) ? $blockObject->name() : '<i class="material-icons">' . $blockObject->icon() . '</i> <span>' . $blockObject->name() . '</span>',
            'twig_admin' => $blockObject->renderAdmin(),
            'vars' => $blockObject->getConfigVarsExport(),
            'cfgs' => $blockObject->getConfigCfgsExport(),
            'extras' => $blockObject->getExtraVarValues(),
            'values' => $blockItem['json_config_values'],
            'field_help' => $blockObject->getFieldHelp(),
            'cfgvalues' => $blockItem['json_config_cfg_values'], // add: t1_json_config_cfg_values
            '__placeholders' => $placeholders,
            'variations' => isset($variations[$className]) ? $variations[$className] : false,
            'variation' => empty($blockItem['variation']) ? "0" : $blockItem['variation'], // as by angular selection
            'is_dirty_dialog_enabled' => $blockObject->getIsDirtyDialogEnabled(),
        ];
    }

    /**
     * Copy blocks from one page to another.
     *
     * @param integer $fromPageId
     * @param integer $toPageId
     * @return boolean
     */
    public static function copyBlocks($fromPageId, $toPageId)
    {
        $pageBlocks = NavItemPageBlockItem::find()->where(['nav_item_page_id' => $fromPageId])->asArray(true)->all();

        $idLink = [];
        foreach ($pageBlocks as $block) {
            $blockItem = new NavItemPageBlockItem();
            $blockItem->attributes = $block;
            $blockItem->nav_item_page_id = $toPageId;
            if ($blockItem->insert()) {
                $idLink[$block['id']] = $blockItem->id;
            }
        }

        // as blocks with subblocks have the previous block id stored in prev_id those values must be replaced from the old prev_id
        // with the new prev_id
        $newPageBlocks = NavItemPageBlockItem::find()->where(['nav_item_page_id' => $toPageId])->asArray(true)->all();
        foreach ($newPageBlocks as $block) {
            if ($block['prev_id'] && isset($idLink[$block['prev_id']])) {
                // update the given blocks' prev_id
                NavItemPageBlockItem::updateAll(['prev_id' => $idLink[$block['prev_id']]], ['id' => $block['id']]);
            }
        }

        return true;
    }

    /**
     * This method is used to force the parent nav item for the corresponding page item whether the type matches or not:
     *
     * @return \luya\cms\models\NavItem
     */
    public function getForceNavItem()
    {
        return $this->hasOne(\luya\cms\models\NavItem::class, ['id' => 'nav_item_id']);
    }

    /**
     * Return all page block items for the current corresponding page. Not related to any sortings or placeholders.
     *
     * @return \luya\cms\models\NavItemPageBlockItem
     */
    public function getNavItemPageBlockItems()
    {
        return $this->hasMany(NavItemPageBlockItem::class, ['nav_item_page_id' => 'id']);
    }
}
