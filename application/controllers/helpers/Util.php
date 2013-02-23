<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Util
 *
 * @author raniela.carvalho
 */
class Zend_Controller_Action_Helper_Util extends Zend_Controller_Action_Helper_Abstract {


    public function formatarDataParaInserir($data)
    {
        return implode('-', array_reverse(explode("/", $data)));
    }
}
