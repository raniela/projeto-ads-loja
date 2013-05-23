<?php

class Application_Model_DbTable_Compra extends Zend_Db_Table_Abstract {

    protected $_name = 'compra';
    protected $_primary = 'id_compra';

    public function getDataGrid($params = null) {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('c' => $this->_name));

        //join 
        $select->joinInner(array('f' => 'fornecedor'), 'f.id_fornecedor = c.id_fornecedor', array('razao_social'));

        //ordenacao
        $select->order('data DESC');

        //filtros do formulario
        if (!empty($params['razao_social'])) {
            $select->where("f.razao_social LIKE '%{$params['razao_social']}%'");
        }

        if (!empty($params['num_nota_fiscal'])) {
            $select->where("c.num_nota_fiscal = '{$params['num_nota_fiscal']}'");
        }

        if (!empty($params['id_compra'])) {
            $select->where("c.id_compra = '{$params['id_compra']}'");
        }

        if (!empty($params['data_inicial'])) {
            $select->where("c.data >= '{$params['data_inicial']}'");
        }

        if (!empty($params['data_final'])) {
            $select->where("c.data <= '{$params['data_final']}'");
        }

        return $select->query()->fetchAll();
    }

}