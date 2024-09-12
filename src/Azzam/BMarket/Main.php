<?php

namespace Azzam\BMarket;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\lang\Language;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use Terpz710\EconomyPE\Money;

class Main extends PluginBase implements Listener
{
    public $offre;
    public $config;
    public $ItemInfo = [];
    public $hasBuy = [];
    public $price;

    public function onEnable(): void
    {
        $this->saveResource('config.yml');
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $probabiliteOffreSpeciale = $this->config->get("ProbabiliteOffreSpeciale", 35);

        $rand = mt_rand(1, 100);

        if ($rand <= $probabiliteOffreSpeciale) {
            $this->offre = mt_rand(2, $this->config->get("MaxDivisions", 4));
        } else {
            $this->offre = 1;
        }

        $items = $this->config->get("Items", []);

        $randomIndex = mt_rand(0, count($items) - 1);
        $randomItemData = $items[$randomIndex];

        list($itemId, $price, $itemName, $count) = explode(":", $randomItemData);
        $item = StringToItemParser::getInstance()->parse($itemId) ?? LegacyStringToItemParser::getInstance()->parse($itemId);

        if ($item instanceof Item) {
            $this->ItemInfo = [
                "item" => $item,
                "price" => $price/$this->offre,
                "itemName" => $itemName,
                "count" => $count,
            ];
        } else {
            $this->getLogger()->warning("L'item ID $itemId n'est pas valide.");
        }

        $this->price = $this->config->get("price");
    }

    public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool
    {
        if ($player instanceof Player)
        {
            switch ($command->getName())
            {
                case "bmarket":
                    if ($player->hasPermission("bmarket.perm"))
                    {
                        $this->BuyForm($player);
                    }else{
                        $this->MainForm($player);
                    }
                    break;
            }
            return true;
        }else{
            $player->sendMessage("Vous ne pouvez pas utilisé cette commande !");
        }
        return true;
    }

    public function MainForm(Player $player)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            $re = $data;
            if ($re === null)
            {
                return true;
            }
            switch ($re){
                case 0 :
                    $price = $this->price;
                    if (Money::getMoneyPlayer($player) < $price){
                        $player->sendMessage("§cVous n'avez pas les fonds suffisants !");
                    }else{
                        Money::removeMoney($player, $price);
                        $format = 'setuperm "{player}" bmarket.perm';
                        $format = str_replace("{player}", $player->getName(), $format);

                        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server:: getInstance(), new Language("fra")), $format);
                        $player->sendMessage("§fVous avez été retirer de §9$price");
                        $player->sendTitle("§9>>§f Accès au §9/bmarket ! §9<<");
                    }
                    break;
                case 1 :
                    break;
            }
            return true;
        });
        $form->setTitle("Black Market");
        $form->setContent("§9>> §fSouhaitez-vous acheter l'accès au §9Marché Noir§f pour §91'000'000$ §f?\n\n§9>> §fChaque jour, un article rare sera mis en vente, et son prix peut être §9réduit §fjusqu'à §94 fois §fen fonction de l'offre du jour !");
        $form->addButton("§aAcheter");
        $form->addButton("§cRetour");
        $form->sendToPlayer($player);
        return $form;
    }

    public function BuyForm(Player $player)
    {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            $re = $data;
            if ($re === null)
            {
                return true;
            }
            switch ($re){
                case 0 :
                    $this->buyItem($player);
                    break;
            }
            return true;
        });
        $itemName = $this->ItemInfo["itemName"];
        $price = $this->ItemInfo["price"];
        $count = $this->ItemInfo["count"];

        $form->setTitle("Black Market");
        $form->setContent("§9>> §fBienvenue dans le §9Black Market§f !\n\nChaque jour, de nouveaux items sont mis en vente. Leurs prix peut être §9divisé jusqu'à 4 §fdépendamment de l'offre du jour !\n\nL'item du Jour est : §9x$count $itemName\n\n§6Offre du jour : §ePrix divisé par $this->offre");
        $form->addButton("Acheter\n§e$price$", 0, "textures/items/emerald");
        $form->addButton("Quitter", 0, "textures/block/glass_red");
        $form->sendToPlayer($player);
        return $form;
    }

    public function buyItem(Player $player){
        if (isset($this->ItemInfo) && !isset($this->hasBuy[$player->getName()])) {
            $item = $this->ItemInfo["item"];
            $price = round($this->ItemInfo["price"]);
            $count = $this->ItemInfo["count"];
            if (Money::getMoneyPlayer($player) < $price){
                $player->sendMessage("§cVous n'avez pas les fonds suffisants !");
            }else{
                Money::removeMoney($player, $price);
                $player->sendMessage("§9>> §fVous avez été retirer de §9$price$");
                $player->getInventory()->addItem($item->setCount($count));
                $this->hasBuy[$player->getName()] = True;
            }
        }else{
            $player->sendMessage("§9>> §fVous avez déjà acheter un article au §9BlackMarket §faujourd'hui, revenez demain !");
        }
    }
}