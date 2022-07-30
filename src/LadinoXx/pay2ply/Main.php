<?php

namespace LadinoXx\pay2ply;

use LadinoXx\pay2ply\Commands\ActivedCommand;
use LadinoXx\pay2ply\Commands\Pay2PlyCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use LadinoXx\pay2ply\Sdk\Sdk;
use pocketmine\utils\SingletonTrait;


class Main extends PluginBase {
  
  use SingletonTrait;
  
  public $config;
  
  private ?Sdk $sdk = null;
  
  public function onEnable() : void {
    @mkdir($this->getDataFolder());
    $this->saveDefaultConfig();
    $this->config = $this->getConfig();
    $this->sdk = new Sdk();
    $this->sdk->setToken($this->config->get("token"));
    if ($this->config->get("token") != "") {
      $this->getServer()->getLogger()->info("§aSe tudo estiver nos conformes, o servidor será vinculado em segundos.");
    }else{
      $this->getServer()->getLogger()->info("§aUse /pay2ply <token> para colocar o token da sua loja.");
    }
    $this->getServer()->getCommandMap()->registerAll("pay2ply", [
      new Pay2PlyCommand($this),
      new ActivedCommand($this, $this->config->getNested("resgatar-command.command", "resgatar"), $this->config->getNested("resgatar-command.description", "Use para resgatar seu vip "), $this->config->getNested("resgatar-command.aliases", ["vip"]))
    ]);
  }
  
  public function getSdk() : ?Sdk {
    return $this->sdk;
  }
  
}
