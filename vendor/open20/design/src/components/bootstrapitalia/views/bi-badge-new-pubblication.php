<?php

use open20\design\assets\BootstrapItaliaDesignAsset;
use open20\design\Module;

$bootstrapItaliaAsset = BootstrapItaliaDesignAsset::register($this);

if(empty($class_span)){
    $class_span = "badge-new-pubblication d-flex align-items-center";
}
if(empty($class_div_container)){
    $class_div_container = "badge badge-pill badge-danger text-uppercase";
}
?>

<div class="<?= $class_span?>">
  <span class="<?=  $class_div_container?>">
        <?= Module::t('amosdesign', "NEW"); ?>
  </span>
</div>