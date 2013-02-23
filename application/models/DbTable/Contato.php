<?php

class Application_Model_DbTable_Contato extends Zend_Db_Table_Abstract
{

    protected $_name = 'contato';

    // protected $_dependetesTables = array('Comments');


    public function executaSQLInnerJoin()
    {
        $sql = $this->getDefaultAdapter()->select()
                ->from(array('cn' => 'contato'), array('*'
                ))
                ->joinInner(array('gr' => 'grupo'), 'gr.id_grupo = cn.id_grupo', array('nome_grupo' => 'nome'))
                ->group('cn.id_contato');


        //die($sql);

        $ret = $sql->query()->fetchAll();

        return $ret;
    }

    public function getDataGrid($nome = null, $idGrupo = null)
    {
        //obj select
        $select = $this->getDefaultAdapter()->select();
        //from contato
        $select->from(array('cn' => 'contato'));
        //join grupo
        $select->joinInner(array('gr' => 'grupo'), 'gr.id_grupo = cn.id_grupo', array('nome_grupo' => 'nome'));
        //ordenacao
        $select->order('nome');

        //filtro por usuario logado
        $user = Zend_Auth::getInstance()->getIdentity();
        $select->where("id_usuario = '{$user->id_usuario}'");


        //filtros do formulario

        if ($nome) {
            $select->where("cn.nome LIKE '%{$nome}%'");
        }

        if ($idGrupo) {
            $select->where("gr.id_grupo = '{$idGrupo}'");
        }



        return $select->query()->fetchAll();
    }

    public function verificaDb($id = null, $dados)
    {

        $nome = $dados['nome'];
        $email = $dados['email'];

        $where = "nome LIKE '{$nome}' AND email LIKE '{$email}'";

        if ($id) {
            $where .= " AND id_contato <> $id";
        }
        
        $contatoCadastrado = $this->fetchRow($where);
        
        if($contatoCadastrado){
            return false;
        }else{
            return true;
        }
        
    }

}