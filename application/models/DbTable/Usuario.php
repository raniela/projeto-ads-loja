<?php

class Application_Model_DbTable_Usuario extends Zend_Db_Table_Abstract{
    
    protected $_name = 'usuario';
    protected $_primary = 'id_usuario';
    
    public function verificaDb($id = null, $dados){
        
        $login = $dados['login'];
        
        $where = "login LIKE '{$login}'";
        
        if($id){
            $where .= " AND id_usuario <> $id";
        }
        
        $usuariosCadastrados = $this->fetchRow($where);
        
        if($usuariosCadastrados){
            return false;
        }else{
            return true;
        }
        
        
    }
    
}