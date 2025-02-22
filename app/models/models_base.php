<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/configs/db.php';

/**
 * funcs: connect_to_db(), function close(), clear_input($data), 
 * show_params_html()
 */
class ModelsBase {
    protected $dbparams;
    protected $connection;
    protected $native_table;
    
    function __construct(string $db_conf_name = 'main') {
        global $configs_db;
        // Exeptions needed because of errors in this case are not informative
        $error_start = "Database '";
        $error_end = "' configuration not found";
        if (!isset($configs_db[$db_conf_name])) {
            throw new Exception($error_start.$db_conf_name.$error_end);
        } else {
            if (!isset($configs_db[$db_conf_name]['dbhost'])) {
                throw new Exception($error_start.$db_conf_name.'->dbhost'.$error_end);
            } elseif (!isset($configs_db[$db_conf_name]['dbuser'])) {
                throw new Exception($error_start.$db_conf_name.'->dbuser'.$error_end);
            } elseif (!isset($configs_db[$db_conf_name]['dbpass'])) {
                throw new Exception($error_start.$db_conf_name.'->dbpass'.$error_end);
            } elseif (!isset($configs_db[$db_conf_name]['dbname'])) {
                throw new Exception($error_start.$db_conf_name.'->dbname'.$error_end);
            } else {
                $this -> dbparams['host'] = $configs_db[$db_conf_name]['dbhost'];
                $this -> dbparams['user'] = $configs_db[$db_conf_name]['dbuser'];
                $this -> dbparams['pass'] = $configs_db[$db_conf_name]['dbpass'];
                $this -> dbparams['name'] = $configs_db[$db_conf_name]['dbname'];
            }
        }
    }

    protected function connect_to_db(): void{
		$this -> connection = new mysqli(   $this -> dbparams['host'], 
                                            $this -> dbparams['user'], 
			                                $this -> dbparams['pass'], 
                                            $this -> dbparams['name']) 
            or die("Connection failed: %s\n".$this -> connection -> error);
		$this -> connection -> query('SET NAMES utf8');
	}

    protected function close(): void {
		$this -> connection -> close();
	}

    /**
     *  For debug in future while starts new project
     */
    function show_params_html(): void{
        echo (" DATABASE PARAMS:<br>
                Host: {$this ->         dbparams['host']},<br/> 
                User: {$this ->         dbparams['user']},<br/> 
                Pass: {$this ->         dbparams['pass']},<br/> 
                Base-name: {$this ->    dbparams['name']}<br/>"
            );
    }

    protected function clear_input($data): string{
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function set_native_table(string $name): void {
        $this -> native_table = $name;
    }

    function list_elements($table_name = 'ignore'): array {
        $vault = array();
        if ($table_name != 'ignore') {
            $query = "SELECT * from `{$table_name}`;";
        } else if (isset($this -> native_table)) {
            $query = "SELECT * from `{$this -> native_table}`;";
        } else return $vault;

        $this -> connect_to_db();
        $result = $this -> connection -> query($query);
        $this -> close();
        $counter = 0;
        while ($row = $result -> fetch_assoc()) {
            $vault[$counter] = $row;
            $counter++;
        }

        return $vault;
    }
}
?>
