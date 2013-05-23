<?php

class Application_Model_DbTable_Fornecedor extends Zend_Db_Table_Abstract
{
    protected $_name = 'fornecedor';
    protected $_primary = 'id_fornecedor';

    
    public function getDataAutoCompleteFornecedorFormulario() {               
        $query = "SELECT DISTINCT id_fornecedor, razao_social, documento FROM " . $this->_name;

        $data = $this->getDefaultAdapter()->fetchAll($query);

        $str = "[";
        foreach ($data as $val) {

            $id_fornecedor = $val['id_fornecedor'];
            $razao_social = $val['razao_social'];
            $documento = $val['documento'];
            
            if ($str != '[')
                $str .=',';

            $razao_social = str_replace("'", "\'", $razao_social);
            $value = $razao_social . " - CNPJ: ".$documento;
            /** enviar o mínimo possível de código pra view para não ficar lerda */
            $str .= "{id: '$id_fornecedor', value: '$value'}";
        }
        $str .= "]";
        return $str;       
    }
    
    
}