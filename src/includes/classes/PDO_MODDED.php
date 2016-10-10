<?php
class PDO_MODDED extends PDO
{
	private static $instance;
	
	private function __construct()
	{
		try
		{
			parent::__construct( SQLH_MANAGER . ':host=' .  SQLH_HOST . ';port=' . SQLH_PORT . ';dbname=' . SQLH_DATABASE . ';charset=utf8',
			SQLH_USERNAME, SQLH_PASSWORD, array(PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_FOUND_ROWS => true) );
			//The second array attribute is useful so you can use rowCount() to see all rows MATCHED by UPDATE statements, not only the changed ones.
			//The first one grants a persistent connection, which means less average time spent per query after some queries.
		}
		catch (PDOException $e)
		{
			Write_To_Logfile($e);
			exit('Erro 1001d');
		}
	}
	
	public static function getInstance()
	{
        if(isset(self::$instance) === FALSE)
		{
            self::$instance = new self();
        }
        return self::$instance;
    }

	// Returns number of rows found by previous SELECT statement.
    public function last_row_count()
    {
    	return $this->query("SELECT FOUND_ROWS();")->fetchColumn();
    }

	// For debugging
	public function Debug_PDO_Error(PDOStatement $stmt = NULL, /*Bool*/ $critical_exit = FALSE)
	{
		if($stmt === NULL)
			$err = $this->errorInfo();
		else
			$err = $stmt->errorInfo();
		if( $err[0] == 00000 || $err[0] == 'ORA-00000' )
			return TRUE; // Success

		$errstr = '';
		$errstr .='<pre>';
		$errstr .='PDO Statement Error!' . "\n";
		$errstr .='SQLState: ' . $err[0] . "\n";
		$errstr .='Error Code: ' . $err[1] . "\n";
		$errstr .='Error Message: ' . $err[2] . "\n\n";
		$errstr .='DEBUG BACKTRACE:' . "\n";
		$errstr .= print_r ( debug_backtrace(), TRUE );
		$errstr .="\n";
		$errstr .='</pre>';

		if($critical_exit === TRUE)
		{
			exit($errstr);
		}
		else
		{
			//Colocar tudo isso no arquivo de debug
			Write_To_Logfile(str_replace(Array('<pre>','</pre>'),'',$errstr));
			return FALSE;
		}
	}

	public function getEnumArray(/*String*/ $tablename, /*String*/ $colname)
	{
		$tablename = $this::quote($tablename);
		$colname = $this::quote($tablename);

		$stmt = $this->query("SHOW COLUMNS FROM {$tablename} WHERE Field = '{$colname}'");
		Debug_PDO_Error();

		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		$option_array = explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2", $data['Type']));
		$return_array = Array();
		foreach($option_array as $key=>$value) // Indexes keys according to ENUM/SET index table, so Array indexes can be used programatically
			$return_array[$key+1] = $value;

		return $return_array;
	}
}
?>