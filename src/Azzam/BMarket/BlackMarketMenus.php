<?php

namespace Azzam\BMarket;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\utils\SingletonTrait;
use pocketmine\player\Player;

class BlackMarketMenus
{
    use SingletonTrait;

    public function __construct()
    {
        self::$instance = $this;
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
                    BlackMarketManager::getInstance()->buyMarketPermission($player);
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
                    BlackMarketManager::getInstance()->buyItem($player);
                    break;
            }
            return true;
        });
        $itemName = BlackMarketManager::getInstance()->getMarketItem("itemName");
        $price = BlackMarketManager::getInstance()->getMarketItem("price");
        $count = BlackMarketManager::getInstance()->getMarketItem("count");

        $form->setTitle("Black Market");
        $form->setContent("§9>> §fBienvenue dans le §9Black Market§f !\n\nChaque jour, de nouveaux items sont mis en vente. Leurs prix peut être §9divisé jusqu'à 4 §fdépendamment de l'offre du jour !\n\nL'item du Jour est : §9x$count $itemName\n\n§6Offre du jour : §ePrix divisé par $this->offre");
        $form->addButton("Acheter\n§e$price$", 0, "textures/items/emerald");
        $form->addButton("Quitter", 0, "textures/block/glass_red");
        $form->sendToPlayer($player);
        return $form;
    }
}