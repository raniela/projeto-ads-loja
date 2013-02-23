<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
protected function _initLogger()
    {
    
    //é chamado sempre qdo inicia o programa
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Firebug();
        $logger->addWriter($writer);
        Zend_Registry::set('logger', $logger);
    }
}

