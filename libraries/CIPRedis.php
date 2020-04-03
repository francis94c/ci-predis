<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(__DIR__ . '/../vendor/autoload.php');

use Predis\Autoloader;
use Predis\Client;

class CIPRedis
{
  /**
   * [private description]
   * @var [type]
   */
  private $config = [];

  /**
   * [RedisServer Instance]
   * @var CI_Predis\RedisServer
   */
  private $client;

  /**
   * [RedisServerCollection Instance]
   * @var array
   */
  private $clients = [];

  /**
   * Redis constructor.
   * @param  array|null $params
   */
  public function __construct(?array $config=null)
  {
    Autoloader::register();
    if ($config) {
      $this->config = $config;
      if (isset($config['default_server'])) {
        $this->connect($config['default_server']);
      }
    }
  }

  /**
   * [connect description]
   * @date   2020-04-01
   * @param  string     $serverName [description]
   * @return [type]                 [description]
   */
  public function connect(string $serverName)
  {
    if(!isset($this->config['servers'][$serverName])) {
      throw new Exception('Configuration for requested Redis Server not found, given: ' . $serverName);
    }
    $this->client = new Client($this->config['servers'][$serverName]);
    $this->clients[$serverName] = $this->client;
  }

  /**
   * [getClient description]
   * @date   2020-04-01
   * @return Predis\Client [description]
   */
  public function getClient():Predis\Client
  {
    return $this->client;
  }

  /**
   * [getClients description]
   * @date   2020-04-01
   * @return array [description]
   */
  public function getClients():array
  {
    return $this->clients;
  }

  /**
   * [__call description]
   * @date   2020-04-02
   * @param  string $name      [description]
   * @param  array $arguments [description]
   * @return mixed            [description]
   */
  public function __call(string $name, array $arguments)
  {
    return call_user_func_array([$this->client, $name], $arguments);
  }

  /**
   * [__get description]
   * @date   2020-04-02
   * @param  string     $name [description]
   * @return [type]           [description]
   */
  public function __get(string $name)
  {
    return $this->client->$name;
  }

  /**
   * [__set description]
   * @date  2020-04-02
   * @param string     $name  [description]
   * @param [type]     $value [description]
   */
  public function __set(string $name, $value)
  {
    return $this->client->$name = $value;
  }
}
