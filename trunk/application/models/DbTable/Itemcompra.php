<?php

class Application_Model_DbTable_Itemcompra extends Zend_Db_Table_Abstract
{
    protected $_name = 'itemcompra';
    protected $_primary = 'id_itemcompra';
    
    public function getItensCompra($id_compra) {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('i' => $this->_name));
        
        //join 
        $select->joinInner(array('m' => 'mercadoria'),'m.id_mercadoria = i.id_mercadoria', array('qtde_estoque','descricao'));
        
        //ordenacao
        $select->order('m.descricao');
       
        //filtros
        if(!empty($id_compra)) {
            $select->where("i.id_compra = '{$id_compra}'");
        }               
        
        return $select->query()->fetchAll();
    }
    
}