<?php
/**
 * Created by PhpStorm.
 * User: Luca Petrucci
 * Date: 03/02/2015
 * Time: 19:06
 */
namespace SimpleHungerGames;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\CallbackTask;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    private $prefs;
    private $ingame = false;
    private $players = 0;
    private $minute;

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
                ),
                "minplayers" => 4,
                "waiting_time" => 5,
                "game_time" => 7,
                "deathmatch_time" => 3
            )
        );
        $this->prefs->save();
        $this->minute = $this->prefs->get("waiting_time") + $this->prefs->get("game_time") + $this->prefs->get("deathmatch_time");
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this, "schedule"]), 1200); //1*20*60
    }

    public function onDisable(){
        $this->prefs->save();
    }

    public function onPreLogin(PlayerPreLoginEvent $event){
        if($this->ingame == true){
            $event->getPlayer()->close("Match running.");
        }
    }

    public function onJoin(PlayerJoinEvent $event){
        $spawn = $this->getNextSpawn();
        $event->getPlayer()->teleport($spawn);
        $this->players++;
        $event->setJoinMessage("[HG] ".$event->getPlayer()->getName()." joined the match!");
    }

    public function onQuit(PlayerQuitEvent $event){
        $this->players--;
        $event->setQuitMessage("[HG] ".$event->getPlayer()->getName()." left the match!");
    }

    private function schedule(){
        $this->minute--;
        //TODO
    }

    private function getNextSpawn(){
        $x = $this->prefs->get('spawn_locs')[$this->players][0];
        $y = $this->prefs->get('spawn_locs')[$this->players][1];
        $z = $this->prefs->get('spawn_locs')[$this->players][2];
        $spawn = new Vector3($x, $y, $z);
        return $spawn;
    }
}