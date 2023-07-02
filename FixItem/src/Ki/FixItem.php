<?php

namespace Ki;

use pocketmine\player\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\plugin\PluginBase as PB;
use pocketmine\event\Listener as L;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use jojoe77777\FormAPI\{SimpleForm, CustomForm};
use pocketmine\item\StringToItemParser;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use onebone\economyapi\EconomyAPI;
use pocketmine\inventory\BaseInventory;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class FixItem extends PB implements L {

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
    	switch($cmd->getName()){
    		case "fix":
    		    if(!$sender instanceof Player){
    		    	$sender->sendMessage("§l§c•§e Hãy Sử Dụng Lệnh Trong Trò Chơi !");
    		    	return true;
    		    }else{
    		    	$this->FixUI($sender);
    		    }
    		break;
    	}
    	return true;
    }

    public function FixUI(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = new SimpleForm(function (Player $sender, ?int $data = null){
        $result = $data;
        if($result === null){
            return;
            }
            switch($result){
                case 0:
                $this->Fix($sender);
                break;
            }
        }); 
        $form->setTitle("§l§6Repair");
        $form->addButton("§l§e● §cRepair §e●", 1, "https://cdn-icons-png.flaticon.com/128/1973/1973830.png");
        $form->sendToPlayer($sender);
            return $form;
    }

    public function Fix($sender){
        $slot = $sender->getInventory()->getHeldItemIndex();
        $item = $sender->getInventory()->getItem($slot);
        $cost = 1000; #cost with item no have enchantments
        $costec = 10000; #cost with item have enchantments
        if($item->hasEnchantments()){
            if(EconomyAPI::getInstance()->myMoney($sender) >= $costec){
                EconomyAPI::getInstance()->reduceMoney($sender, $costec);
                $sender->getInventory()->setItem($slot, $item->setDamage(0));
                $sender->sendMessage("§l§c•§e You Repaired Items In Your Hand With Price ". $costec ." Money");
            }
            else{
                $a = EconomyAPI::getInstance()->myMoney($sender);
                $b = $costec - $a;
                $sender->sendMessage("§l§c•§e You Need More About ". $b ." Money To Repair");
            }
        }
        else{
            if(EconomyAPI::getInstance()->myMoney($sender) >= $cost){
                EconomyAPI::getInstance()->reduceMoney($sender, $cost);
                $sender->getInventory()->setItem($slot, $item->setDamage(0));
                $sender->sendMessage("§l§c•§e You Repaired Items In Your Hand With Price ". $cost ." Money");
            }
            else{
                $a = EconomyAPI::getInstance()->myMoney($sender);
                $b = $cost - $a;
                $sender->sendMessage("§l§c•§e You Need More About ". $b ." Money To Repair");
            }
        }
    }
}