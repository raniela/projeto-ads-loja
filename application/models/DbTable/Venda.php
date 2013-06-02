<?php

class Application_Model_DbTable_Venda extends Zend_Db_Table_Abstract
{
    protected $_name = 'venda';
    protected $_primary = 'id_venda';
    
    public function getDescricaoFormaPagamento($sigla) {
        $formaPagamento['D'] = 'DINHEIRO';
        $formaPagamento['CC'] = 'CARTÃO DE CRÉDITO';
        $formaPagamento['CD'] = 'CARTÃO DE DÉBITO';
        
        if(!empty($formaPagamento[$sigla])) {
            return $formaPagamento[$sigla];
        } else {
            return "";
        }
    }
    
    public function getDescricaoTipoPagamento($sigla) {
        $tipoPagamento['V'] = 'A VISTA';
        $tipoPagamento['P'] = 'A PRAZO';        
        
        if(!empty($tipoPagamento[$sigla])) {
            return $tipoPagamento[$sigla];
        } else {
            return "";
        }
    }
    
    public function getDataGrid($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('v' => $this->_name));
        
        //join 
        $select->joinInner(array('c' => 'cliente'),'c.id_cliente = v.id_cliente', array('nome'));
        
        //ordenacao
        $select->order('data_venda DESC');
       
        //filtros do formulario
        if(!empty($params['nome'])) {
            $select->where("c.nome LIKE '%{$params['nome']}%'");
        }
        
        if(!empty($params['tipo_pagamento'])) {
            $select->where("v.tipo_pagamento = '{$params['tipo_pagamento']}'");
        }
        
        if(!empty($params['id_venda'])) {
            $select->where("v.id_venda = '{$params['id_venda']}'");
        }
        
        if(!empty($params['forma_pagamento'])) {
            $select->where("v.forma_pagamento = '{$params['forma_pagamento']}'");
        }
        
        if(!empty($params['data_inicial'])) {
            $select->where("v.data_venda >= '{$params['data_inicial']}'");
        }
        
        if(!empty($params['data_final'])) {
            $select->where("v.data_venda <= '{$params['data_final']}'");
        }
        
        return $select->query()->fetchAll();
    }
    
    public function getDataToRelatorioMovimentacaoCaixa($params = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('v' => $this->_name));
        
        //join cliente
        $select->joinInner(array('c' => 'cliente'),'c.id_cliente = v.id_cliente', array('nome'));
        
        //join duplicata
        $dadosDuplicata = array('valorRecebido' => new Zend_Db_Expr("COALESCE(SUM(d.valor_pago),0)"));
        $select->joinInner(array('d' => 'duplicata'),'d.id_venda = v.id_venda', $dadosDuplicata);                        
        
        //agrupamento
        $select->group('v.id_venda');
        
        //ordenacao
        $select->order('data_venda DESC');
       
        //filtros do formulario                
        if(!empty($params['data_inicial'])) {
            $select->where("d.data_pagamento >= '{$params['data_inicial']}'");
        }
        
        if(!empty($params['data_final'])) {
            $select->where("d.data_pagamento <= '{$params['data_final']}'");
        }
        
        return $select->query()->fetchAll();
    }
}