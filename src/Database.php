<?php

namespace src;

class Database {

    /**
     * Database connexion state
     *
     * @var bool
     * @access private
     */
    private bool $connected = false;
    /**
     * Database selected state
     *
     * @var bool
     * @access private
     */
    private bool $database_selected = false;

    /**
     * Errors list
     *
     * @var array
     * @access private
     */
    private array $errors = array();
    /**
     * Last error occured
     *
     * @var mixed
     * @access private
     */
    private mixed $last_error;

    /**
     * Selected database
     *
     * @var string
     * @access private
     */
    private string $database;
    /**
     * Database tables prefix
     *
     * @var string
     * @access private
     */
    private string $prefix = 'gpt_';

    /**
     * Last query send
     *
     * @var string
     * @access private
     */
    private string $last_query;
    /**
     * Last result from query
     *
     * @var mixed
     * @access private
     */
    private mixed $last_result;
    /**
     * Last binding from last query
     *
     * @var mixed
     * @access private
     */
    private $last_binding;
    /**
     * Last parameters binded from last query
     *
     * @var mixed
     * @access private
     */
    private mixed $last_parameters;

    /**
     * Database connexion (mysqli)
     *
     * @var mixed
     * @access private
     */
    protected mixed $db;

    /**
     * @name __construct
     * @description Class Constructor
     * @access public
     *
     * @param string $host Database Host
     * @param string $user Database User
     * @param string $password Database Password
     * @param string $database Database Name
     *
     * @return false|null Return false on error
     */
    public function __construct( $host='', $user='', $password='', $database='' ) {
        if( ! class_exists( 'mysqli' ) ) $this->set_error( 'MySQLi Drive not available on this version of PHP.' );
        mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT );
        if(
            null === $host ||
            null === $user ||
            null === $password
        ) $this->set_error( 'Please fill all fields.' );
        if( ! empty( $this->errors ) ) return false;
        $this->connect( $host, $user, $password, $database );
    }

    /**
     * @name set_error
     * @description Set last error and add theme to the errors list
     * @access private
     *
     * @param string $text The error text
     * @param int $number Error number
     *
     * @return false|null Return false on error
     */
    private function set_error( $text, $number=0 ) {
        if( empty( $text ) ) return false;
        array_push( $this->errors, array(
            'message' => $text,
            'number' => $number
        ) );
        $last_error = end( $this->errors );
    }

    /**
     * @name init
     * @description Init the Database connexion
     * @access private
     *
     * @param string $database Database name
     *
     * @return null
     */
    private function init( $database ) {
        if( $this->db->connect_errno ) $this->set_error( $this->db->connect_error );
        else $this->connected = true;
        if( $this->connected ) {
            if( $this->select_database( $database ) ) {
                $this->database = $database;
                $this->database_selected = true;
            } else $this->set_error( "Could not connect to database '$database'" );
        }
    }

    /**
     * @name connect
     * @description Create connexion to the database
     * @access public
     *
     * @param string $host Database Host
     * @param string $user Database User
     * @param string $password Database Password
     * @param string $database Database Name
     *
     * @return mixed
     */
    public function connect( $host, $user, $password, $database ) {
        try {
            $this->db = new \mysqli( $host, $user, $password, '' );
        } catch( Exception $e ){
            $this->connected = false;
            return false;
        }
        $this->init( $database );
        return $this->db;
    }

    /**
     * @name select_database
     * @description Select the database to works on
     * @access public
     *
     * @param string $database Database Name
     *
     * @return bool $state Database selectioned state
     */
    public function select_database( $database ) {
        return $this->db->select_db( $database );
    }

    /**
     * @name query
     * @description Query the databse
     * @access public
     *
     * @param string $sql The query to execute
     *
     * @return false|mixed $result Mysqli query result
     */
    public function query( $sql ) {
        if( empty( $sql ) || ! $this->db ) return false;
        echo '<br>' . $sql . '<br>';
        $result = $this->db->query( $sql );
        if( ! $result ) $this->set_error( $this->db->error, $this->db->errno );
        $this->last_query = $sql;
        $this->last_result = $result;
        return $result ? $result : false;
    }

    /**
     * Check if specified table exists onto Database
     *
     * @param string $table
     * @return boolean
     */
    protected function check_table( string $table ) : bool {
        if( ! $this->is_connected() ) return false;
        $result = $this->query( "CHECK TABLE `$table`" );
        if( $result->num_rows == 0 ) return false;
        $row = $result->fetch_assoc();
        if( strtolower( $row[ 'Msg_text' ] ) !== 'ok' ) return false;
        return true;
    }

    /**
     * Run prepared mysqli query (Return 0 when no rows affected)
     *
     * @param string $statement
     * @param string|null $binding
     * @param mixed ...$parameters
     * @return mixed|null
     */
    public function prepared_query( string $statement, ?string $binding=null, mixed ...$parameters ) : mixed {
        if( ! $this->connected ) return null;
        preg_match( '/' . $this->prefix . '\w+/i', $statement, $match );
        if( empty( $match ) && ! $this->check_table( end( $match ) ) ) return null;
        if( $binding === null ) $result = $this->db->query( $statement );
        else {
            $state = $this->db->prepare( $statement );
            $state->bind_param( $binding, ...$parameters );
            if( ! $state->execute() ) return null;
            $result = $state->get_result();
            if( str_contains( $statement, 'UPDATE' ) && $this->db->affected_rows === 0 ) return 0;
            else if( str_contains( $statement, 'UPDATE' ) && $this->db->affected_rows > 0 ) return $this->db->affected_rows;
        }
        $this->last_query = $statement;
        if( isset( $binding ) ) $this->last_binding = $binding;
        if( isset( $parameters ) ) $this->last_parameters = $parameters;
        $this->last_result = $result;
        if( ! preg_match( '/INSERT INTO/i', $statement ) ) {
            if( $result->num_rows == 0 ) return null;
            $return = array();
            while( $row = $result->fetch_assoc() ) array_push( $return, $row );
            return $return;
        }
        return $result;
    }

    /**
     * @name query_file
     * @description Query the database with a SQL file
     * @access public
     *
     * @param string $path The SQL filepath to execute
     *
     * @return false|mixed $result Mysqli query result
     */
    public function query_file( $path ) {
        if( ! @file_exists( $path ) ) {
            $this->set_error( "File '$path' dosen't exists." );
            return false;
        }
        $sql = file_get_contents( $path );
        if( empty( $sql ) ) return false;
        $result = $this->db->multi_query( $sql );
        while( $this->db->next_result() ){;}
        if( ! $result ) $this->set_error( $this->db->error, $this->db->errno );
        $this->last_query = $sql;
        $this->last_result = $result;
        return $result ? $result : false;
    }

    /**
     * Run multiple queries at once
     *
     * @param string $sql
     * @return void
     */
    public function multiple_query( string $sql ) {
        if( empty( $sql ) ) return false;
        $result = $this->db->multi_query( $sql );
        while( $this->db->next_result() ){;}
        if( ! $result ) $this->set_error( $this->db->error, $this->db->errno );
        $this->last_query = $sql;
        $this->last_result = $result;
        return $result ? $result : false;
    }

    /**
     * @name get_last_query
     * @description Get the last query executed
     * @access public
     *
     *
     * @return string $query The last query executed
     */
    public function get_last_query() : string {
        return $this->last_query;
    }

    /**
     * @name get_last_binding
     * @description Get the last binding from last query executed
     * @access public
     *
     *
     * @return mixed $bindin The last binding for last query executed
     */
    public function get_last_binding() : mixed {
        return $this->last_binding;
    }

    /**
     * @name get_last_parameters
     * @description Get the last parameters from last query executed
     * @access public
     *
     *
     * @return mixed $bindin The last parameters for last query executed
     */
    public function get_last_parameters() : mixed {
        return $this->last_parameters;
    }

    /**
     * @name get_last_result
     * @description Get the last result from last query executed
     * @access public
     *
     *
     * @return mixed $result The result from the last Databse request
     */
    public function get_last_result() {
        return $this->last_result;
    }

    /**
     * @name get_last_error
     * @description Get the last error from the errors list
     * @access public
     *
     *
     * @return array $error The last error occured
     */
    public function get_last_error() {
        return $this->last_error;
    }

    /**
     * Check if the instance is connected to Database
     *
     * @return boolean
     */
    public function is_connected() : bool {
        return $this->connected;
    }

    /**
     * Get last inserted ID of the Database
     *
     * @return integer|null
     */
    public function get_last_id() : ?int {
        if( ! $this->is_connected() ) return null;
        return mysqli_insert_id( $this->db );
    }

}

// Silence is golden
