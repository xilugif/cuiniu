<?php
class BDbConnection
{
    public $connectionString;
    public $username;
    public $password;
    public $pdoClass = 'PDO';

    private $_attributes=array();
    private $_active=false;
    private $_pdo;
    private $_transaction;
    private $_schema;

    public function __construct($dsn = '', $username = '', $password = '')
    {
        $this->connectionString = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Opens DB connection if it is currently not
     * @throws CException if connection fails
     */
    protected function open()
    {
        if($this->_pdo === null)
        {
            if(empty($this->connectionString))
            {
                throw new BException('DbConnection.connectionString cannot be empty.');
            }
            try
            {
                $this->_pdo=$this->createPdoInstance();
                $this->initConnection($this->_pdo);
                $this->_active=true;
            }
            catch(PDOException $e)
            {
                 throw new BDbException('CDbConnection failed to open the DB connection.',(int)$e->getCode(), $e->errorInfo);
            }
        }
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     */
    protected function close()
    {
        $this->_pdo = null;
        $this->_active = false;
        $this->_schema = null;
    }

    /**
     * Creates the PDO instance.
     * When some functionalities are missing in the pdo driver, we may use
     * an adapter class to provides them.
     * @return PDO the pdo instance
     */
    protected function createPdoInstance()
    {
        $pdoClass=$this->pdoClass;
        //driver
        if(($pos = strpos($this->connectionString, ':')) !== false)
        {
            $driver = strtolower(substr($this->connectionString, 0, $pos));
        }

        return new $pdoClass($this->connectionString, $this->username,
                    $this->password, $this->_attributes);
    }

    /**
     * Initializes the open db connection.
     * This method is invoked right after the db connection is established.
     * The default implementation is to set the charset for MySQL and PostgreSQL database connections.
     * @param PDO $pdo the PDO instance
     */
    protected function initConnection($pdo)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if($this->emulatePrepare !== null && constant('PDO::ATTR_EMULATE_PREPARES'))
        {
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $this->emulatePrepare);
        }

        if($this->charset !== null)
        {
            $driver=strtolower($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));

            if(in_array($driver, array('pgsql', 'mysql', 'mysqli')))
            {
                $pdo->exec('SET NAMES ' . $pdo->quote($this->charset));
            }
        }

        if($this->initSQLs !== null)
        {
            foreach($this->initSQLs as $sql)
            {
                $pdo->exec($sql);
            }
        }
    }
}