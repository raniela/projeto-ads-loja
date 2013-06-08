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
        
        $nomeArquivo = time().".sql";                               

        $diretorio = realpath(APPLICATION_PATH . "/../public/files") . "/" . $nomeArquivo;
        //$file = $this->_helper->backup->backupTables("localhost", $dbUsername, $dbPassword, $dbName);
        
        
        $comandoDump = "C:\\xampp\\mysql\\bin\\mysqldump.exe -u root -p12345  -h 127.0.0.1 loja > ".$diretorio;
        //print $comandoDump;
        //die();
        exec($comandoDump);
        
//        $nomeArquivo = time();
//        $file = APPLICATION_PATH . '/../public/files/' . $nomeArquivo . '.sql';
//        $command = sprintf("mysqldump -u %s --password=%s -d %s --skip-no-data > %s", 
//                escapeshellcmd($dbUsername), 
//                escapeshellcmd($dbPassword), 
//                escapeshellcmd($dbName), 
//                escapeshellcmd($file)
//        );
//        exec($command);
        
        $this->view->url = $nomeArquivo;
        //$this->view->url = $file;
        
    }
    
    public function uploadBackupAction()
    {
        //desabilita layout
        $this->getHelper('layout')->disableLayout();
        //$config = new Zend_Config_Ini(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini', APPLICATION_ENV);               
        
        /** Cria o diretorio do arquivo caso não exista*/
        $diretorioFiles = realpath(APPLICATION_PATH . "/../public/files") . '/';                        
        $diretorioFiles .= 'RESTAURAR';
        if(!is_dir($diretorioFiles)) {            
            mkdir($diretorioFiles, 0777, true);
        }
                                
        $extensoesPermitidas = array('sql');
        $urlArquivo = $this->_helper->upload->file('fileUploadBackup', null, $extensoesPermitidas, $diretorioFiles.'/');
        $diretorio = $diretorioFiles.'/'.$urlArquivo;
        

        // mysql -u root -p Tutorials < tut_backup.sql
        $comandoDump = "C:\\xampp\\mysql\\bin\\mysql.exe -u root -p12345 -h 127.0.0.1 loja < ".$diretorio;
        
        exec($comandoDump);
        echo "Backup retaurado com sucesso.";
        $this->getHelper('viewRenderer')->setNoRender();
        //die();        
        
//        mysql_connect("localhost", "root", "") or die("Error connecting to MySQL server: ".mysql_error());
//        mysql_select_db("loja");
//
//        $all_lines = file($diretorioFiles.'/'.$urlImagem, FILE_SKIP_EMPTY_LINES);
//        
//        mysql_query("DROP DATABASE loja");
//        mysql_query("CREATE DATABASE loja");
//        mysql_query("USE loja");
//        foreach($all_lines as $query) {
//            if(substr($query, 0, 2) == "--") {
//                continue;
//            }
//            //print $query;
//            mysql_query($query) or die();            
//        }
    }
}