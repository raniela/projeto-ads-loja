<?php

class VendaController extends Zend_Controller_Action
{
    /**
     *
     * @var Application_Model_DbTable_Usuario 
     */
    //private $usuarioDbTable;
    private $flashMessenger;

    public function init()
    {
        //$this->usuarioDbTable = new Application_Model_DbTable_Usuario();
        $this->clienteDbTable = new Application_Model_DbTable_Cliente();
        $this->vendaDbTable = new Application_Model_DbTable_Venda();
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        $this->logger = Zend_Registry::get('logger');
    }

    public function indexAction()
    {
        
        if($this->_getParam('menu')){
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titlePage = "Listagem de Vendas";
        
        $dadosAutoCompleteCliente = $this->clienteDbTable->getDataAutoCompleteCliente();               
        
        $this->view->dadosAutoCompleteCliente = $dadosAutoCompleteCliente;                
    }

    public function gridAction()
    {

        $this->getHelper('layout')->disableLayout();
        $params['nome'] = $this->_helper->util->urldecodeGet($this->_getParam('nome'));
        $params['tipo_pagamento'] = $this->_getParam('tipo_pagamento');
        $params['forma_pagamento'] = $this->_getParam('forma_pagamento');
        
        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));
        
        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));                     
        
        $vendas = $this->vendaDbTable->getDataGrid($params);
                 
        $paginator = Zend_Paginator::factory($vendas);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        if ($this->_getParam('id_venda')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Venda";
            $id = $this->_getParam('id_venda');
            $venda = $this->vendaDbTable->getDataGrid(array('id_venda' => $id));
            
            
            $this->view->venda = $venda[0];
            $this->view->venda['data_venda'] = $this->_helper->util->reverseDate($this->view->venda['data_venda']);
            $this->view->venda['valor_total_venda'] = $this->_helper->util->floatToMoney($this->view->venda['valor_total_venda']);
            $this->view->venda['valor_desconto'] = $this->_helper->util->floatToMoney($this->view->venda['valor_desconto']);
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Vendas";
        }        
        $sessao = new Zend_Session_Namespace();
        if (isset($sessao->dados)) {
            $this->view->usuario = $sessao->dados;
            unset($sessao->dados);
        }
    }

    public function salvarAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        
        $venda = $this->getRequest()->getPost('venda');
        
        $venda['id_cliente'] = '1';   
        $venda['data_venda'] = $this->_helper->util->reverseDate($venda['data_venda']);
        $venda['valor_total_venda'] = $this->_helper->util->moneyToFloat($venda['valor_total_venda']); 
        $venda['valor_desconto'] = $this->_helper->util->moneyToFloat($venda['valor_desconto']); 
        unset($venda['nome']);                                

        try {           
            if ($this->_getParam('id_venda')) {
                $id = $this->_getParam('id_venda');
                $this->vendaDbTable->update($venda, "id_venda = {$id}");
            } else {
                $this->vendaDbTable->insert($venda);
            }

            $this->flashMessenger->addMessage('Salvo com sucesso!');
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/1/'
            );
        } catch (Exception $exc) {
            $json = array(
                'tipo' => 'erro',
                'msg' => "Erro errado!",
            );

            $this->logger->err($exc->getMessage());
        }                        
        
        echo Zend_Json::encode($json);
    }

    public function excluirAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $id = $this->getRequest()->getParam('id_venda');
            //$usuarioDbTable = new Application_Model_DbTable_Usuario();
            //$usuarioDbTable->delete("id_usuario = $id");
            $this->vendaDbTable->delete("id_venda = $id");
            
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Registro excluído com sucesso!',
            );

            echo Zend_Json::encode($json);
        } catch (Exception $exc) {
            $json = array(
                'tipo' => 'erro',
                'msg' => $exc->getMessage()
            );

            echo Zend_Json::encode($json);
        }
    }

}