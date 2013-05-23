<?php

class MercadoriaController extends Zend_Controller_Action
{
    /**
     *
     * @var Application_Model_DbTable_Usuario 
     */
    //private $usuarioDbTable;
    private $flashMessenger;

    public function init()
    {
        $this->mercadoriaDbTable = new Application_Model_DbTable_Mercadoria();
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

        $this->view->titlePage = "Listagem de Mercadorias";
        
        $dadosAutoComplete = array();
        $mercadoriasAC = array();
        
        //chama o metodo que busca todos as Mercadorias do bd
        $mercadoriasAC = $this->mercadoriaDbTable->fetchAll(null, 'descricao')->toArray();
                
        
        //passa a descrição da mercadoria para um array q será utilizado no autocompletar da pesquisa
        foreach ($mercadoriasAC as $mercadoria){            
            $dadosAutoComplete[] = $this->_helper->util->utf8Encode($mercadoria['descricao']);            
        }
        
        $this->view->dadosAutoComplete = $dadosAutoComplete;                        
    }

    public function gridAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        //pega o nome ou qualquer coisa que o usuario digitar para buscar
        $params['descricao'] = $this->_helper->util->urldecodeGet($this->_getParam('nome'));
        $params['id_tipomercadoria'] = $this->_getParam('id_tipomercadoria');
        $params['id_subtipomercadoria'] = $this->_getParam('id_subtipomercadoria');
        $params = $this->_helper->util->utf8Decode($params);
        
        //variavel que recebe as mercadorias buscadas
        $mercadorias = $this->mercadoriaDbTable->getDataGrid($params);
        $mercadorias = $this->_helper->util->utf8Encode($mercadorias);
        
        $paginator = Zend_Paginator::factory($mercadorias);
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
            $this->view->titulo = "Edição de Mercadoria";
            $id = $this->_getParam('id');
            
            $mercadoria = $this->mercadoriaDbTable->fetchRow("id_mercadoria = {$id}")->toArray();
            $mercadoria['preco_venda_unitario'] = $this->_helper->util->floatToMoney($mercadoria['preco_venda_unitario']);
            
            //Busca o tipo de mercadoria conforme o subtipo 
            $idTipoMercadoria = $this->subTipoMercadoriaDbTable->getIdTipoMercadoria($mercadoria['id_subtipomercadoria']);
            $mercadoria['id_tipomercadoria'] = $idTipoMercadoria; 
            
            
            //Monta o combo de subtipo para o tipo daquela mercadoria
            $this->view->dadosComboSubTipoMercadoria = $this->_helper->util->utf8Encode($this->subTipoMercadoriaDbTable->getDataCombo(array('id_tipomercadoria' => $idTipoMercadoria)));
            
            $this->view->mercadoria = $this->_helper->util->utf8Encode($mercadoria);
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Mercadoria";
            
            $this->view->dadosComboSubTipoMercadoria = array('' => '');
        }
    }

    public function salvarAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        $dados = $this->_helper->util->utf8Decode($this->getRequest()->getPost('mercadoria'));
        //Remove o id_mercadoria que não faz parte da tabela mercadoria
        unset($dados['id_tipomercadoria']);
        $dados['preco_venda_unitario'] = $this->_helper->util->moneyToFloat($dados['preco_venda_unitario']);
        
        $id = null;
        if (!empty($dados['id_mercadoria'])) {
            $id = $dados['id_mercadoria'];
        }
        
        //Verifica a existência de algums Subtipo de Mercadoria com os mesmos dados
        if ($this->mercadoriaDbTable->verificaDb($id, $dados) == false) {
             $this->_helper->json->sendJson(array(
                 'tipo' => 'erro',
                 'url' => '/index/tabs/dir/7/',
                 'msg' => 'Mercadoria já existente com esses dados, verifique!'
             ));
        }
        
        try {           
            //verifica se o id existe
            if (!empty($dados['id_mercadoria'])) {
                //passa o valor do id tipo de despesa para a variavel id
                $id = $dados['id_mercadoria'];

                //atualiza o subtipo da mercadoria que está vindo pelo post
                $this->mercadoriaDbTable->update($dados, "id_mercadoria = {$id}");
            } else {
                //insere um novo subtipo de mercadoria, ja que não temos um id para editar
                $this->mercadoriaDbTable->insert($dados);
            }

            //retorna a mensagem de sucesso, o tipo da mensagem e a url para o usuario em javascript
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/7/'
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
            $this->mercadoriaDbTable->delete("id_mercadoria = $id");

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
    
    //Utilizada para buscar as mercadorias da tela de Venda
    public function pesquisarMercadoriaAction() {
        try {
            $this->getHelper('layout')->disableLayout();
            
            $params = $this->_getAllParams();
            $params = $this->_helper->util->urldecodeGet($params);                        
            $params = $this->_helper->util->utf8Decode($params);                                                                        
            //$this->view->dataGrid = $this->view->utf8($this->mercadoriaDbTable->getDataGrid($params))->encode();
            $this->view->dataGrid = $this->_helper->util->utf8Encode($this->mercadoriaDbTable->getDataGrid($params));
                                    
        } catch (Exception $E) {
            
            die('ERRO|Ocorreu um erro ao tentar executar a operação. Tente novamente. Caso persista, contate o administrador do sistema.');
        }
    }

    //Utilizada para buscar as mercadorias da tela de Venda
    public function pesquisarMercadoriaFornecedorAction() {
        try {
            $this->getHelper('layout')->disableLayout();
            
            $params = $this->_getAllParams();
            $params = $this->_helper->util->urldecodeGet($params);                        
            $params = $this->_helper->util->utf8Decode($params);                                                                        
            //$this->view->dataGrid = $this->view->utf8($this->mercadoriaDbTable->getDataGrid($params))->encode();
            $this->view->dataGrid = $this->_helper->util->utf8Encode($this->mercadoriaDbTable->getDataGrid($params));
                                    
        } catch (Exception $E) {
            
            die('ERRO|Ocorreu um erro ao tentar executar a operação. Tente novamente. Caso persista, contate o administrador do sistema.');
        }
    }
    
}