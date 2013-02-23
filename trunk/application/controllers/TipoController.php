<?php

class TipoController extends Zend_Controller_Action
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

        $this->view->titlePage = "Listagem de Tipos de Mercadorias";
        
        $dadosAutoComplete = array();
        $tiposAC = array();
        //$subtipos = $this->usuarioDbTable->fetchAll(null, 'nome')->toArray();
        
        $tiposAC[0]['nome'] = "Teste";
        $tiposAC[1]['nome'] = "Nome";
        
        foreach ($tiposAC as $tipo){
            
            $dadosAutoComplete[] = $tipo['nome'];
            
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
        
        $subtipos = $select->query()->fetchAll();*/

        $tipos = array();
        
        $tipos[0]['tipo'] = "Teste";
        
        $tipos[1]['tipo'] = "Nome";
        
        $paginator = Zend_Paginator::factory($tipos);
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
            /*$this->view->titulo = "Cadastro de Subtipos";
        }*/
        $this->view->titulo = "Cadastro de Tipos de Mercadoria";
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
                'url' => '/index/tabs/dir/7/'
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