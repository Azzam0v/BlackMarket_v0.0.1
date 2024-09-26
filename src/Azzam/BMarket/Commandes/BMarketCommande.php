<?php

namespace Azzam\BMarket\Commandes;

use Azzam\BMarket\BlackMarketMenus;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\player\Player;

class BMarketCommande extends Command
{
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission(DefaultPermissionNames::COMMAND_HELP);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if ($player instanceof Player){
            if ($player->hasPermission("bmarket.perm"))
            {
                BlackMarketMenus::getInstance()->BuyForm($player);
            }else{
                BlackMarketMenus::getInstance()->MainForm($player);
            }
        }
    }

}