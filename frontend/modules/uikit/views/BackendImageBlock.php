<?php
/**
 * @var string $url
 * @var array $options
 * @var Object $model
 * @var string $attribute
 */

if(!empty($url)) {
    if(empty($options)){
        $options = [];
    }
    $optionsDesktop = $options;
    $optionsMobile = $options;
    $optionsDesktop['class'] = 'img-event img-desktop';
    $optionsMobile['class'] = 'img-event img-mobile';
//    $optionsMobile['style'] = 'display:none';

    echo \yii\helpers\Html::img($url, $optionsDesktop);

    if($attribute == 'eventLogo') {
        if(!empty($model->eventLogoMobile)){
            $urlMobile = $model->eventLogoMobile->getWebUrl();
            echo \yii\helpers\Html::img($urlMobile, $optionsMobile);
        }
    }

}
?>

