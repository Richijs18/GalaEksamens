<?php

class db
{
    private static $instance;
    private $mysqli;
    private $debug;
	private $affected_rows;
	
    public function __construct()
	{
		$this->debug = array();
		$this->affected_rows = -1;
		$this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
        if ($this->mysqli->connect_errno) {
			$this->debug[] = "connect() failed: (".$this->mysqli->connect_errno.") ".$this->mysqli->connect_error;
			exit('Tiek veikti uzlabojumi (maintenance)!');
		}
		$this->mysqli->set_charset("utf8");
    }
	
	public function __destruct() {
		if (DEBUG)
			$this->get_debug();
		else
			$this->email_error();
	}
	
	private function d2_d1($param) {
		$str='';
		foreach($param as $arr)
			$str .= $arr[0];
			
		$param2=array();	
		$param2[]=$str;
		
		foreach($param as $arr)
			$param2[] = $arr[1];
		
		return $param2;
	}
	
	private static function ref_values($arr){
		if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
		{
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
		return $arr;
	}

	public static function query($prepare=null, $param=null)
	{
		if ($prepare!==null)
		{
			if (!self::$instance) self::$instance = new db();
			$ret = null;
			if ($stmt = self::$instance->mysqli->prepare($prepare))
			{
				if ($param!=null) 
				{
					$param=self::$instance->d2_d1($param);
					if (call_user_func_array(array($stmt, "bind_param"), db::ref_values($param)))
					{
						if ($stmt->execute())
						{
							self::$instance->affected_rows = $stmt->affected_rows;
							if ($result = $stmt->result_metadata())
							{
								$stmt->store_result();
								if ($stmt->num_rows>0)
								{
									$fields = $result->fetch_fields();
									$statement_params='';
									 foreach($fields as $field){
										if(empty($statement_params))
											$statement_params.="\$column['".$field->name."']";
										else
											$statement_params.=", \$column['".$field->name."']";
									}
									$statment="\$stmt->bind_result($statement_params);";
									eval($statment);
									$ret = array();
									while($stmt->fetch()) {							
										foreach($column as $key=>$value)
											$row_tmb[ $key ] = $value;
										$ret[] = $row_tmb;
									}
									$result->free();
								}
							}
							$stmt->close();
						}
						else
							self::$instance->debug[] = "execute() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
					}
					else
						self::$instance->debug[] = "bind_param() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
				}
				else
				{
					if ($stmt->execute())
					{
						self::$instance->affected_rows = $stmt->affected_rows;
						if ($result = $stmt->result_metadata())
						{
							$stmt->store_result();
							if ($stmt->num_rows>0)
							{
								$fields = $result->fetch_fields();
								$statement_params='';
								 foreach($fields as $field){
									if(empty($statement_params))
										$statement_params.="\$column['".$field->name."']";
									else
										$statement_params.=", \$column['".$field->name."']";
								}
								$statment="\$stmt->bind_result($statement_params);";
								eval($statment);
								$ret = array();
								while($stmt->fetch()) {							
									foreach($column as $key=>$value)
										$row_tmb[ $key ] = $value;
									$ret[] = $row_tmb;
								}
								$result->free();
							}
						}
						$stmt->close();
					}
					else
						self::$instance->debug[] = "execute() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
				}
			}
			else
				self::$instance->debug[] = "prepare() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
			return $ret;
		}
		else
			self::$instance->debug[] = "query() failed: missing parameter - prepare";
	}
	
	public static function query_row($prepare=null, $param=null)
	{
		if ($prepare!==null)
		{
			if (!self::$instance) self::$instance = new db();
			$ret = null;
			if ($stmt = self::$instance->mysqli->prepare($prepare))
			{
				if ($param!=null) 
				{
					$param=self::$instance->d2_d1($param);
					if (call_user_func_array(array($stmt, "bind_param"), db::ref_values($param)))
					{
						if ($stmt->execute())
						{							
							if ($result = $stmt->result_metadata())
							{
								$stmt->store_result();
								if ($stmt->num_rows>0)
								{
									$fields = $result->fetch_fields();
									$statement_params='';
									 foreach($fields as $field){
										if(empty($statement_params))
											$statement_params.="\$column['".$field->name."']";
										else
											$statement_params.=", \$column['".$field->name."']";
									}
									$statment="\$stmt->bind_result($statement_params);";
									eval($statment);
									$stmt->fetch();					
									$ret = $column;
									$result->free();
								}
							}
							$stmt->close();
						}
						else
							self::$instance->debug[] = "execute() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
					}
					else
						self::$instance->debug[] = "bind_param() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
				}
				else
				{
					if ($stmt->execute())
					{
						if ($result = $stmt->result_metadata())
						{
							$stmt->store_result();
							if ($stmt->num_rows>0)
							{
								$fields = $result->fetch_fields();
								$statement_params='';
								 foreach($fields as $field){
									if(empty($statement_params))
										$statement_params.="\$column['".$field->name."']";
									else
										$statement_params.=", \$column['".$field->name."']";
								}
								$statment="\$stmt->bind_result($statement_params);";
								eval($statment);
								$stmt->fetch();					
								$ret = $column;
								$result->free();
							}
						}
						$stmt->close();
					}
					else
						self::$instance->debug[] = "execute() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
				}
			}
			else
				self::$instance->debug[] = "prepare() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
			return $ret;
		}
		else
			self::$instance->debug[] = "query() failed: missing parameter - prepare";
	}
	
	public static function query_value($prepare=null, $param=null)
	{
		if ($prepare!==null)
		{
			if (!self::$instance) self::$instance = new db();
			$ret = '';
			if ($stmt = self::$instance->mysqli->prepare($prepare))
			{
				if ($param!=null) 
				{
					$param=self::$instance->d2_d1($param);
					if (call_user_func_array(array($stmt, "bind_param"), db::ref_values($param)))
					{
						if ($stmt->execute())
						{							
							if ($result = $stmt->result_metadata())
							{								
								if ($stmt->bind_result($ret)) {
									if(!$stmt->fetch())
										$ret='';
								} else
									self::$instance->debug[] = "bind_result() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
							}
							$stmt->close();
						}
						else
							self::$instance->debug[] = "execute() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
					}
					else
						self::$instance->debug[] = "bind_param() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
				}
				else
				{
					if ($stmt->execute())
					{
						if ($result = $stmt->result_metadata())
						{
							if ($stmt->bind_result($ret)) {
								if(!$stmt->fetch())
									$ret='';
							} else
								self::$instance->debug[] = "bind_result() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".var_dump($prepare).")";
						}
						$stmt->close();
					}
					else
						self::$instance->debug[] = "execute() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
				}
			}
			else
				self::$instance->debug[] = "prepare() failed: (".self::$instance->mysqli->errno.") ".self::$instance->mysqli->error." (".$prepare.")";
			return $ret;
		}
		else
			self::$instance->debug[] = "query() failed: missing parameter - prepare";
	}
	
	public static function insert_id()
	{
        if (!self::$instance) self::$instance = new db();
		return self::$instance->mysqli->insert_id;
	}
	
	public static function affected_rows()
	{
        if (!self::$instance) self::$instance = new db();
		return self::$instance->affected_rows;
    }
	
	private function get_debug()
	{
        if (!self::$instance) self::$instance = new db();
		if (!empty(self::$instance->debug))
		{
			echo '<pre>';
			var_dump(self::$instance->debug);
			echo '</pre>';
		}
    }
	
	private function email_error() {
		$to  = '';
		$subject = 'MySQL Error';

		$message = '<html><head><title>'.$subject.'</title></head><body><table border="1">';
		
		$message .= '<tr><td colspan="2" align="center"><b>INFO</b></td></tr>';
		$message .= '<tr><td>REMOTE_ADDR:</td><td>'.$_SERVER['REMOTE_ADDR'].'</td></tr>';
		$message .= '<tr><td>HTTP_HOST:</td><td>'.$_SERVER['HTTP_HOST'].'</td></tr>';
		$message .= '<tr><td>REQUEST_URI:</td><td>'.$_SERVER['REQUEST_URI'].'</td></tr>';
		$message .= '<tr><td>SCRIPT_FILENAME:</td><td>'.$_SERVER['SCRIPT_FILENAME'].'</td></tr>';
		$message .= '<tr><td colspan="2" align="center"><b>MySQLi</b></td></tr>';
		
		if (!empty($this->debug)) {
			foreach ($this->debug as $debug)
				$message .= '<tr><td>MySQLi Error:</td><td>'.$debug.'</td></tr>';
			
			$message .= '<tr><td colspan="2" align="center"><b>$_SERVER</b></td></tr>';
			foreach ($_SERVER as $key=>$info)
				$message .= '<tr><td>'.$key.'</td><td>'.$info.'</td></tr>';
				
			$message .= '</table></body></html>';
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

			mail($to, $subject, $message, $headers);
		}
	}

}

?>