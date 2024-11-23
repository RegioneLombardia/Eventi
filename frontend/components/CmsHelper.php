<?php

namespace app\components;

use luya\admin\models\Lang;
use luya\cms\models\NavItem;
use Yii;

class CmsHelper {
    
    /**
     * render recursively luya menu.
     * 
     * @param array $menu
     */
    public static function MenuRender($menu)
    {
        $html = '';
        
        foreach ($menu as $item)
        {
            $html.='<li class="'. ($item->isActive ? ' active preopened' : '') . ($item->hasChildren ? ( $item->depth == 1 ? ' dropdown' : ' dropdown-submenu') : '' ).'">';
            $html.='<a class="'. ($item->hasChildren ? ' nav-link dropdown-toggle' : '').'" '.( $item->hasChildren ? ' data-toggle="dropdown"' : '') .' href="'. ($item->hasChildren ? "javascript:void(0)" : $item->link ).'">'. $item->title;
            $html.= $item->hasChildren ? '<span class="am am-chevron-down"></span>' : '';
            $html.= '</a>';
            if($item->hasChildren)
            {
                $html.= '<ul class="dropdown-menu dropdown-menu-left">';
                $html.= static::MenuRender(Yii::$app->menu->getLevelContainer($item->depth + 1, $item));
                $html.= '</ul>';
            }
            $html.= '</li>';
        }
        return $html;
    }

    /**
     *
     * @param type $itemId
     * @param type $version
     * @return type
     */
    public static function ContentRender($itemId, $version = false)
    {
        $rendered = '';
        $controller = Yii::$app->controller;
        $language_code = Yii::$app->composition['langShortCode'];


        $language = Lang::findOne(['short_code' => $language_code ]);
        if(!is_null($language))
        {
            $navItem = NavItem::findOne(['nav_id' => $itemId, 'lang_id' => $language->id]);
            if(!is_null($navItem))
            {
                $rendered = $controller->renderItem($navItem->id, null, $version);
            }
        }

        return $rendered;
    }

    /**
     * @param $text
     * @param int $chars
     * @return bool|string
     */
    public static function truncate($text, $chars = 25)
    {
        if (strlen($text) <= $chars) {
            return $text;
        }
        $text = $text . " ";
        $text = substr($text, 0, $chars);
        $text = substr($text, 0, strrpos($text, ' '));
        $text = $text . "...";
        return $text;
    }
}
