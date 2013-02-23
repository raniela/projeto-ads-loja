<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ValidarLogin
 *
 * @author
 */
class Application_Plugin_ValidarLogin extends Zend_Controller_Plugin_Abstract
{
    //é semrpre disparado qdo executada uma action
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        //obj auth
        $auth = Zend_Auth::getInstance();
        //se não é a controller de login
        if ($request->getControllerName() != 'index') {
            //se o usuário não está logado
            if (!$auth->hasIdentity()) {
                //renderizo a controller de login e a action index
                $request->setControllerName('index');
                $request->setActionName('index');
            }
        }
    }

}

