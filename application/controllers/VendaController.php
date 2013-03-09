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
        
        /*$vendasAC = array();
        
        $vendasAC[0]['cliente'] = "Nome Cliente";
        $vendasAC[1]['cliente'] = "Cliente Exemplo";
        
        foreach ($vendasAC as $venda){
            
            $dadosAutoComplete[] = $venda['cliente'];
            
        }*/
        
        $this->view->dadosAutoCompleteCliente = $dadosAutoCompleteCliente;                
    }

    public function gridAction()
    {

        $this->getHelper('layout')->disableLayout();
        $params['nome'] = $this->_helper->util->urldecodeGet($this->_getParam('nome'));
        $params['tipo_pagamento'] = $this->_getParam('tipo_pagamento');
        $params['forma_pagamento'] = $this->_getParam('forma_pagamento');
        
        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = $this->_helper->util->reverseDate($params['data_inicial']);
        
        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = $this->_helper->util->reverseDate($params['data_final']);
                
        /*$nome = $this->_getParam('nome');
        
        $select =$this->usuarioDbTable->select();
        if (!empty($nome)) {            
            $select->where("nome LIKE ?", "%$nome%");
        }
        $select->order('nome');
        
        $vendas = $select->query()->fetchAll();*/

        $vendas = array();
        
        $vendas[0]['data'] = "19/02/2013";
        $vendas[0]['cliente'] = "Nome Cliente";
        $vendas[0]['valor'] = "R$ 67,00";
        
        $vendas[1]['data'] = "18/02/2013";
        $vendas[1]['cliente'] = "Cliente Exemplo";
        $vendas[1]['valor'] = "R$ 45,00";
        
        $paginator = Zend_Paginator::factory($vendas);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        /*if ($this->_getParam('id')) {
            /**
             * Edição do registro
             */
            /*$this->view->titulo = "Edição de Usuario";
            $id = $this->_getParam('id');
            $usuario = $this->usuarioDbTable->fetchRow("id_usuario = {$id}")->toArray();
            $this->view->usuario = $usuario;*/
        /*} else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            /*$this->view->titulo = "Cadastro de Usuarios";
        }*/
        $this->view->titulo = "Cadastro de Venda";
        /*$sessao = new Zend_Session_Namespace();
        if (isset($sessao->dados)) {
            $this->view->usuario = $sessao->dados;
            unset($sessao->dados);
        }*/
    }

    public function salvarAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        /*$dados = $this->getRequest()->getPost('u');

        $id = null;
        if ($this->_getParam('id')) {
            $id = $this->_getParam('id');
        }*/

       /*if ($this->usuarioDbTable->verificaDb($id, $dados) == false) {
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'msg' => 'Usuário já existente'
            ));
        }*/

        /*try {

            $data = $this->getRequest()->getPost('u');


            if ($this->_getParam('id')) {
                $id = $this->_getParam('id');
                $this->usuarioDbTable->update($data, "id_usuario = {$id}");
            } else {
                $this->usuarioDbTable->insert($data);
            }

            $this->flashMessenger->addMessage('Salvo com sucesso!');
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/2/'
            );
        } catch (Exception $exc) {
            $json = array(
                'tipo' => 'erro',
                'msg' => "Erro errado!",
            );

            $this->logger->err($exc->getMessage());
        }*/
        
        
        $this->flashMessenger->addMessage('Salvo com sucesso!');
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/1/'
            );
        
        echo Zend_Json::encode($json);
    }

    public function excluirAction()
    {

        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $id = $this->getRequest()->getParam('id');
            $usuarioDbTable = new Application_Model_DbTable_Usuario();
            $usuarioDbTable->delete("id_usuario = $id");

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