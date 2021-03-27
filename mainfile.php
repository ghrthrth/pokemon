<?php

error_reporting(0);

$dbhost = "localhost";
$dbname = "dragon";
$dbuname = "root";
$dbpass = "SFgLqI##dc7b";

// End the transaction
if(!defined('END_TRANSACTION')) {
  define('END_TRANSACTION', 2);
}

$_SERVER['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER'])?addslashes(stripslashes($_SERVER['HTTP_REFERER'])):'';  // Security patch by KrasivayaSvo

$HTTP_GET_VARS = $_GET;
$HTTP_POST_VARS = $_POST;
$HTTP_SERVER_VARS = $_SERVER;
$HTTP_POST_FILES = $_FILES;
$HTTP_ENV_VARS = $_ENV;
$PHP_SELF = $_SERVER['PHP_SELF'];
 
if(isset($_SESSION)) $HTTP_SESSION_VARS = $_SESSION;
if(isset($_COOKIE))  $HTTP_COOKIE_VARS = $_COOKIE;


// After doing those superglobals we can now use one
// and check if this file isnt being accessed directly
if (stristr(htmlentities($_SERVER['PHP_SELF']), "mainfile.php")) {
    header("Location: index.php");
    exit();
}

if (!function_exists("floatval")) {
    function floatval($inputval) {
        return (float)$inputval;
    }
}


/*
Возможно, понадобится
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && @extension_loaded('zlib') && !headers_sent()) {
   ob_start('ob_gzhandler');
   ob_implicit_flush(0);
}
*/


if (!@ini_get('register_globals')) {
   extract($_GET, EXTR_SKIP);
   extract($_POST, EXTR_SKIP);
   extract($_COOKIE, EXTR_SKIP);
} 

//Union Tap
//Copyright Zhen-Xjell 2004 http://nukecops.com
//Beta 3 Code to prevent UNION SQL Injections
unset($matches);
unset($loc);

if(isset($_SERVER['QUERY_STRING'])) {
	if (preg_match("/([OdWo5NIbpuU4V2iJT0n]{5}) /", rawurldecode($loc=$_SERVER['QUERY_STRING']), $matches)) {
    	die('Illegal Operation');
  	}
}


if(!function_exists('stripos')) {
	function stripos_clone($haystack, $needle, $offset=0) {
		$return = strpos(strtoupper($haystack), strtoupper($needle), $offset);
		if ($return === false) {
			return false;
		} else {
			return true;
		}
	}
} else {
	// But when this is PHP5, we use the original function
	function stripos_clone($haystack, $needle, $offset=0) {
		$return = stripos($haystack, $needle, $offset=0);
		if ($return === false) {
			return false;
		} else {
			return true;
		}
	}
}

// Additional security (Union, CLike, XSS)
if(isset($_SERVER['QUERY_STRING']) && (!stripos_clone($_SERVER['QUERY_STRING'], "ad_click"))) {
	$queryString = $_SERVER['QUERY_STRING'];
    if (stripos_clone($queryString,'%20union%20') OR (stripos_clone($queryString,'*%2f*')) OR stripos_clone($queryString,'/*') OR stripos_clone($queryString,'*/union/*') OR stripos_clone($queryString,'c2nyaxb0') OR stripos_clone($queryString,'+union+') OR (stripos_clone($queryString,'cmd=') AND !stripos_clone($queryString,'&cmd')) OR (stripos_clone($queryString,'exec') AND !stripos_clone($queryString,'execu')) OR stripos_clone($queryString,'concat')) {
    	die('Illegal Operation');
    }
}



if(!defined("SQL_LAYER"))
{
define("SQL_LAYER","mysql");

class sql_db
{
	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;

	//
	// Constructor
	//
	function sql_db($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true)
	{

		$this->persistency = $persistency;
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;

		if($this->persistency)
		{
			$this->db_connect_id = @mysql_pconnect($this->server, $this->user, $this->password);
		}
		else
		{
			$this->db_connect_id = @mysql_connect($this->server, $this->user, $this->password);
		}
		

		if($this->db_connect_id)
		{
			if($database != "")
			{
				$this->dbname = $database;
				$dbselect = @mysql_select_db($this->dbname);
				if(!$dbselect)
				{
					@mysql_close($this->db_connect_id);
					$this->db_connect_id = $dbselect;
				}
			}
			return $this->db_connect_id;
		}
		else
		{
			return false;
		}
	}

	//
	// Other base methods
	//
	function sql_close()
	{
		if($this->db_connect_id)
		{
			if($this->query_result)
			{
				@mysql_free_result($this->query_result);
			}
			$result = @mysql_close($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}

	//
	// Base query method
	//
	function sql_query($query = "", $transaction = FALSE)
	{
		// Remove any pre-existing queries
		unset($this->query_result);
		if($query != "")
                {

			$this->query_result = @mysql_query($query, $this->db_connect_id);

		}
		if($this->query_result)
		{

			$this->num_queries += 1;
			$this->text_queries.="<BR>".$query."";
		
			unset($this->row[$this->query_result]);
			unset($this->rowset[$this->query_result]);
			return $this->query_result;
		}
		else
		{
			return ( $transaction == END_TRANSACTION ) ? true : false;
		}
	}

	//
	// Other query methods
	//
	function sql_numrows($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_num_rows($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_affectedrows()
	{
		if($this->db_connect_id)
		{
			$result = @mysql_affected_rows($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_numfields($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_num_fields($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldname($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_field_name($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldtype($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_field_type($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrow($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$this->row[$query_id] = @mysql_fetch_array($query_id);
			return $this->row[$query_id];
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrowset($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			unset($this->rowset[$query_id]);
			unset($this->row[$query_id]);
			while($this->rowset[$query_id] = @mysql_fetch_array($query_id))
			{
				$result[] = $this->rowset[$query_id];
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fetchfield($field, $rownum = -1, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			if($rownum > -1)
			{
				$result = @mysql_result($query_id, $rownum, $field);
			}
			else
			{
				if(empty($this->row[$query_id]) && empty($this->rowset[$query_id]))
				{
					if($this->sql_fetchrow())
					{
						$result = $this->row[$query_id][$field];
					}
				}
				else
				{
					if($this->rowset[$query_id])
					{
						$result = $this->rowset[$query_id][$field];
					}
					else if($this->row[$query_id])
					{
						$result = $this->row[$query_id][$field];
					}
				}
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_rowseek($rownum, $query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_data_seek($query_id, $rownum);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_nextid(){
		if($this->db_connect_id)
		{
			$result = @mysql_insert_id($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_freeresult($query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}

		if ( $query_id )
		{
			unset($this->row[$query_id]);
			unset($this->rowset[$query_id]);

			@mysql_free_result($query_id);

			return true;
		}
		else
		{
			return false;
		}
	}
	function sql_error($query_id = 0)
	{
		$result["message"] = @mysql_error($this->db_connect_id);
		$result["code"] = @mysql_errno($this->db_connect_id);

		return $result;
	}

}
} 

$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);

?>
