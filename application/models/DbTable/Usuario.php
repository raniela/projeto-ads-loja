<?php

class Application_Model_DbTable_Usuario extends Zend_Db_Table_Abstract{
    
    protected $_name = 'usuario';
    protected $_primary = 'id_usuario';
    
    public function verificaDb($id = null, $dados) {
        
        $login = $dados['login'];
        
        $where = "login LIKE '%{$login}%'";
        
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
    
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('u' => $this->_name));                
        
        //ordenacao
        $select->order('login DESC');
       
        //filtros do formulario
        if(!empty($params['login'])) {
            $select->where("login LIKE '%{$params['login']}%'");
        }
        
        if(!empty($params['tipo_usuario'])) {
            $select->where("tipo_usuario = '{$params['tipo_usuario']}'");
        }                
        
        return $select->query()->fetchAll();
    }
    
}