<?php

namespace Core;

use PDO;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;
        
        if ($db === null) {
            $dsn = 'mysql:host=' . getenv('DATABASE_HOST') . ';dbname=' . getenv('DATABASE_NAME') . ';charset=utf8';
            $db = new PDO($dsn, getenv('DATABASE_USER'), getenv('DATABASE_PASSWORD'));

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}
