<?php
/**
 * Created by PhpStorm.
 * User: Luca Petrucci
 * Date: 03/02/2015
 * Time: 19:06
 */
namespace SimpleHungerGames;

use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    private $prefs;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->prefs = new Config($this->getDataFolder()."prferences.yml", Config::YAML, array
            (
                "world" => "worldname",
                "players" => 16,
                "spawn_locs" => array(
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                    array(1,2,3),
                )
            )
        );
        $this->prefs->save();
    }

    public function onDisable(){
        $this->prefs->save();
    }

}