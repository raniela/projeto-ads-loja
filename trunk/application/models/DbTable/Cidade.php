<?php

class Application_Model_DbTable_Cidade extends Zend_Db_Table_Abstract
{

    protected $_name = 'cidade';

    public function getDataGrid($nome = null, $idEstado = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('cid' => 'cidade'));
        //join grupo
        $select->joinInner(array('es' => 'estado'), 'es.id_estado = cid.id_estado', array('nome_estado' => 'nome'));
        //ordenacao
        $select->order('nome');

        //filtros do formulario

        if ($nome) {
            $select->where("cid.nome LIKE '%{$nome}%'");
        }

        if ($idEstado) {
            $select->where("es.id_estado = '{$idEstado}'");
        }



        return $select->query()->fetchAll();
    }

    public function selCid($idEstado = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('ci' => 'cidade'), array('*'
        ));
        //ordenacao
        $select->order('ci.nome');
        //condicao
        $select->where("ci.id_estado = '{$idEstado}'");

        $cidades = $select->query()->fetchAll();

        $dataCombo = array('' => 'Selecione');

        foreach ($cidades as $cidade) {
            $dataCombo [$cidade['id_cidade']] = $cidade['nome'];
        }
        /* enviando os dados da combo para a view */
        
        //Zend_Debug::dump($dataCombo);
       // die;
        return $dataCombo;
    }

    public function verificaDb($id = null, $dados)
    {

        $nome = $dados['nome'];

        $where = "nome LIKE '{$nome}'";

        if ($id) {
            $where .= " AND id_cidade <> $id";
        }

        $cidadeCadastrada = $this->fetchRow($where);

        if ($cidadeCadastrada) {
            return false;
        } else {
            return true;
        }
    }

}