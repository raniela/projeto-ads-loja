<?php

class ClienteController extends Zend_Controller_Action {

    /**
     *
     * @var Application_Model_DbTable_Usuario 
     */
    private $flashMessenger;

    public function init() {
        $this->clienteDbTable = new Application_Model_DbTable_Cliente();
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        $this->logger = Zend_Registry::get('logger');
    }

    public function indexAction() {

        if ($this->_getParam('menu')) {
            $this->getHelper('layout')->disableLayout();
        }

        //envia o titulo da pagina para a view
        $this->view->titlePage = "Listagem de Clientes";

        $dadosAutoComplete = array();

        //busca todos os clientes e guarda num array
        $clientes = $this->clienteDbTable->fetchAll(null, 'nome')->toArray();

        //pega o nome de todos os clientes do array
        foreach ($clientes as $cliente) {

            $dadosAutoComplete[] = $cliente['nome'];
        }

        //envia para a view um array com o nome dos clientes para funcionar o auto complete
        $this->view->dadosAutoComplete = $dadosAutoComplete;
    }

    public function gridAction() {

        $this->getHelper('layout')->disableLayout();

        $nome = $this->_getParam('nome');
        $cpf = $this->_getParam('cpf');
        $rg = $this->_getParam('rg');

        $select = $this->clienteDbTable->select();
        if (!empty($nome)) {
            $select->where("nome LIKE ?", "%$nome%");
        }
        if (!empty($cpf)) {
            $select->where("documento LIKE ?", "$cpf");
        }
        if (!empty($rg)) {
            $select->where("rg LIKE ?", "$rg");
        }

        $select->order('nome');

        $clientes = $select->query()->fetchAll();

        $paginator = Zend_Paginator::factory($clientes);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction() {
        $this->getHelper('layout')->disableLayout();

        //se já tem id é edição, tem que mandar os dados desse id pra view
        if ($this->_getParam('id')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Usuario";
            $id = $this->_getParam('id');
            $cliente = $this->clienteDbTable->fetchRow("id_cliente = {$id}")->toArray();
            $this->view->cliente = $cliente;
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Clientes";
        }
        $this->view->titulo = "Cadastro de Clientes";
        /* $sessao = new Zend_Session_Namespace();
          if (isset($sessao->dados)) {
          $this->view->usuario = $sessao->dados;
          unset($sessao->dados);
          } */
    }

    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        /* if ($this->usuarioDbTable->verificaDb($id, $dados) == false) {
          $this->_helper->json->sendJson(array(
          'tipo' => 'erro',
          'msg' => 'Usuário já existente'
          ));
          } */

        try {

            $data = $this->getRequest()->getPost('c');

            if ($this->_getParam('id')) {
                $id = $this->_getParam('id');
                $this->clienteDbTable->update($data, "id_cliente = {$id}");
            } else {
                $this->clienteDbTable->insert($data);
            }

            //$this->flashMessenger->addMessage('Salvo com sucesso!');
            $this->_helper->json->sendJson(array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/2/'
            ));
        } catch (Exception $exc) {
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'msg' => "Erro!",
            ));

            $this->logger->err($exc->getMessage());
        }

        //echo Zend_Json::encode($json);
    }

    public function excluirAction() {

        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $id = $this->getRequest()->getParam('id');
            $clienteDbTable = new Application_Model_DbTable_Cliente();
            $clienteDbTable->delete("id_cliente = $id");

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