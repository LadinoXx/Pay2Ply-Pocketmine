<?php

namespace LadinoXx\pay2ply\Commands;

use LadinoXx\pay2ply\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Pay2PlyCommand extends Command {

    public $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("pay2ply", "Vincular a sua loja");
        $this->setPermission("pay2ply.perm");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!isset($args[0])) {
            $sender->sendMessage("§cUse : /pay2ply <token>");
          }
          $token = strtolower($args[0]);
          $this->plugin->getSdk()->setToken($token);
          $this->plugin->config->set("token", $token);
          $this->plugin->config->save();
          $sender->sendMessage("§aSua loja foi vinculada.");
    }
}