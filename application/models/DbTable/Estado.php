<?php

class Application_Model_DbTable_Estado extends Zend_Db_Table_Abstract
{

    protected $_name = 'estado';

    public function getDataComboEst()
    {
        $estados = $this->fetchAll()->toArray();

        $dataCombo = array('' => 'selecione');

        foreach ($estados as $estado) {
            $dataCombo [$estado['id_estado']] = $estado['sigla'];
        }
        /* enviando os dados da combo para a view */
        return $dataCombo;
    }

    public function verificaDb($id = null, $dados)
    {

        $nome = $dados['nome'];

        $where = "nome LIKE '{$nome}'";

        if ($id) {
            $where .= " AND id_estado <> $id";
        }

        $estadosCadastrados = $this->fetchRow($where);

        if ($estadosCadastrados) {
            return false;
        } else {
            return true;
        }
    }

}