<?php

class Zend_Controller_Action_Helper_Backup extends Zend_Controller_Action_Helper_Abstract {

    /* backup the db OR just a table */
    function backupTables($host, $user, $pass, $name, $tables = '*')
    {

            $link = mysql_connect($host,$user,$pass);
            mysql_select_db($name,$link);

            //get all of the tables
            if($tables == '*')
            {
                    $tables = array();
                    $result = mysql_query('SHOW TABLES');
                    while($row = mysql_fetch_row($result))
                    {
                            $tables[] = $row[0];
                    }
            }
            else
            {
                    $tables = is_array($tables) ? $tables : explode(',',$tables);
            }
            $return = "";
            //cycle through
            foreach($tables as $table)
            {
                    $result = mysql_query('SELECT * FROM '.$table);
                    $num_fields = mysql_num_fields($result);

                    $return.= 'DROP TABLE '.$table.';';
                    $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
                    $return.= "\n\n".$row2[1].";\n\n";

                    for ($i = 0; $i < $num_fields; $i++) 
                    {
                            while($row = mysql_fetch_row($result))
                            {
                                    $return.= 'INSERT INTO '.$table.' VALUES(';
                                    for($j=0; $j<$num_fields; $j++) 
                                    {
                                            $row[$j] = addslashes($row[$j]);
                                            $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                                            if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                                            if ($j<($num_fields-1)) { $return.= ','; }
                                    }
                                    $return.= ");\n";
                            }
                    }
                    $return.="\n\n\n";
            }

            //save file
            //$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
            //fwrite($handle,$return);
            //fclose($handle);
            
            $nomeArquivo = time().".sql";                               

            $fp = fopen(realpath(APPLICATION_PATH . "/../public/files") . "/" . $nomeArquivo, "a");
            fwrite($fp, $return);        
            fclose($fp);

            return "{$nomeArquivo}";
    }
}
?>
