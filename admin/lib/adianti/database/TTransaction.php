<?php
namespace Adianti\Database;

use Adianti\Database\TConnection;
use Adianti\Log\TLogger;
use Adianti\Log\AdiantiLoggerInterface;
use PDO;
use Closure;

/**
 * Manage Database transactions
 *
 * @version    5.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
final class TTransaction
{
    private static $conn;     // active connection
    private static $logger;   // Logger object
    private static $database; // database name
    private static $dbinfo;   // database info
    private static $counter;
    
    /**
     * Class Constructor
     * There won't be instances of this class
     */
    private function __construct(){}
    
    /**
     * Open a connection and Initiates a transaction
     * @param $database Name of the database (an INI file).
     * @param $dbinfo Optional array with database information
     */
    public static function open($database, $dbinfo = NULL)
    {
        if (!isset(self::$counter))
        {
            self::$counter = 0;
        }
        else
        {
            self::$counter ++;
        }
        
        if ($dbinfo)
        {
            self::$conn[self::$counter] = TConnection::openArray($dbinfo);
            self::$dbinfo[self::$counter] = $dbinfo;
        }
        else
        {
            self::$conn[self::$counter] = TConnection::open($database);
            self::$dbinfo[self::$counter] = TConnection::getDatabaseInfo($database);
        }
        self::$database[self::$counter] = $database;
        
        $driver = self::$conn[self::$counter]->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver !== 'dblib')
        {
            // begins transaction
            self::$conn[self::$counter]->beginTransaction();
        }
        
        if (!empty(self::$dbinfo[self::$counter]['slog']))
        {
            $logClass = self::$dbinfo[self::$counter]['slog'];
            if (class_exists($logClass))
            {
                self::setLogger(new $logClass);
            }
        }
        else
        {
            // turn OFF the log
            self::$logger[self::$counter] = NULL;
        }
    }
    
    /**
     * Returns the current active connection
     * @return PDO
     */
    public static function get()
    {
        if (isset(self::$conn[self::$counter]))
        {
            return self::$conn[self::$counter];
        }
    }
    
    /**
     * Rollback all pending operations
     */
    public static function rollback()
    {
        if (self::$conn[self::$counter])
        {
            $driver = self::$conn[self::$counter]->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver !== 'dblib')
            {
                // rollback
                self::$conn[self::$counter]->rollBack();
            }
            self::$conn[self::$counter] = NULL;
            self::$counter --;
        }
    }
    
    /**
     * Commit all the pending operations
     */
    public static function close()
    {
        if (self::$conn[self::$counter])
        {
            $driver = self::$conn[self::$counter]->getAttribute(PDO::ATTR_DRIVER_NAME);
            $info = self::getDatabaseInfo();
            $fake = isset($info['fake']) ? $info['fake'] : FALSE;
            
            if ($driver !== 'dblib' AND !$fake)
            {
                // apply the pending operations
                self::$conn[self::$counter]->commit();
            }
            self::$conn[self::$counter] = NULL;
            self::$counter --;
        }
    }
    
    /**
     * Assign a Logger closure function
     * @param $logger A Closure
     */
    public static function setLoggerFunction(Closure $logger)
    {
        if (isset(self::$conn[self::$counter]))
        {
            self::$logger[self::$counter] = $logger;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__);
        }
    }
    
    /**
     * Assign a Logger strategy
     * @param $logger A TLogger child object
     */
    public static function setLogger(AdiantiLoggerInterface $logger)
    {
        if (isset(self::$conn[self::$counter]))
        {
            self::$logger[self::$counter] = $logger;
        }
        else
        {
            // if there's no active transaction opened
            throw new Exception(AdiantiCoreTranslator::translate('No active transactions') . ': ' . __METHOD__);
        }
    }
    
    /**
     * Write a message in the LOG file, using the user strategy
     * @param $message Message to be logged
     */
    public static function log($message)
    {
        // check if exist a logger
        if (self::$logger[self::$counter])
        {
            $log = self::$logger[self::$counter];
            
            // avoid recursive log
            self::$logger[self::$counter] = NULL;
            
            if ($log instanceof AdiantiLoggerInterface)
            {
                // call log method
                $log->write($message);
            }
            else if ($log instanceof Closure)
            {
                $log($message);
            }
            
            // restore logger
            self::$logger[self::$counter] = $log;
        }
    }
    
    /**
     * Return the Database Name
     */
    public static function getDatabase()
    {
        return self::$database[self::$counter];
    }
    
    /**
     * Returns the Database Information
     */
    public static function getDatabaseInfo()
    {
        return self::$dbinfo[self::$counter];
    }
}
