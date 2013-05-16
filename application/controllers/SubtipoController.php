<?php

class SubtipoController extends Zend_Controller_Action
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
        
        $this->tipoMercadoriaDbTable = new Application_Model_DbTable_Tipomercadoria();
        $this->subTipoMercadoriaDbTable = new Application_Model_DbTable_Subtipomercadoria();
        
        $this->view->dadosComboTipoMercadoria = $this->_helper->util->utf8Encode($this->tipoMercadoriaDbTable->getDataCombo());
    }

    public function indexAction()
    {
        
        if($this->_getParam('menu')){
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titlePage = "Listagem de Subtipo de Mercadorias";
        
        //variaveis para autocomplete
        $dadosAutoComplete = array();
        $subTiposMercadoriaAC = array();
        
        //chama o metodo que busca todos os tiposMercadoria do bd
        $subTiposMercadoriaAC = $this->subTipoMercadoriaDbTable->fetchAll(null, 'descricao')->toArray();                
        
        //passa a descrição do subtipo de mercadoria para um array q será utilizado no autocompletar da pesquisa
        foreach ($subTiposMercadoriaAC as $subtipo){            
            $dadosAutoComplete[] = $this->_helper->util->utf8Encode($subtipo['descricao']);            
        }
        //print_r($dadosAutoComplete);
        //die();
        $this->view->dadosAutoComplete = $dadosAutoComplete;        
    }

    public function gridAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        //pega o nome ou qualquer coisa que o usuario digitar para buscar
        $params['descricao'] = $this->_helper->util->urldecodeGet($this->_getParam('nome'));
        $params['id_tipomercadoria'] = $this->_getParam('id_tipomercadoria');
        $params = $this->_helper->util->utf8Decode($params);
        
        //variavel que recebe os subtipos de mercadorias buscados
        $subtipos = $this->subTipoMercadoriaDbTable->getDataGrid($params);
        $subtipos = $this->_helper->util->utf8Encode($subtipos);
        
        $paginator = Zend_Paginator::factory($subtipos);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        if ($this->_getParam('id')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Subtipos de Mercadoria";
            $id = $this->_getParam('id');
            
            $subtipo = $this->subTipoMercadoriaDbTable->fetchRow("id_subtipomercadoria = {$id}")->toArray();
            
            $this->view->subtipo = $this->_helper->util->utf8Encode($subtipo);
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Subtipos de Mercadoria";
        }
                
    }

    public function salvarAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        $dados = $this->_helper->util->utf8Decode($this->getRequest()->getPost('subtipo'));

        $id = null;
        if (!empty($dados['id_subtipomercadoria'])) {
            $id = $dados['id_subtipomercadoria'];
        }
        
        //Verifica a existência de algums Subtipo de Mercadoria com os mesmos dados
        if ($this->subTipoMercadoriaDbTable->verificaDb($id, $dados) == false) {
             $this->_helper->json->sendJson(array(
                 'tipo' => 'erro',
                 'url' => '/index/tabs/dir/5/',
                 'msg' => 'Subtipo de Mercadoria já existente com esses dados, verifique!'
             ));
        }
        
        try {           
            //verifica se o id existe
            if (!empty($dados['id_subtipomercadoria'])) {
                //passa o valor do id tipo de despesa para a variavel id
                $id = $dados['id_subtipomercadoria'];

                //atualiza o subtipo da mercadoria que está vindo pelo post
                $this->subTipoMercadoriaDbTable->update($dados, "id_subtipomercadoria = {$id}");
            } else {
                //insere um novo subtipo de mercadoria, ja que não temos um id para editar
                $this->subTipoMercadoriaDbTable->insert($dados);
            }

            //retorna a mensagem de sucesso, o tipo da mensagem e a url para o usuario em javascript
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/5/'
            );
        } catch (Exception $exc) {
            //retorna a mensagem de erro para o usuario
            $json = array(
                'tipo' => 'erro',
                'msg' => $exc->getMessage()
            );            
        }                        
        
        echo Zend_Json::encode($json);
    }

    public function excluirAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            //Recebe o id que é transmitido do link de exclusão
            $id = $this->getRequest()->getParam('id');
            
            //Realiaza a exclusão
            $this->subTipoMercadoriaDbTable->delete("id_subtipomercadoria = $id");

            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Registro excluído com sucesso!',
            );

            echo Zend_Json::encode($json);
        } catch (Exception $exc) {
            //mensagem que retorna no json para javascript que no caso é de erro 
            if($exc->getCode() == 23000) {
                $json = array(
                    'tipo' => 'erro',
                    'msg' => 'Esse registro possui vínculos e não pode ser excluído'
                );
            } else {
                $json = array(
                    'tipo' => 'erro',
                    'msg' => $exc->getMessage()
                );
            }

            echo Zend_Json::encode($json);
        }
    }

}