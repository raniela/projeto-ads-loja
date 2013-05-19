<?php

class Application_Model_DbTable_Subtipomercadoria extends Zend_Db_Table_Abstract
{
    protected $_name = 'subtipomercadoria';
    protected $_primary = 'id_subtipomercadoria';
    
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        
        //from subtipomercadoria
        $select->from(array('st' => $this->_name));
        
        //join com tipo de mercadoria
        $select->joinInner(array('t' => 'tipomercadoria'),'t.id_tipomercadoria = st.id_tipomercadoria', array('tipo' => 'descricao'));
        
        //ordenacao
        $select->order('st.descricao');
       
        //filtros do formulario
        if(!empty($params['descricao'])) {
            $select->where("st.descricao LIKE '%{$params['descricao']}%'");
        }
        
        if(!empty($params['id_tipomercadoria'])) {
            $select->where("t.id_tipomercadoria = '{$params['id_tipomercadoria']}'");
        }                        
        
        return $select->query()->fetchAll();
    }
    
    public function verificaDb($id = null, $dados) {
        
        $descricao = $dados['descricao'];
        $idTipoMercadoria = $dados['id_tipomercadoria'];
        
        $where = "descricao = '{$descricao}' AND id_tipomercadoria = '{$idTipoMercadoria}'";
        
        if($id){
            $where .= " AND id_subtipomercadoria <> $id";
        }
        
        $subTiposCadastrados = $this->fetchRow($where);
        
        if($subTiposCadastrados){
            return false;
        }else{
            return true;
        }                
    }
    
    public function getDataCombo($params = null)
    {
    	$data = $this->getDataGrid($params);
        $dataCombo = array('' => '');
        
        foreach ($data as $k => $val) {
            $dataCombo[$val['id_subtipomercadoria']] = $val['descricao'];
        }
        
        return $dataCombo;
    }
    
    public function getIdTipoMercadoria($id_subtipomercadoria)
    {
    	$data = $this->fetchRow("id_subtipomercadoria = '{$id_subtipomercadoria}'")->toArray();
        
        if(!empty($data['id_tipomercadoria'])) {
            return $data['id_tipomercadoria'];
        } else {
            return "";
        }
    }
}