<?php
namespace Util\Config;

class LoadConfig
{
    private static $instance;

    private $type;
    private $host;
    private $port;
    private $name;
    private $user;
    private $pass;

    private function __construct()
    {
        $file = file_get_contents( "../app/database/config.json" );
        $config = json_decode( $file, true );

        $this->type = $config[ "type" ];
        $this->host = $config[ "host" ];
        $this->port = $config[ "port" ];
        $this->name = $config[ "name" ];
        $this->user = $config[ "user" ];
        $this->pass = $config[ "pass" ];
    }

    public static function get()
    {
        if ( empty( self::$instance ) ) {
            self::$instance = new LoadConfig();
        }

        return self::$instance;
    }

    public function getType() { return $this->type; }

    public function getHost() { return $this->host; }

    public function getPort() { return $this->port; }

    public function getName() { return $this->name; }

    public function getUser() { return $this->user; }

    public function getPass() { return $this->pass; }

}
