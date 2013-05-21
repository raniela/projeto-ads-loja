<?php

class Application_Model_DbTable_Tipodespesa extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipodespesa';
    protected $_primary = 'id_tipodespesa';
    
    public function getDataCombo()
    {
    	$data = $this->fetchAll(null, 'descricao');
        $dataCombo = array('' => '');
        foreach ($data as $k => $val) {
            $dataCombo[$val['id_tipodespesa']] = $val['descricao'];
        }
        return $dataCombo;
    }
}