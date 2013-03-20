<?php

class FornecedorController extends Zend_Controller_Action
{
    /**
     *
     * @var Application_Model_DbTable_Usuario 
     */
    private $fornecedorDbTable;
    

    public function init()
    {
        $this->fornecedorDbTable = new Application_Model_DbTable_Fornecedor();
        $this->logger = Zend_Registry::get('logger');
    }

    public function indexAction()
    {
        
        if($this->_getParam('menu')){
            $this->getHelper('layout')->disableLayout();
        }
        
        $fornecedorAC = array();
        $dadosAutoComplete = array();
        
        $this->view->titlePage = "Listagem de Fornecedores";
        $fornecedorAC = $this->fornecedorDbTable->fetchAll(null, 'razao_social')->toArray();
        foreach ($fornecedorAC as $tipo) {
            $dadosAutoComplete[] = $tipo['razao_social'];
        }
        
        $this->view->dadosAutoCompleteForn = $dadosAutoComplete;
        
                
        
        
    }

    public function gridAction()
    {
        $this->getHelper('layout')->disableLayout();

        $razaoSocial = $this->_getParam('nome');
        
        $select =$this->fornecedorDbTable->select();
        if (!empty($razaoSocial)) {            
            $select->where("razao_social LIKE ?", "%$razaoSocial%");
        }
        $select->order('razao_social');
        
        $fornecedores = $select->query()->fetchAll();

        $paginator = Zend_Paginator::factory($fornecedores);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        if ($this->_getParam('id')) {
            $titulo = "Edição de Fornecedor";
            $this->view->fornecedor = $this->fornecedorDbTable->fetchRow("id_fornecedor = {$this->_getParam('id')}")->toArray();
        } else {          
            $titulo = "Cadastro de Fornecedor";
        }
        $this->view->titulo = $titulo;
    }

    public function salvarAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();
        //print_r($this->getRequest()->getPost('f'));die;
        //vou fazer outra hora
       if (1==0 )//$this->fornecedorDbTable->verificaCnpj() == false 
       {
            $this->_helper->json->sendJson(array(
                'tipo' => 'erro',
                'msg' => 'Cnpj já existente'
            ));
        }
            
        try {
            if (($this->_getParam('id'))) {
                $this->fornecedorDbTable->update($this->getRequest()->getPost('f'), "id_fornecedor = {$this->_getParam('id')}");
            } else {
                $this->fornecedorDbTable->insert($this->getRequest()->getPost('f'));
            }            
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/2/'
            );
        } catch (Exception $exc) {
            $json = array(
                'tipo' => 'erro',
                'msg' => "Erro errado!".$exc,
            );

            //$this->logger->err($exc->getMessage());
        }
          
        echo Zend_Json::encode($json);
    }

    public function excluirAction()
    {

        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try { 
            $this->fornecedorDbTable->delete("id_fornecedor = '{$this->getRequest()->getParam('id')}'");

            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Registro excluído com sucesso!',
            );            
        } catch (Exception $exc) {
            $json = array(
                'tipo' => 'erro',
                'msg' => $exc->getMessage()
            );            
        }
        echo Zend_Json::encode($json);
    }

}