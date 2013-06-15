<?php

class IndexController extends Zend_Controller_Action {

    public function indexAction() {

        $this->view->titulo = "Login Loja";

        if ($this->getRequest()->isPost()) {

            $auth = Zend_Auth::getInstance();

            $db = Zend_Db_Table::getDefaultAdapter();

            $login = $this->getRequest()->getPost('login');
            $senha = $this->getRequest()->getPost('senha');
            
            if(empty($login)) {
                $login = "";
            }
            
            if(empty($senha)) {
                $senha = " ";
            }
            
            /* usa a classe Zend_Auth_Adapter_DbTable na autenticação */
            $authAdapterDbTable = new Zend_Auth_Adapter_DbTable($db);

            $authAdapterDbTable->setTableName('usuario') //nome da tabela usada para autenticar
                    ->setIdentityColumn('login') // nome da coluna de login
                    ->setCredentialColumn('senha') // nome da coluna de senha
                    ->setIdentity($login) // valor para testar o login
                    ->setCredential($senha); // valor para testar a senha

            /* autentica usuário */
            $result = $auth->authenticate($authAdapterDbTable); // tenta autenticar e retorna um codigo para testar
            //Zend_Debug::dump($result);
            //die;

            /* verifica o codigo e lança uma exceção */
            switch ($result->getCode()) { // testa o codigo
                case Zend_Auth_Result::SUCCESS:
                    /* pega os dados do usuario */
                    $resultRow = $authAdapterDbTable->getResultRowObject();

                    /* salva o usuario na sessão */
                    $auth->getStorage()->write($resultRow);

                    /* Salva a data do login como ultimo acesso */
                    $this->usuarioDbTable = new Application_Model_DbTable_Usuario();
                    $usuario['dt_ultimo_acesso'] = date('Y-m-d H:i:s');
                    $this->usuarioDbTable->update($usuario, "id_usuario = {$resultRow->id_usuario}");
                    
                    /* Direciona o usuário para a página de abertura do sistema*/
                    $this->_redirect('/index/tabs');
                    $usuario = Zend_Auth::getInstance()->getIdentity();
                    
                    $this->view->usuario = $usuario;
                    /* $ident = $auth->getIdentity();
                      Zend_Debug::dump($ident);
                      die; */
                    break;
                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    die("Nome de usuario não existe");
                    break;
                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    die("Senha invalida");
                    break;
                default :
                    die("Um erro desconhecido ocorreu, entre em contato com a administração");
                    break;
            }
        }
    }

    public function sairAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/index');
    }

    public function emailAction() {

        try {
            $config = array('auth' => 'login',
                'username' => 'raniela.carvalho@sigma.com.br',
                'password' => '',
                'ssl' => 'ssl',
                'port' => '465'
            );
            $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
            $mail = new Zend_Mail('UTF-8');
            $mail->setFrom('raniela.carvalho@sigma.com.br');
            $mail->addTo('eduardo.mendonca@sigma.com.br');
            $mail->setBodyText('UHUHU');
            $mail->setSubject('TESTE');
            $mail->send($mailTransport);
            echo "Email enviado com SUCESSSO!";
        } catch (Exception $e) {
            echo ($e->getMessage());
        }
        exit;
    }

    public function tabsAction() {
        //Zend_Debug::dump($this->_getParam('dir'));die;
        $this->view->dir = $this->_getParam('dir', 1);
    }

}

