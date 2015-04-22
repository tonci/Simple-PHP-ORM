<?php
#This is base class representing sql adapters model.

namespace SimpleORM\Adapters;

abstract class Adapter {
    # Connection host name
    public $host = 'localhost';
    # Connection username
    public $user;
    # Connection password
    public $password;
    # Connection database
    public $database;
    # Charset to be used
    public $charset = 'utf8';

    # If we have allready made a connection to the database, keep it here
    protected static $instance;

    abstract function connect();
}