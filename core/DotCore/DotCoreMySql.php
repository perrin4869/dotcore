<?php

/**
 * Exception thrown whenever there's an error with any SQL processed by DotCoreMySql
 * @author perrin
 *
 */
class MySqlException extends Exception 
{ 
	private $query;

	public function __construct($message = '', $query ='')
	{
		if(empty($message))
		{
			$message = 'MySqlException';
		}
		if(!empty($query)) {
			$message = 'Query:<br />' . $query . '<br />Error: <br />' . $message;
		}
		parent::__construct($message);
		$this->query = $query;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function setQuery($query)
	{
		$this->query = $query;
	}
}

/**
 * Encapsulates common MySQL Tasks inside the classes in DotCore
 * @author perrin
 *
 */
class DotCoreMySql extends DotCoreObject
{
	public function __construct()
	{

	}

	public function __destruct()
	{
		if($this->result != NULL)
		{
			$this->FreeResult();
		}
	}

	/**
	 * Holds the MySql connection for the queries to come
	 * @var mysqli
	 */
	private static $conn = NULL;

	private static $username = NULL;
	private static $password = NULL;
	private static $host = NULL;
	private static $database = NULL;

	/**
	 * Sets the connection data for global connectivity for this class
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 */
	public static function SetConnectionData($host, $username, $password, $database = NULL)
	{
		self::$host = $host;
		self::$username = $username;
		self::$password = $password;
		self::$database = $database;
	}

	/**
	 * Sets the database which recieves the queries
	 * @param string $database
	 */
	public static function SetDatabase($database)
	{
		self::$database = $database;
	}

	/**
	 * Holds the result of the current query
	 * @var mysqli_result
	 */
	private $result = NULL;
	
	/**
	 * Returns a valid mysql connection.
	 * @return mysqli
	 * @throws Exception on failure to connect to the database or to select the database
	 */
	public static function GetConnection()
	{
		if(self::$conn == NULL)
		{
			self::OpenConnection();
		}

		return self::$conn;
	}

	protected static function OpenConnection()
	{
		$conn = new mysqli(self::$host, self::$username, self::$password, self::$database);

		/* check connection */
		if (mysqli_connect_errno()) {
			throw new MySqlException('Connection to Database Failed.');
		}
		
		if(!$conn->set_charset('utf8'))
		{
			throw new MySqlException($conn->error);
		}
		self::$conn = $conn;
	}
	
	public static function CloseConnection()
	{
		if(self::$conn != NULL)
		{
			self::$conn->close();
		}
	}

	/**
	 * If an error is found from the last query, it throws it as an Exception
	 * @throws Exception on error found, with the message from the error
	 */
	private function ThrowError($query)
	{
		$error = self::$conn->error;
		if(empty($error))
		{
			$error = NULL;
		}
		throw new MySqlException($error, $query);
	}

	/**
	 * Method used to execute queries, for internal uses
	 *
	 * @param string $sql
	 */
	private static function ExecuteSQL($sql) {
		return self::$conn->query($sql);
	}

	/**
	 * Executes $query, on failure it'll try to reestablish connection with the database, and try again
	 * If it fails the second time, and MySqlException is thrown
	 * @param string $query
	 * @param int $i
	 * 
	 * @return mysqli_result
	 */
	protected function Execute($query, $i = 0)
	{
		$result = self::ExecuteSQL($query);

		$error = $this->GetConnection()->error;
		if(!empty($error))
		{
			if($i == 0)
			{
				// Try reopening connection, and execute again
				// OpenConnection will not establish a new connection, it'll reopen the old one
				self::OpenConnection();
				$this->Execute($query, $i+1);
			}
			else
			{
				$this->ThrowError($query);
			}
		}

		return $result;
	}
	
	/**
	 * Performs query $query, and returns the result
	 * @param String $query
	 * @return mysql_query_result
	 * @throws Exception on query error
	 */
	public function PerformQuery($query)
	{
		$conn = $this->GetConnection();
		$result = $this->Execute($query);

		if($this->result != NULL)
		{
			$this->FreeResult();
		}

		$this->result = $result;
	}
	
	/**
	 * Perfoms the insertion in query $query
	 * @param String $query
	 * @return the inserted ID
	 * @throws Exception if query fails
	 */
	public function PerformInsert($query)
	{
		$conn = $this->GetConnection();
		$this->ExecuteSQL($query);

		return $conn->insert_id;
	}

	/**
	 * Perfoms the update in query $query
	 * @param String $query
	 * @throws Exception if query fails
	 */
	public function PerformUpdate($query)
	{
		$conn = $this->GetConnection();
		$this->ExecuteSQL($query);
	}

	/**
	 * Perfoms the delete in query $query
	 * @param String $query
	 * @throws Exception if query fails
	 */
	public function PerformDelete($query)
	{
		$conn = $this->GetConnection();
		$this->ExecuteSQL($query);
	}

	/**
	 * Gets the number of affected rows from the last query
	 * @return int
	 */
	public function GetAffectedRows()
	{
		$conn = $this->GetConnection();
		return $conn->num_rows;
	}

	/**
	 * Fetches the next row inside $result
	 * @param mysql_query_result $result
	 * @return mysql_query_row
	 */
	public function FetchRow()
	{
		return $this->result->fetch_assoc();
	}

	/**
	 * Performs a scalar operation, like COUNT or SUM inside $query, and returns the result
	 * @param $query
	 * @return mixed
	 */
	public function PerformScalar($query)
	{
		$conn = $this->GetConnection();
		$result = $this->Execute($query);

		$count = $result->fetch_row();
		$result->free();
		$result = NULL;
		return $count[0];
	}

	public function FreeResult()
	{
		$this->result->free();
		$this->result = NULL;
	}

	// Transaction Methods

	public static function BeginTransaction() {
		// self::ExecuteSQL('BEGIN');
		// Nothing to do, really
		self::GetConnection()->autocommit(FALSE);
	}

	public static function Rollback() {
		// self::ExecuteSQL('ROLLBACK');
		$conn = self::GetConnection();
		$conn->rollback();
		$conn->autocommit(TRUE);
	}

	public static function CommitTransaction() {
		// self::ExecuteSQL('COMMIT');
		$conn = self::GetConnection();
		$conn->commit();
		$conn->autocommit(TRUE);
	}
}

?>