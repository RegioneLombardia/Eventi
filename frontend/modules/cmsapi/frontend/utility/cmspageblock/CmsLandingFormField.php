<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\cmsapi\frontend\utility\cmspageblock;

use app\modules\cmsapi\frontend\utility\CmsObject;

/**
 * Description of CmsLandigFormField
 *
 */
class CmsLandingFormField extends CmsObject
{
    public $type;
    public $label;
    public $required;
    public $field;
    public $subvalue = [];
    public $otheroptions = [];

}