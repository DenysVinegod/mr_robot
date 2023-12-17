<?php
/**
 * funcs: connect_to_db(), function close(), clear_input($data), 
 * show_params_html()
 */
class ModelsBase {
    private $connection;
    
    private $dbparams;
    
    function __construct($params_a = array(
        'dbhost' => 'localhost',
        'dbuser' => 'mr_robot', 
        'dbpass' => 'ip$Hone123',
        'dbname' => 'mr_robot'
        )) {
            $this -> dbparams['host'] = $params_a['dbhost'];
		    $this -> dbparams['user'] = $params_a['dbuser'];
		    $this -> dbparams['pass'] = $params_a['dbpass'];
		    $this -> dbparams['name'] = $params_a['dbname'];
    }

    private function connect_to_db(): void{
		$this -> connection = new mysqli(   $this -> dbparams['host'], 
                                            $this -> dbparams['user'], 
			                                $this -> dbparams['pass'], 
                                            $this -> dbparams['name']) 
            or die("Connection failed: %s\n".$this -> connection -> error);
		$this -> connection -> query('SET NAMES utf8');
	}

    private function close(): void {
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

    private function clear_input($data): string{
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
?>