<?php

class RelatorioController extends Zend_Controller_Action {

    /**
     * O metodo init é sempre executado antes de tudo toda vez que a controller é acessada
     */
    public function init() {
        $this->tipoMercadoriaDbTable = new Application_Model_DbTable_Tipomercadoria();
        $this->mercadoriaDbTable = new Application_Model_DbTable_Mercadoria();
        
        $this->view->dadosComboTipoMercadoria = $this->_helper->util->utf8Encode($this->tipoMercadoriaDbTable->getDataCombo());        
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
     public function relatorioMercadoriasAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
               
        $params = $this->_getAllParams();
        
        $dataRelatorioMercadorias = $this->mercadoriaDbTable->getDataToRelatorioMercadorias($params);
        
        /*echo "<pre>";
        print_r($dataRelatorioMercadorias);
        
        die();*/
        
        $this->view->dataMercadorias = $dataRelatorioMercadorias;
    }
}