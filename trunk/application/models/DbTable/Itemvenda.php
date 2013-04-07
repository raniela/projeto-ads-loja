<?php

class Application_Model_DbTable_Itemvenda extends Zend_Db_Table_Abstract
{
    protected $_name = 'itemvenda';
    protected $_primary = 'id_itemvenda';
    
    public function getItensVenda($id_venda) {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('i' => $this->_name));
        
        //join 
        $select->joinInner(array('m' => 'mercadoria'),'m.id_mercadoria = i.id_mercadoria', array('qtde_estoque','descricao'));
        
        //ordenacao
        $select->order('m.descricao');
       
        //filtros
        if(!empty($id_venda)) {
            $select->where("i.id_venda = '{$id_venda}'");
        }               
        
        return $select->query()->fetchAll();
    }
}