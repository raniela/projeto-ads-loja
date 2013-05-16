<?php

class Application_Model_DbTable_Tipomercadoria extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipomercadoria';
    protected $_primary = 'id_tipomercadoria';
    
    public function getDataCombo()
    {
    	$data = $this->fetchAll(null, 'descricao');
        $dataCombo = array('' => '');
        foreach ($data as $k => $val) {
            $dataCombo[$val['id_tipomercadoria']] = $val['descricao'];
        }
        return $dataCombo;
    }
}