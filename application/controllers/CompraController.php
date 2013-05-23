<?php

class CompraController extends Zend_Controller_Action {

    /**
     * @author Raniela Carvalho
     * 
     */
    private $flashMessenger;

    public function init() {
        $this->fornecedorDbTable = new Application_Model_DbTable_Fornecedor();
        $this->compraDbTable = new Application_Model_DbTable_Compra();
        $this->itemcompraDbTable = new Application_Model_DbTable_Itemcompra();
        $this->mercadoriaDbTable = new Application_Model_DbTable_Mercadoria();
        $this->vendaDbTable = new Application_Model_DbTable_Venda();
        $this->adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->flashMessenger->getMessages();
        $this->logger = Zend_Registry::get('logger');
    }

    public function indexAction() {

        if ($this->_getParam('menu')) {
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titlePage = "Listagem de Compras";

        $dadosAutoComplete = array();
        $comprasAC = array();
        //$compras = $this->usuarioDbTable->fetchAll(null, 'nome')->toArray();

        $comprasAC[0]['nome'] = "Teste";
        $comprasAC[1]['nome'] = "Nome";

        foreach ($comprasAC as $compra) {

            $dadosAutoComplete[] = $compra['nome'];
        }

        $this->view->dadosAutoComplete = $dadosAutoComplete;
    }

    public function gridAction() {

        $this->getHelper('layout')->disableLayout();
        $params['razao_social'] = $this->_helper->util->urldecodeGet($this->_getParam('razao_social'));

        $params['num_nota_fiscal'] = $this->_getParam('num_nota_fiscal');

        $params['data_inicial'] = $this->_helper->util->urldecodeGet($this->_getParam('data_inicial'));
        $params['data_inicial'] = trim($this->_helper->util->reverseDate($params['data_inicial']));

        $params['data_final'] = $this->_helper->util->urldecodeGet($this->_getParam('data_final'));
        $params['data_final'] = trim($this->_helper->util->reverseDate($params['data_final']));

        $compras = $this->compraDbTable->getDataGrid($params);

        $paginator = Zend_Paginator::factory($compras);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setDefaultItemCountPerPage(5);
        $this->view->paginator = $paginator;
    }

    public function formularioAction() {
        $this->getHelper('layout')->disableLayout();

        /** Traz os cados do fornecedor para utilizar no autocomplete */
        $this->view->dataAutoCompleteFornecedor = $this->fornecedorDbTable->getDataAutoCompleteFornecedorFormulario();

        /** Possiveis itens que a compra possui */
        $this->view->itensCompra = array();

        //se já tem id é edição, tem que mandar os dados desse id pra view
        if ($this->_getParam('id')) {
            /**
             * Edição do registro
             */
            $this->view->titulo = "Edição de Compra";
            $id = $this->_getParam('id');
            //busca todos os campos da compra 
            $compra = $this->compraDbTable->getDataGrid(array('id_compra' => $id));

            //aqui ele manda pra view 
            $this->view->compra = $compra[0];

            //aqui ele esta editando a data_compra através de uma helper para padrao brasileiro e mandando pra view
            $this->view->compra['data'] = $this->_helper->util->reverseDate($this->view->compra['data']);

            //aqui ele está editando valor_total_compra com uma helper para money
            $this->view->compra['valor_total_nota'] = $this->_helper->util->floatToMoney($this->view->compra['valor_total_nota']);

            $itensCompra = $this->_helper->util->utf8Encode($this->itemcompraDbTable->getItensCompra($this->view->compra['id_compra']));

            //Zend_Debug::dump($itensCompra); die;
            //Recupera os itens da compra
            $this->view->itensCompra = $itensCompra;
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Compras";
        }
        $sessao = new Zend_Session_Namespace();
        if (isset($sessao->dados)) {
            $this->view->usuario = $sessao->dados;
            unset($sessao->dados);
        }
    }

    public function salvarAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        $compra = $this->getRequest()->getPost('compra');
        $itens = $this->getRequest()->getPost('item-compra');

        $compra['data'] = $this->_helper->util->reverseDate($compra['data']);
        $compra['valor_total_nota'] = $this->_helper->util->moneyToFloat($compra['valor_total_nota']);
        unset($compra['razao_social']);

        /* inicia a transação */
        $this->adapter->beginTransaction();
        try {
            if ($this->_getParam('id_compra')) {
                $id = $this->_getParam('id_compra');
                $this->compraDbTable->update($compra, "id_compra = {$id}");
            } else {
                $id = $this->compraDbTable->insert($compra);
            }

            $item = array();
            $item['id_compra'] = $id;

            //Deleta os itens anteriores para inserir os novos
            $itensAntigos = $this->itemcompraDbTable->fetchAll("id_compra = {$id}")->toArray();
            if (!empty($itensAntigos)) {
                foreach ($itensAntigos as $i) {
                    $mercadoria = $this->mercadoriaDbTable->fetchRow("id_mercadoria = {$i['id_mercadoria']}")->toArray();
                    $mercadoria['qtde_estoque'] -= $i['quantidade'];
                    $this->mercadoriaDbTable->update($mercadoria, "id_mercadoria = {$i['id_mercadoria']}");
                }
            }
            $this->itemcompraDbTable->delete("id_compra = $id");

            //Percorre os itens do formulario fazendo a inserção
            foreach ($itens as $i) {
                $item['id_mercadoria'] = $i['id_mercadoria'];
                $item['quantidade'] = $i['quantidade'];
                $item['valor_unitario'] = $this->_helper->util->moneyToFloat($i['valor_unitario']);
                $this->itemcompraDbTable->insert($item);

                $mercadoria = $this->mercadoriaDbTable->fetchRow("id_mercadoria = {$i['id_mercadoria']}")->toArray();
                $mercadoria['qtde_estoque'] += $i['quantidade'];
                $this->mercadoriaDbTable->update($mercadoria, "id_mercadoria = {$i['id_mercadoria']}");
            }

            /** commita */
            $this->adapter->commit();

            $this->flashMessenger->addMessage('Salvo com sucesso!');
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/4/'
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

    public function excluirAction() {

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