<?php

namespace LadinoXx\pay2ply\Commands;

use LadinoXx\pay2ply\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Exception;
use pocketmine\console\ConsoleCommandSender;

class ActivedCommand extends Command {
    
    public $plugin;

    public function __construct(Main $plugin, string $name, string $description, array $alis)
    {
        $this->plugin = $plugin;
        parent::__construct($name, $description, null, $alis);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $dispenses = null;
            try {
                $dispenses = $this->plugin->getSdk()->getDispenses();
            } catch (Exception $e) {
                $this->plugin->getServer()->getLogger->erro((string) $e);
            }
            if ($dispenses == null) {
                $sender->sendMessage($this->plugin->config->get("fail-resgate", ""));
                return;
            }
            foreach($dispenses as $dsp) {
                if ($dsp->is_actived != 1 and strtolower($dsp->username) == strtolower($sender->getName())) {
                    try {
                        $this->plugin->getSdk()->update($dsp->username, $dsp->id);
                        $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender($this->plugin->getServer(), $this->plugin->getServer()->getLanguage()), str_replace("%username%", $dsp->username, $dsp->command));
                        $sender->sendMessage($this->plugin->config->get("sucess-resgate", ""));
                        return;
                    } catch (Exception $e) {
                        $this->plugin->getServer()->getLogger->info((string) $e);
                        return;
                    }
                }
            }
            $sender->sendMessage($this->plugin->config->get("fail-resgate", ""));
        }
    }
}