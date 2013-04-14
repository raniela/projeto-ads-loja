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
        $this->itemVendaDbTable = new Application_Model_DbTable_Itemvenda();
        $this->mercadoriaDbTable = new Application_Model_DbTable_Mercadoria();
        $this->duplicataDbTable = new Application_Model_DbTable_Duplicata();
        
        $this->adpter = Zend_Db_Table_Abstract::getDefaultAdapter();
        
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
        
        /** Traz os cados do cliente para utilizar no autocomplete */
        $this->view->dataAutoCompleteCliente = $this->clienteDbTable->getDataAutoCompleteClienteFormulario();
        
        /** Possiveis itens que a venda possui */
        $this->view->itensVenda = array();
        
        //se já tem id é edição, tem que mandar os dados desse id pra view
        if ($this->_getParam('id_venda')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Venda";
            $id = $this->_getParam('id_venda');
            //busca todos os campos da venda 
            $venda = $this->vendaDbTable->getDataGrid(array('id_venda' => $id));
            
            //aqui ele manda pra view 
            $this->view->venda = $venda[0];
            //aqui ele esta editando a data_venda através de uma helper para padrao brasileiro e mandando pra view
            $this->view->venda['data_venda'] = $this->_helper->util->reverseDate($this->view->venda['data_venda']);
            
            //aqui ele está editando valor_total_venda com uma helper para money
            $this->view->venda['valor_total_venda'] = $this->_helper->util->floatToMoney($this->view->venda['valor_total_venda']);
            
            //aqui ele está editando valor_total_venda com uma helper para money
            $this->view->venda['valor_desconto'] = $this->_helper->util->floatToMoney($this->view->venda['valor_desconto']);
            
            $this->view->itensVenda = $this->itemVendaDbTable->getItensVenda($this->view->venda['id_venda']);
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
        $itens = $this->getRequest()->getPost('item-venda');
                           
        $venda['data_venda'] = $this->_helper->util->reverseDate($venda['data_venda']);
        $venda['valor_total_venda'] = $this->_helper->util->moneyToFloat($venda['valor_total_venda']); 
        $venda['valor_desconto'] = $this->_helper->util->moneyToFloat($venda['valor_desconto']); 
        unset($venda['nome']);                                
        
        /* inicia a transação */
        $this->adpter->beginTransaction();
        try {           
            if ($this->_getParam('id_venda')) {
                $id = $this->_getParam('id_venda');
                $this->vendaDbTable->update($venda, "id_venda = {$id}");
            } else {
                $id = $this->vendaDbTable->insert($venda);
            }

            $item = array();
            $item['id_venda'] = $id;
            
            //Deleta os itens anteriores para inserir os novos
            $itensAntigos = $this->itemVendaDbTable->fetchAll("id_venda = {$id}")->toArray();
            if(!empty($itensAntigos)) {
                foreach($itensAntigos as $i) {
                    $mercadoria = $this->mercadoriaDbTable->fetchRow("id_mercadoria = {$i['id_mercadoria']}")->toArray();
                    $mercadoria['qtde_estoque'] += $i['quantidade'];
                    $this->mercadoriaDbTable->update($mercadoria, "id_mercadoria = {$i['id_mercadoria']}");                
                }
            }
            $this->itemVendaDbTable->delete("id_venda = $id");
            
            //Percorre os itens do formulario fazendo a inserção
            foreach($itens as $i) {
                $item['id_mercadoria'] = $i['id_mercadoria'];
                $item['quantidade'] = $i['quantidade'];
                $item['valor_unitario'] = $this->_helper->util->moneyToFloat($i['valor_unitario']);
                $this->itemVendaDbTable->insert($item);
                
                $mercadoria = $this->mercadoriaDbTable->fetchRow("id_mercadoria = {$i['id_mercadoria']}")->toArray();
                $mercadoria['qtde_estoque'] -= $i['quantidade'];
                $this->mercadoriaDbTable->update($mercadoria, "id_mercadoria = {$i['id_mercadoria']}"); 
            }
            
            $this->duplicataDbTable->delete("id_venda = $id"); 
            
            //Salva as duplicatas do pagamento "a vista"
            if($venda['tipo_pagamento'] == 'V') {
                if($venda['forma_pagamento'] == 'D') {
                    $duplicata['id_venda'] = $id;
                    $duplicata['data_vencimento'] = $venda['data_venda'];
                    $duplicata['data_pagamento'] = $venda['data_venda'];
                    $duplicata['valor_total'] = $venda['valor_total_venda'];
                    $duplicata['valor_pago'] = $venda['valor_total_venda'] - $venda['valor_desconto'];
                    $this->duplicataDbTable->insert($duplicata);
                }
                
                if($venda['forma_pagamento'] == 'CD') {
                    $duplicata['id_venda'] = $id;
                    $duplicata['data_vencimento'] = date('Y-m-d', strtotime($venda['data_venda'] . " +1 days"));
                    $duplicata['data_pagamento'] = date('Y-m-d', strtotime($venda['data_venda'] . " +1 days"));
                    $duplicata['valor_total'] = $venda['valor_total_venda'];
                    $duplicata['valor_pago'] = 0.9733 * ($venda['valor_total_venda'] - $venda['valor_desconto']);
                    $this->duplicataDbTable->insert($duplicata);
                }
                
                if($venda['forma_pagamento'] == 'CC') {
                    $duplicata['id_venda'] = $id;
                    $duplicata['data_vencimento'] = date('Y-m-d', strtotime($venda['data_venda'] . " +30 days"));
                    $duplicata['data_pagamento'] = date('Y-m-d', strtotime($venda['data_venda'] . " +30 days"));
                    $duplicata['valor_total'] = $venda['valor_total_venda'];
                    $duplicata['valor_pago'] = 0.965 * ($venda['valor_total_venda'] - $venda['valor_desconto']);
                    $this->duplicataDbTable->insert($duplicata);
                }
            }                        
            
            /** commita */
            $this->adpter->commit();
            
            $this->flashMessenger->addMessage('Salvo com sucesso!');
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/0/'
            );
        } catch (Exception $exc) {
            /** executa rollback */
            $this->adpter->rollBack();
            
            $json = array(
                'tipo' => 'erro',
                'msg' => "Ocorreu um erro ao tentar executar a operacao, contate o administrador!",
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
            $id = $this->getRequest()->getParam('id_venda');
            //$usuarioDbTable = new Application_Model_DbTable_Usuario();
            //$usuarioDbTable->delete("id_usuario = $id");
                        
            $itens = $this->itemVendaDbTable->fetchAll("id_venda = {$id}")->toArray();
            if(!empty($itens)) {
                foreach($itens as $i) {
                    $mercadoria = $this->mercadoriaDbTable->fetchRow("id_mercadoria = {$i['id_mercadoria']}")->toArray();
                    $mercadoria['qtde_estoque'] += $i['quantidade'];
                    $this->mercadoriaDbTable->update($mercadoria, "id_mercadoria = {$i['id_mercadoria']}");                
                }
            }
            $this->itemVendaDbTable->delete("id_venda = $id");
            $this->duplicataDbTable->delete("id_venda = $id");
            
            $this->vendaDbTable->delete("id_venda = $id");
            
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