<?php

namespace Azzam\BMarket;

use Azzam\BMarket\Commandes\BMarketCommande;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase implements Listener
{
    public int $offre;
    public Config $config;
    public array $ItemInfo = [];
    public array $hasBuy = [];
    public int $price;

    use SingletonTrait;

    public function onEnable(): void
    {
        self::$instance = $this;

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

        $this->registerCommands();
    }

    private function registerCommands(): void {
        $this->getServer()->getCommandMap()->registerAll("Commands", [
            new BMarketCommande("bmarket", "permet d'ouvrir le Black market", "bmarket")
        ]);
    }
}