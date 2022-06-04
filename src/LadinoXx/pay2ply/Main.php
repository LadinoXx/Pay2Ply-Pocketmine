<?php

namespace LadinoXx\pay2ply;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\scheduler\Task;
use pocketmine\console\ConsoleCommandSender;
use LadinoXx\pay2ply\Sdk\Sdk;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class OwnerTask extends Task
{
  
  private $plugin;
  
  public function __construct($plugin) {
    $this->plugin = $plugin;
  }
  
  public function onRun() : void {
    $dispenses = null;
    try {
      $dispenses = $this->plugin->getSdk()->getDispenses();
    } catch (Exception $e) {
      $this->getServer()->getLogger->erro((string) $e);
    }
    if ($dispenses == null) {
      return;
    }
    foreach($dispenses as $dsp) {
      if ($dsp->is_actived != 1) {
        $player = $this->getServer()->getPlayerByPrefix($dsp->username);
        if ($player instanceof Player) {
          try {
            $this->plugin->getSdk()->update($dsp->username, $dsp->id);
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), str_replace("%username%", $dsp->username, $dsp->command));
            $player->sendTitle($this->plugin->config->get("title"));
            if ($this->plugin->config->get("message")) $this->getServer()->getLogger()->info(str_replace("{player}", $dsp->username, "§a[Pay2Ply] O proproduto {player} foi ativo."));
          } catch (Exception $e) {
            $this->getServer()->getLogger->info((string) $e);
          }
        }
      }
    }
  }
  
  private function getServer() : Server {
    return $this->plugin->getServer();
  }
  
}


class Main extends PluginBase implements Listener {
  
  use SingletonTrait;
  
  public $config;
  
  private ?Sdk $sdk = null;
  
  public function onEnable() : void {
    @mkdir($this->getDataFolder());
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    $this->sdk = new Sdk();
    $this->sdk->setToken($this->config->get("token"));
    if ($this->config->get("token") != "") $this->getServer()->getLogger()->info("§aSe tudo estiver nos conformes, o servidor será vinculado em segundos.");
    if ($this->config->get("token") != "") $this->getScheduler()->scheduleRepeatingTask(new OwnerTask($this), 60 * 20, 20 * 20);
    
    $this->getServer()->getLogger()->info("§a[Pay2Ply] Ligado, feito por LADINO#0001");
  }
  
  public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool {
    switch ($command->getName()) {
      case "pay2ply":
        if (!isset($args[0])) {
          $player->sendMessage("§cUse : /pay2ply <token>");
        }
        $token = strtolower($args[0]);
        $this->sdk->setToken($token);
        $this->config->set("token", $token);
        $this->config->save();
        $player->sendMessage("§aSe tudo estiver nos conformes, a loja será vinculada após o servidor ser reiniciado");
        break;
      }
    return true;
  }
  
  public function getSdk() : ?Sdk {
    return $this->sdk;
  }
  
}
