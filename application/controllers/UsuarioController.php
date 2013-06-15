<?php

class UsuarioController extends Zend_Controller_Action
{
    /**
     *
     * @var Application_Model_DbTable_Usuario 
     */
    //private $usuarioDbTable;
    private $flashMessenger;

    public function init()
    {        
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        $this->logger = Zend_Registry::get('logger');
        $this->usuarioDbTable = new Application_Model_DbTable_Usuario();
    }

    public function indexAction()
    {
        
        if($this->_getParam('menu')){
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titlePage = "Listagem de Usuarios";
        
        $dadosAutoComplete = array();
        
        $usuarios = $this->usuarioDbTable->fetchAll(null,"login")->toArray();
        
        foreach ($usuarios as $usuario){
            
            $dadosAutoComplete[] = $usuario['login'];
            
        }
        
        $this->view->dadosAutoComplete = $dadosAutoComplete;                
    }

    public function gridAction()
    {

        $this->getHelper('layout')->disableLayout();

        $params['login'] = $this->_helper->util->urldecodeGet($this->_getParam('login'));
        $params['tipo_usuario'] = $this->_getParam('tipo_usuario');
                
        $usuarios = $this->_helper->util->utf8Encode($this->usuarioDbTable->getDataGrid($params));        
        
        $paginator = Zend_Paginator::factory($usuarios);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        if ($this->_getParam('id_usuario')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Usuario";
            $id = $this->_getParam('id_usuario');
            $usuario = $this->usuarioDbTable->fetchRow("id_usuario = {$id}")->toArray();
            $this->view->usuario = $this->_helper->util->utf8Encode($usuario);
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Usuarios";
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

        $usuario = $this->_helper->util->utf8Decode($this->getRequest()->getPost('usuario'));

        $id = null;
        if ($this->_getParam('id_usuario')) {
            $id = $this->_getParam('id_usuario');
        }

       if ($this->usuarioDbTable->verificaDb($id, $usuario) == false) {
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'url' => '/index/tabs/dir/2/',
                'msg' => 'Usuário já existente com esse Login'
            ));
        }

        try {          
            if ($this->_getParam('id_usuario')) {
                $id = $this->_getParam('id_usuario');
                $this->usuarioDbTable->update($usuario, "id_usuario = {$id}");
            } else {
                $this->usuarioDbTable->insert($usuario);
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
                'msg' => "Ocorreu um erro ao tentar executar a operacao, contate o administrador!",
            );

            $this->logger->err($exc->getMessage());
        }
        
        
        $this->flashMessenger->addMessage('Salvo com sucesso!');
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/2/'
            );
        
        echo Zend_Json::encode($json);
    }

    public function excluirAction()
    {

        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            $id = $this->getRequest()->getParam('id_usuario');
            
            $usuario = Zend_Auth::getInstance()->getIdentity();
            
            if($id == $usuario->id_usuario) {
                $json = array(
                    'tipo' => 'sucesso',
                    'msg' => 'Atenção, não é possível excluir seu próprio usuário!',
                );
            } else {
                $this->usuarioDbTable->delete("id_usuario = $id");

                $json = array(
                    'tipo' => 'sucesso',
                    'msg' => 'Registro excluído com sucesso!',
                );
            }
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