<?php

namespace amos\userauth\frontend\properties;

use amos\userauth\frontend\Module;
use amos\userauth\frontend\utility\cmspageblock\CmsDataPageBlock;
use open20\amos\community\models\Community;
use open20\amos\community\utilities\CommunityUtil;
use luya\admin\base\CheckboxProperty;
use luya\admin\models\Config;
use luya\cms\frontend\events\BeforeRenderEvent;
use luya\cms\helpers\Url;
use luya\cms\menu\QueryOperatorFieldInterface;
use luya\cms\models\NavItem;
use Yii;
use yii\helpers\ArrayHelper;

class UserAuthProtection extends CheckboxProperty
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->on(self::EVENT_BEFORE_RENDER, [$this, 'ensureUserAccess']);
    }

    /**
     * Check whether the current page requires protection and user is logged in.
     *
     * @param BeforeRenderEvent $event
     */
    public function ensureUserAccess(BeforeRenderEvent $event)
    {
        $moduleUserAuth = \Yii::$app->getModule('userauthfrontend');
        if ($this->getValue() == 1) {
            if (Yii::$app->user->isGuest) {

                if(!empty($moduleUserAuth->redirectLoginBackend)){
                    $redirLoginBackend = \Yii::$app->params['platform']['backendUrl'].$moduleUserAuth->redirectLoginBackend.'?redir='. urlencode(\Yii::$app->request->absoluteUrl);
                    return Yii::$app->response->redirect($redirLoginBackend);
                }
                // find the nav item to redirect from config
                $navItem = Yii::$app->menu->find()->where([QueryOperatorFieldInterface::FIELD_NAVID => Config::get(Module::USERAUTH_CONFIG_REDIRECT_NAV_ID)])->with([
                    'hidden'])->one();

                // redirect to the given nav item
//                var_dump(\Yii::$app->request->absoluteUrl);
                return Yii::$app->response->redirect($navItem->absoluteLink . '?redir=' . urlencode(\Yii::$app->request->absoluteUrl));
            } else {
                if (!$this->canSeePage()) {
                    $navItem = Yii::$app->menu->find()->where([QueryOperatorFieldInterface::FIELD_NAVID => Config::get(Module::NOPERMISSION_CONFIG_REDIRECT_NAV_ID)])->with([
                        'hidden'])->one();
                    if ($navItem !== false) {
                        // redirect to the given nav item
                        return Yii::$app->response->redirect($navItem->absoluteLink);
                    } else {
                        return Yii::$app->response->redirect(Url::home(true));
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function varName()
    {
        return 'userAuthProtection';
    }

    /**
     * @inheritdoc
     */
    public function label()
    {
        return Module::t('userauth.propertie.userauthprotection.label');
    }

    /**
     *
     */
    protected function canSeePage()
    {
        $canSee = false;
        $canSeeInivitedToEvent = false;
        $canSeeOperator = false;
        $nav = Yii::$app->menu->current;
        $nav_item = NavItem::findOne(['nav_id' => $nav->itemArray['nav_id']]);
        if (!is_null($nav_item)) {
            $values = $this->getBlockConfigValues($nav_item->nav_item_type_id,
                CmsDataPageBlock::class);
            if (isset($values['community_id'])) {
                $community = Community::findOne($values['community_id']);
                if ($community->context == 'open20\amos\events\models\Event') {
                    $ContextClassname = $community->context;
                    $event = $ContextClassname::find()->andWhere(['community_id' => $values['community_id']])->one();
                    if ($event) {
                        $groupIds = [1, $event->event_group_referent_id];
                        $isOperator = \open20\amos\events\models\EventGroupReferentMm::find()
                            ->andWhere(['event_group_referent_mm.user_id' => \Yii::$app->user->id])
                            ->andWhere(['event_group_referent_mm.exclude_from_query' => true])
                            ->andWhere(['event_group_referent_mm.event_group_referent_id' => $groupIds])->count();
                        $canSeeOperator = $isOperator > 0;

                        $isInvited = \open20\amos\events\models\EventInvitationSent::find()
                            ->innerJoin('event_internal_list', 'event_internal_list.id = event_invitation_sent.event_internal_list_id')
                            ->andWhere(['event_invitation_sent.user_id' => \Yii::$app->user->id])
                            ->andWhere(['event_internal_list.event_id' => $event->id])
                            ->andWhere(['event_internal_list.deleted_at' => null])
                            ->count();
                        $canSeeInivitedToEvent = ($isInvited > 0);
                    }

                }
                $canSee = CommunityUtil::userIsCommunityMember($values['community_id'],
                    \Yii::$app->user->id);
            }
        }
        return $canSee || $canSeeInivitedToEvent || $canSeeOperator;
    }

    /**
     *
     * @param integer $nav_item_page_id
     * @param type $class_
     * @return array
     */
    protected function getBlockConfigValues($nav_item_page_id, $class_)
    {
        $items = [];
        $blocks = $this->findBlockModules($nav_item_page_id, $class_);
        foreach ($blocks as $block) {
            $items = ArrayHelper::merge($items, $block->getCfgValues());
        }
        return $items;
    }

    /**
     *
     * @param type $nav_item_page_id
     * @param type $class_
     * @return type
     */
    protected function findBlockModules($nav_item_page_id, $class_)
    {
        $blocks = $class_::findBlocks($nav_item_page_id);
        return $blocks;
    }
}