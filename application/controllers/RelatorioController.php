<?php

class RelatorioController extends Zend_Controller_Action {

    /**
     * O metodo init é sempre executado antes de tudo toda vez que a controller é acessada
     */
    public function init() {
        
    }

    public function indexAction() {
        //verifica se existe parametro menu para que no caso desabite o layout    
        if ($this->_getParam('menu')) {
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titulo = "Relatórios";
    }

    public function testeAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
               
        
    }       
}