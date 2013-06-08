<?php

class TipoDespesaController extends Zend_Controller_Action {

    /**
     * O metodo init é sempre executado antes de tudo toda vez que a controller é acessada
     */
    public function init() {
        
    }

    public function indexAction() {
        //verifica se existe parametro menu para que no caso desabite o layout    
        if ($this->_getParam('menu')) {
            $this->getHelper('layout')->disableLayout();
        }

        //manda pra view o titulo
        $this->view->titlePage = "Listagem de Tipos de Despesa";

        //variaveis para autocomplete
        $dadosAutoComplete = array();
        $tipoDespesaAC = array();

        //instancia a classe da model tipoDespesa 
        $tipoDespesaDbTable = new Application_Model_DbTable_TipoDespesa();

        //chama o metodo que busca todos os tiposDespesas do bd
        $tipoDespesaAC = $tipoDespesaDbTable->fetchAll(null, 'descricao')->toArray();

        //passa a descrição do tipo de despesa para um array q será utilizado no autocompletar da view esse for foi feito só para dar exemplo de como funciona em um array
        foreach ($tipoDespesaAC as $tipo) {
            $dadosAutoComplete[] = $tipo['descricao'];
        }
        //manda pra view tds as descriçoes do tipo de despesas
        $this->view->dadosAutoComplete = $this->_helper->util->utf8Encode($dadosAutoComplete);
    }

    public function gridAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();

        //instancia a classe da model tipoDespesa 
        $tipoDespesaDbTable = new Application_Model_DbTable_TipoDespesa();
        //die($this->_getParam('nomeTipoDespesa'));
        //pega o nome ou qualquer coisa que o usuario digitar para buscar
        $descricao = $this->_helper->util->utf8Decode($this->_helper->util->urldecodeGet($this->_getParam('nome')));
               
        //faz uma busca em tipo de despesa para popular o grid
        $select = $tipoDespesaDbTable->select();

        //verificar se o usuario buscou por alguma descrição se sim ele utiliza de where para comparar
        if (!empty($descricao)) {
            $select->where("descricao LIKE '%{$descricao}%'");
        }
        //ordena por descrição
        $select->order('descricao');
        
        //cria uma variavel do tipo array para receber os tipos de despesas
        //$tipoDespesa = array();
        
        //a variavel recebe todos os tipos de depesas buscados
        $tipoDespesa = $this->_helper->util->utf8Encode($select->query()->fetchAll());
               
        // para saber o que vc está buscando é só tirar o comentario abaixo
        //print_r($tipoDespesa);die;
        
        //faz a paginação propria do zend
        $paginator = Zend_Paginator::factory($tipoDespesa);
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
            //manda pra view o titulo
            $this->view->titulo = "Edição de Tipo de Despesas";

            //passa o valor do parametro id para a variavel id
            $id = $this->_getParam('id');

            //instancia a classe da model tipoDespesa 
            $tipoDespesaDbTable = new Application_Model_DbTable_TipoDespesa();

            //busca o tipo de despesa que corresponde ao id que passou como parametro
            $tipoDespesa = $tipoDespesaDbTable->fetchRow("id_tipodespesa = {$id}")->toArray();

            //envia para a view 
            $this->view->tipoDespesa = $this->_helper->util->utf8Encode($tipoDespesa);
        } else {
            /**
             * Cadastro do registro
             */
            //se for cadastro é só enviar o titulo
            $this->view->titulo = "Cadastro de Tipo de Despesas";
        }

    }

    public function salvarAction() {
        //desabilita o layout
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        //esse trecho comentado é para verificar o que vem por post
        //print_r($this->getRequest()->getPost());
        //die;
        //pega o que está vindo por post do formulario
        $dados = $this->_helper->util->utf8Decode($this->getRequest()->getPost());

        try {

            //instancia a classe da model tipoDespesa 
            $tipoDespesaDbTable = new Application_Model_DbTable_TipoDespesa();

            //verifica se o id existe
            if (!empty($dados['id_tipodespesa'])) {
                //passa o valor do id tipo de despesa para a variavel id
                $id = $dados['id_tipodespesa'];

                //atualiza o tipo da dispesa que está vindo pelo post
                $tipoDespesaDbTable->update($dados, "id_tipodespesa = {$id}");
            } else {
                //insere o tipo da despesa
                $tipoDespesaDbTable->insert($dados);
            }

            //retorna a mensagem de sucesso, o tipo da mensagem e a url para o usuario em javascript
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Salvo com sucesso!',
                'url' => '/index/tabs/dir/8/'
            );
        } catch (Exception $exc) {
            //retorna a mensagem de erro para o usuario
            $json = array(
                'tipo' => 'erro',
                'msg' => "Erro errado!",
            );

            //
            //$this->logger->err($exc->getMessage());
        }

        //manda a mensagem de retorno que foi executada
        echo Zend_Json::encode($json);
    }

    public function excluirAction() {
        //desabilita o layout
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        try {
            //pega o parametro id
            $id = $this->getRequest()->getParam('id');

            //instancia a classe da model tipoDespesa 
            $tipoDespesaDbTable = new Application_Model_DbTable_TipoDespesa();

            //chama o metodo que exclui e utiliza o id como parametro de comparação
            $tipoDespesaDbTable->delete("id_tipodespesa = $id");

            //mensagem que retorna no json para javascript
            $json = array(
                'tipo' => 'sucesso',
                'msg' => 'Registro excluído com sucesso!',
            );

            //envia a mensagem para javascript
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

            //envia a mensagem 
            echo Zend_Json::encode($json);
        }
    }

}