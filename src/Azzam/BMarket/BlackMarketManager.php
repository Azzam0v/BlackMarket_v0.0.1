<?php

namespace Azzam\BMarket;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\lang\Language;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\player\Player;
use Terpz710\EconomyPE\Money;

class BlackMarketManager
{
    use SingletonTrait;

    public function __construct()
    {
        self::$instance = $this;
    }

    public function buyMarketPermission(Player $player)
    {
        $price = Main::getInstance()->price;

        if (Money::getMoneyPlayer($player) < $price){
            $player->sendMessage("§cVous n'avez pas les fonds suffisants !");
        }else{
            Money::removeMoney($player, $price);
            $format = 'setuperm "{player}" bmarket.perm';
            $format = str_replace("{player}", $player->getName(), $format);

            // A CHANGER
            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server:: getInstance(), new Language("fra")), $format);
            $player->sendMessage("§fVous avez été retirer de §9$price");
            $player->sendTitle("§9>>§f Accès au §9/bmarket ! §9<<");
        }
    }

    public function buyItem(Player $player){
        if (isset(Main::getInstance()->ItemInfo) && !$this->hasBuyItem($player)) {

            $item = $this->getMarketItem("item");
            $price = round($this->getMarketItem("price"));
            $count = $this->getMarketItem("count");

            if (Money::getMoneyPlayer($player) < $price){
                $player->sendMessage("§cVous n'avez pas les fonds suffisants !");
            }else{
                $player->sendMessage("§9>> §fVous avez été retirer de §9$price$");
                $this->buyMarketItem($player, $item, $count, $price);
            }
        }else{
            $player->sendMessage("§9>> §fVous avez déjà acheter un article au §9BlackMarket §faujourd'hui, revenez demain !");
        }
    }

    public function getMarketItem($info) {
        return Main::getInstance()->ItemInfo[$info];
    }

    public function hasBuyItem(Player $player): bool {
        return isset(Main::getInstance()->hasBuy[$player->getName()]);
    }

    public function buyMarketItem(Player $player, Item $item, int $count, int $price){
        $player->getInventory()->addItem($item->setCount($count));
        Main::getInstance()->hasBuy[$player->getName()] = True;
        Money::removeMoney($player, $price);
    }

}