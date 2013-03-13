<?php

class Application_Model_DbTable_Cliente extends Zend_Db_Table_Abstract
{
    protected $_name = 'cliente';
    protected $_primary = 'id_cliente';
    
    /**
     * 
     * @return type
     */
    public function getDataAutoCompleteCliente() {
        

        /** estancia o helper Util para utilizar a funcao de remove acento */
        //$util = new Zend_Controller_Action_Helper_Util();

        $query = "SELECT DISTINCT nome FROM " . $this->_name;

        $data = $this->getDefaultAdapter()->fetchAll($query);

        /*$str = '[';
        foreach ($data as $val) {

            $id_cliente = $val['id_cliente'];
            $nome = $val['nome'];
            $documento = $val['documento'];
            
            if ($str != '[')
                $str .=',';

            //$nome = $util->removeAcento(str_replace("'", "\'", strtoupper($nome)));

            /** enviar o mínimo possível de código pra view para não ficar lerdo */
            /*$str .= "{a:'$id_cliente',b:'$nome',c:'$documento'}";
        }
        $str .= "]";
        return $str;*/
        $array = array();
        foreach ($data as $val) {
            $array[] = $val['nome'];
        }
        
        return $array;
    }
    
    /**
     *  
     * @return string
     */
    public function getDataAutoCompleteClienteFormulario() {               
        $query = "SELECT DISTINCT id_cliente,nome,documento FROM " . $this->_name;

        $data = $this->getDefaultAdapter()->fetchAll($query);

        $str = "[";
        foreach ($data as $val) {

            $id_cliente = $val['id_cliente'];
            $nome = $val['nome'];
            $documento = $val['documento'];
            
            if ($str != '[')
                $str .=',';

            $nome = str_replace("'", "\'", $nome);
            $value = $nome . " - CPF/CNPJ: ".$documento;
            /** enviar o mínimo possível de código pra view para não ficar lerda */
            $str .= "{id: '$id_cliente', value: '$value'}";
        }
        $str .= "]";
        return $str;       
    }
}