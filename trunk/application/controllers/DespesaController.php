<?php

class DespesaController extends Zend_Controller_Action
{
    /**
     *
     * @var Application_Model_DbTable_Usuario 
     */
    
    private $flashMessenger;

    public function init()
    {
        $this->despesaDbTable = new Application_Model_DbTable_Despesa();
        $this->parcelaDbTable = new Application_Model_DbTable_Parcela();
        $this->tipoDespesaDbTable = new Application_Model_DbTable_Tipodespesa();
        
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        $this->logger = Zend_Registry::get('logger');
        
        $this->view->dadosComboTipoDespesa = $this->_helper->util->utf8Encode($this->tipoDespesaDbTable->getDataCombo());
    }

    public function indexAction()
    {        
        if($this->_getParam('menu')){
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titlePage = "Listagem de Despesas";
        
        //variaveis para autocomplete
        $dadosAutoComplete = array();
        $despesaAC = array();
        
        //chama o metodo que busca todos as despesas do bd
        $despesaAC = $this->despesaDbTable->fetchAll(null, 'descricao')->toArray();                
        
        //passa a descrição da despesa para um array q será utilizado no autocompletar da pesquisa
        foreach ($despesaAC as $despesa){            
            $dadosAutoComplete[] = $this->_helper->util->utf8Encode($despesa['descricao']);            
        }        
        
        $this->view->dadosAutoComplete = $dadosAutoComplete;        
    }

    public function gridAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        //pega o nome ou qualquer coisa que o usuario digitar para buscar
        $params['descricao'] = $this->_helper->util->urldecodeGet($this->_getParam('nome'));
        $params['id_tipodespesa'] = $this->_getParam('id_tipodespesa');
        $params = $this->_helper->util->utf8Decode($params);
        
        //variavel que recebe os subtipos de mercadorias buscados
        $despesas = $this->despesaDbTable->getDataGrid($params);
        $despesas = $this->_helper->util->utf8Encode($despesas);
        
        $paginator = Zend_Paginator::factory($despesas);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction()
    {
        $this->getHelper('layout')->disableLayout();                
        
        /** Possiveis parcelas que a despesa possui */
        $this->view->parcelas = array();               
        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        if ($this->_getParam('id')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Venda";
            $id = $this->_getParam('id');
            
            //busca todos os campos da despesa 
            $despesa = $this->despesaDbTable->fetchRow("id_despesa = {$id}")->toArray();
            
            //aqui ele está editando valor_despesa com uma helper para money
            $despesa['valor_despesa'] = $this->_helper->util->floatToMoney($despesa['valor_despesa']);
                        
            //Recupera as parcelas da venda
            $this->view->parcelas = $this->parcelaDbTable->fetchAll("id_despesa = '{$id}'")->toArray();
                        
            $this->view->despesa = $this->_helper->util->utf8Encode($despesa);
            //print_r($this->view->despesa);
            //die();
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Despesas";
        }
    }

    public function salvarAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
                
        $despesa = $this->_helper->util->utf8Decode($this->getRequest()->getPost('despesa'));                                
        $despesa['valor_despesa'] = $this->_helper->util->moneyToFloat($despesa['valor_despesa']); 
                                       
        $parcelas = $this->getRequest()->getPost('parcelas');
        
        $id = null;
        if (!empty($despesa['id_despesa'])) {
            $id = $despesa['id_despesa'];
        }
        
        //Verifica a existência de alguma Despesa com os mesmos dados
        if ($this->despesaDbTable->verificaDb($id, $despesa) == false) {
             $this->_helper->json->sendJson(array(
                 'tipo' => 'erro',
                 'url' => '/index/tabs/dir/9/',
                 'msg' => 'Despesa já existente com esses dados, verifique!'
             ));
        }
        
        /* inicia a transação */
        $this->adpter->beginTransaction();
        try {           
            if (!empty($despesa['id_despesa'])) {
                $id = $despesa['id_despesa'];
                $this->despesaDbTable->update($despesa, "id_despesa = {$id}");
            } else {
                $id = $this->despesaDbTable->insert($despesa);
            }
            
            //Apaga as parcelas anteriores para inserir novamente as vindas do formulário
            $this->parcelaDbTable->delete("id_despesa = $id"); 
            
            //Salva as parcelas da despesa                            
            $parcela['id_despesa'] = $id;

            foreach ($parcelas as $p) {
                $parcela['data_vencimento'] = $this->_helper->util->reverseDate($p['data_vencimento']);
                $parcela['valor_total_parcela'] = $this->_helper->util->moneyToFloat($p['valor_total_parcela']);                
                $this->parcelaDbTable->insert($parcela);
            }                                                                                                       
            
            /** commita */
            $this->adpter->commit();
            
            $this->flashMessenger->addMessage('Salvo com sucesso!');
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/9/'
            );
        } catch (Exception $exc) {
            /** executa rollback */
            $this->adpter->rollBack();
            
            $json = array(
                'tipo' => 'erro',
                'msg' => $exc->getMessage(),
                'url' => '/index/tabs/dir/9/'
            );

            $this->logger->err($exc->getMessage());
        }                        
        
        echo Zend_Json::encode($json);
    }

    public function excluirAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        
        /* inicia a transação */
        $this->adpter->beginTransaction();
        try {
            $id = $this->getRequest()->getParam('id');            
                                    
            $this->parcelaDbTable->delete("id_despesa = $id");            
            
            $this->despesaDbTable->delete("id_despesa = $id");
            
            /** commita */
            $this->adpter->commit();
            
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Registro excluído com sucesso!',
            );

            echo Zend_Json::encode($json);
        } catch (Exception $exc) {
            /** executa rollback */
            $this->adpter->rollBack();
            
            $json = array(
                'tipo' => 'erro',
                'msg' => $exc->getMessage()
            );

            echo Zend_Json::encode($json);
        }
    }

}