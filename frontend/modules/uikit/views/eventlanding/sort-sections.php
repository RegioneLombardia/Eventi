<?php
use open20\amos\events\models\Event;
/**
 * @var $eventLanding
 */
$configLandingSections = Event::getConfigLandingSections();
$orderSections = $eventLanding->getEventLandingSections()->asArray()->orderBy('n_order Asc')->all();
foreach($orderSections as $section){
    $classToModify = $configLandingSections[$section['section']]['container_id'];
    $nOrder = $section['n_order'];
    if($section['section'] == 'landing_form'){
        $js = <<<JS
                $('.section-landing-cms-form').addClass('order-$nOrder');

JS;

    }else {
        $js = <<<JS
                $('#$classToModify').addClass('order-$nOrder');
JS;
    }
    $this->registerJs($js);
}