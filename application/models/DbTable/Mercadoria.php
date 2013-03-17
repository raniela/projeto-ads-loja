<?php

class Application_Model_DbTable_Mercadoria extends Zend_Db_Table_Abstract
{
    protected $_name = 'mercadoria';
    protected $_primary = 'id_mercadoria';
    
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('m' => $this->_name));                
        
        //ordenacao
        $select->order('descricao');
       
        //filtros do formulario
        if(!empty($params['descricao'])) {
            $select->where("descricao LIKE '%{$params['descricao']}%'");
        }
        
        if(!empty($params['id_subtipomercadoria'])) {
            $select->where("id_subtipomercadoria = '{$params['id_subtipomercadoria']}'");
        }                
        
        return $select->query()->fetchAll();
    }
}