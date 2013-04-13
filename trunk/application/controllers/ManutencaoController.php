<?php

class ManutencaoController extends Zend_Controller_Action {

    /**
     * O metodo init é sempre executado antes de tudo toda vez que a controller é acessada
     */
    public function init() {
        
    }

    public function indexAction() {
        //verifica se existe parametro menu para que no caso desabite o layout    
        if ($this->_getParam('menu')) {
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->titulo = "Manutenção";
    }

    public function backupAction() {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();

        
        $config = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini', APPLICATION_ENV);

        $dbUsername = "root";
        $dbPassword = "";
        $dbName = "loja";
        $file = $this->_helper->backup->backupTables("localhost", $dbUsername, $dbPassword, $dbName);
        
//        $nomeArquivo = time();
//        $file = APPLICATION_PATH . '/../public/files/' . $nomeArquivo . '.sql';
//        $command = sprintf("mysqldump -u %s --password=%s -d %s --skip-no-data > %s", 
//                escapeshellcmd($dbUsername), 
//                escapeshellcmd($dbPassword), 
//                escapeshellcmd($dbName), 
//                escapeshellcmd($file)
//        );
//        exec($command);
        
        $this->view->url = $file;
        
    }       
}