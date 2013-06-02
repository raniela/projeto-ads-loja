<?php

class Application_Model_DbTable_Despesa extends Zend_Db_Table_Abstract
{
    protected $_name = 'despesa';
    protected $_primary = 'id_despesa';
    
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('d' => $this->_name));                
        
        //join com tipo de mercadoria
        $select->joinInner(array('td' => 'tipodespesa'),'td.id_tipodespesa = d.id_tipodespesa', array('tipo' => 'descricao'));
        
        //ordenacao
        $select->order('descricao');
       
        //filtros do formulario
        if(!empty($params['descricao'])) {
            $select->where("d.descricao LIKE '%{$params['descricao']}%'");
        }
        
        if(!empty($params['id_tipodespesa'])) {
            $select->where("d.id_tipodespesa = '{$params['id_tipodespesa']}'");
        }
        
        if(!empty($params['id_despesa'])) {
            $select->where("d.id_despesa = '{$params['id_despesa']}'");
        }
        //die($select);
        return $select->query()->fetchAll();
    }
    
    public function verificaDb($id = null, $dados) {
        
        $descricao = $dados['descricao'];
        $idTipoDespesa = $dados['id_tipodespesa'];
        
        $where = "descricao = '{$descricao}' AND id_tipodespesa = '{$idTipoDespesa}'";
        
        if($id){
            $where .= " AND id_despesa <> $id";
        }
        
        $despesasCadastradas = $this->fetchRow($where);
        
        if($despesasCadastradas){
            return false;
        }else{
            return true;
        }                
    }
    
    public function getDataToRelatorioMovimentacaoCaixa($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from despesa
        $select->from(array('d' => $this->_name));                
        
        //join parcela
        //$dadosParcela = array('valorPago' => new Zend_Db_Expr("COALESCE(SUM(p.valor_pago),0)"));
        $select->joinInner(array('p' => 'parcela'),'d.id_despesa = p.id_despesa', array('valor_pago','data_pagamento'));                                        
        
        //ordenacao
        $select->order('descricao');
       
        //filtros do formulario                
        if(!empty($params['data_inicial'])) {
            $select->where("p.data_pagamento >= '{$params['data_inicial']}'");
        }
        
        if(!empty($params['data_final'])) {
            $select->where("p.data_pagamento <= '{$params['data_final']}'");
        }
        
        //die($select);
        return $select->query()->fetchAll();
    }
}