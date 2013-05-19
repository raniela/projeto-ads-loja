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
        
        //join com subtipo de mercadoria
        $select->joinInner(array('st' => 'subtipomercadoria'),'m.id_subtipomercadoria = st.id_subtipomercadoria', array('subtipo' => 'descricao'));
        
        //join com tipo de mercadoria
        $select->joinInner(array('t' => 'tipomercadoria'),'st.id_tipomercadoria = t.id_tipomercadoria', array('tipo' => 'descricao'));
        
        //ordenacao
        $select->order('m.descricao');
       
        //filtros do formulario
        if(!empty($params['descricao'])) {
            $select->where("m.descricao LIKE '%{$params['descricao']}%'");
        }
        
        if(!empty($params['id_subtipomercadoria'])) {
            $select->where("st.id_subtipomercadoria = '{$params['id_subtipomercadoria']}'");
        }
        
        if(!empty($params['id_tipomercadoria'])) {
            $select->where("t.id_tipomercadoria = '{$params['id_tipomercadoria']}'");
        }
        
        return $select->query()->fetchAll();
    }
    
    public function verificaDb($id = null, $dados) {
        
        $descricao = $dados['descricao'];
        $idSubTipoMercadoria = $dados['id_subtipomercadoria'];
        
        $where = "descricao = '{$descricao}' AND id_subtipomercadoria = '{$idSubTipoMercadoria}'";
        
        if($id){
            $where .= " AND id_mercadoria <> $id";
        }
        
        $mercadoriasCadastradas = $this->fetchRow($where);
        
        if($mercadoriasCadastradas){
            return false;
        }else{
            return true;
        }                
    }
}