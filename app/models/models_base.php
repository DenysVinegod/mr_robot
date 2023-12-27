<?php
/**
 * funcs: connect_to_db(), function close(), clear_input($data), 
 * show_params_html()
 */
class ModelsBase {
    protected $connection;
    
    protected $dbparams;
    
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
