<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace open20\elasticsearch\models;

/**
 *
 */
interface InterfaceItemCondition 
{
    public function getField();
    public function setField($field);
    public function toArray();
}
