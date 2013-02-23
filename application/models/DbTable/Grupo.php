<?php

class Application_Model_DbTable_Grupo extends Zend_Db_Table_Abstract
{

    protected $_name = 'grupo';

    public function verificaDb($id = null, $dados)
    {

        $nome = $dados['nome'];

        $where = "nome LIKE '{$nome}'";

        if ($id) {
            $where .= " AND id_grupo <> $id";
        }



        $grupoCadastrado = $this->fetchRow($where);

        if ($grupoCadastrado) {
            return false;
        } else {
            return true;
        }
    }

}