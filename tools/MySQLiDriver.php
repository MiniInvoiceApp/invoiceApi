<?php

class MySQLiDriver
{
    private $dbLink;
    private static $instance = null;

    private function __construct()
    {
        $this->dbLink = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

        if (mysqli_connect_error()) {
            throw new ErrorException("Error: Could not make a database link (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
        }

        $this->dbLink->set_charset("utf8");
    }

    /**
     * @return MySQLiDriver
     * @throws ErrorException
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new MySQLiDriver();
        }

        return self::$instance;
    }

    /**
     * @param $sql
     * @return stdClass
     * @throws ErrorException
     */
    public function query($sql)
    {
        $query = $this->dbLink->query($sql);

        if (!$this->dbLink->errno) {
            $data = [];
            if (isset($query->num_rows)) {
                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                $result = new stdClass();
                $result->num_rows = $query->num_rows;
                $result->rows = $data;

                $query->free();
            } else {
                $result = true;
            }
        } else {
            throw new ErrorException("Error: {$this->dbLink->error} Error No: {$this->dbLink->errno} Query: $sql");
        }

        return $result;
    }

    /**
     * @param $value
     * @return string
     */
    public function escape($value)
    {
        return $this->dbLink->real_escape_string($value);
    }

    /**
     * @return mixed
     */
    public function getLastId()
    {
        return $this->dbLink->insert_id;
    }

    public function __destruct()
    {
        $this->dbLink->close();
    }
}
