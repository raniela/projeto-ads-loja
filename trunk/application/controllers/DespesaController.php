<?php

class DespesaController extends Zend_Controller_Action
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
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        $this->logger = Zend_Registry::get('logger');
    }

    public function indexAction()
    {
        
        if($this->_getParam('menu')){
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titlePage = "Listagem de Despesas";
        
        $dadosAutoComplete = array();
        $despesasAC = array();
        //$despesas = $this->usuarioDbTable->fetchAll(null, 'nome')->toArray();
        
        $despesasAC[0]['nome'] = "Teste";
        $despesasAC[1]['nome'] = "Nome";
        
        foreach ($despesasAC as $despesa){
            
            $dadosAutoComplete[] = $despesa['nome'];
            
        }
        
        $this->view->dadosAutoComplete = $dadosAutoComplete;
        
    }

    public function gridAction()
    {

        $this->getHelper('layout')->disableLayout();

        /*$nome = $this->_getParam('nome');
        
        $select =$this->usuarioDbTable->select();
        if (!empty($nome)) {            
            $select->where("nome LIKE ?", "%$nome%");
        }
        $select->order('nome');
        
        $despesas = $select->query()->fetchAll();*/

        $despesas = array();
        
        $despesas[0]['descricao'] = "Teste";
        $despesas[0]['valor'] = "R$ 67,40";
        
        $despesas[1]['descricao'] = "Exemplo";
        $despesas[1]['valor'] = "R$ 78,90";
        
        $paginator = Zend_Paginator::factory($despesas);
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
            /*$this->view->titulo = "Cadastro de Compras";
        }*/
        $this->view->titulo = "Cadastro de Despesas";
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
                'url' => '/index/tabs/dir/9/'
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