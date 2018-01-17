<?php

namespace awzaw\notp;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class Main extends PluginBase implements Listener {

    private $enabled;

    public function onEnable() {
        $this->enabled = [];
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $issuer, Command $cmd, string $label, array $args) : bool{
        if ((strtolower($cmd->getName()) == "notp") && !(isset($args[0])) && ($issuer instanceof Player) && ($issuer->hasPermission("notp.toggle") || $issuer->hasPermission("notp.toggle.self"))) {
            if (isset($this->enabled[strtolower($issuer->getName())])) {
                unset($this->enabled[strtolower($issuer->getName())]);
            } else {
                $this->enabled[strtolower($issuer->getName())] = strtolower($issuer->getName());
            }

            if (isset($this->enabled[strtolower($issuer->getName())])) {
                $issuer->sendMessage("NoTP mode enabled!");
            } else {
                $issuer->sendMessage("NoTP mode disabled!");
            }
            return true;
        } else {
            return false;
        }
    }

    public function onPlayerCommand(PlayerCommandPreprocessEvent $event) {
        if ($event->isCancelled()) return;
        $message = $event->getMessage();
        if (strtolower(substr($message, 0, 3)) == "/tp" || strtolower(substr($message, 0, 5)) == "/call" || strtolower(substr($message, 0, 7)) == "/tphere" || strtolower(substr($message, 0, 4)) == "/tpa") {
            $args = explode(" ", $message);
            if (!isset($args[1])) {
                return;    
            }
            $sender = $event->getPlayer();

            foreach ($this->enabled as $notpuser) {

                if ((strpos(strtolower($notpuser), strtolower($args[1])) !== false) && (strtolower($notpuser) !== strtolower($sender->getName()))) {
                    $sender->sendMessage(TextFormat::RED . "§5This Player Is Not Accepting TP");
                    $event->setCancelled(true);
                    return;
                }

                if (isset($args[2]) && strpos(strtolower($notpuser), strtolower($args[2])) !== false && (strtolower($notpuser) !== strtolower($sender->getName()))) {
                    $sender->sendMessage(TextFormat::RED . "§5This Player Is Not Accepting TP");
                    $event->setCancelled(true);
                    return;
                }
            }
        }
    }
}
