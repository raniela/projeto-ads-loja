<?php

class RelatorioController extends Zend_Controller_Action {

    /**
     * O metodo init é sempre executado antes de tudo toda vez que a controller é acessada
     */
    public function init() {
        $this->tipoMercadoriaDbTable = new Application_Model_DbTable_Tipomercadoria();
        $this->mercadoriaDbTable = new Application_Model_DbTable_Mercadoria();
        
        $this->vendaDbTable = new Application_Model_DbTable_Venda();        
        $this->despesaDbTable = new Application_Model_DbTable_Despesa();        
        
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
    
    public function relatorioMovimentacaoCaixaAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
               
        $params = $this->_getAllParams();
        
        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));
        
        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));  
                
        
        $dataRelatorioVendas = $this->vendaDbTable->getDataToRelatorioMovimentacaoCaixa($params);
        $dataRelatorioDespesas = $this->despesaDbTable->getDataToRelatorioMovimentacaoCaixa($params);
        
//        echo "<pre>";
//        print_r($dataRelatorioDespesas);
//        
//        die();
        
        $this->view->dataVendas = $dataRelatorioVendas;
        $this->view->dataDespesas = $dataRelatorioDespesas;
        $this->view->params = $params;
    }
    
    public function relatorioVendasAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
               
        $params = $this->_getAllParams();
        
        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));
        
        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));  
                        
        $dataRelatorioVendas = $this->vendaDbTable->getDataToRelatorioVendas($params);                       
        
        $this->view->dataVendas = $dataRelatorioVendas;        
        $this->view->params = $params;
    }
}