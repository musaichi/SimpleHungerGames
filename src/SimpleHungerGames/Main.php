<?php

namespace SimpleHungerGames;

use pocketmine\tile\Chest;
use pocketmine\entity\Item;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\CallbackTask;
use pocketmine\utils\Config;

class Main extends PluginBase{

    /**@var Config*/
    public $prefs;
    public $ingame = false;
    public $players = 0;
    public $totalminutes;
    public $minute;
    public $spawns = 0;
    /**@var Config*/
    public $points;

    const DEV = "luca28pet";
    const VER = "1.0beta";

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
        @mkdir($this->getDataFolder());
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
                "deathmatch_time" => 3,
                "chat_format" => true,
                "chest_items" => array(
                    array(252, 0, 1),
                    array(222, 0, 1)
                )
            )
        );
        $this->prefs->save();

        $this->points = new Config($this->getDataFolder()."points.yml", Config::YAML);

        $this->totalminutes = $this->prefs->get("waiting_time") + $this->prefs->get("game_time") + $this->prefs->get("deathmatch_time");
        $this->minute = $this->prefs->get("waiting_time") + $this->prefs->get("game_time") + $this->prefs->get("deathmatch_time");

        $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this, "schedule"]), 1200); //1200 = 1 tick * 20 ticks per second * 60 seconds

        $this->getServer()->setDefaultLevel($this->getServer()->getLevelByName($this->prefs->get("world")));

        $this->refillChests();
    }

    public function onDisable(){
        $this->prefs->save();
        $this->points->save();
    }

    private function schedule(){
        $this->minute--;
        if($this->minute <= $this->totalminutes and $this->minute > ($this->totalminutes - $this->prefs->get("waiting_time"))) {
            $this->getServer()->broadcastMessage("[HG] Match will start in " . $this->totalminutes - $this->minute);
        }elseif($this->minute == ($this->totalminutes - $this->prefs->get("waiting_time"))){
            if($this->players >= $this->prefs->get("minplayers")){
                $this->getServer()->broadcastMessage("[HG] Game starts NOW!!!");
                $this->ingame = true;
            }else{
                $this->getServer()->broadcastMessage("[HG] There are not enough players to begin the match.");
                $this->minute = $this->totalminutes;
            }
        }elseif($this->minute < ($this->totalminutes - $this->prefs->get("waiting_time")) and $this->minute > ($this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time"))){
            $timetodm = $this->totalminutes - $this->minute + $this->prefs->get('deathmatch_time');
            $this->getServer()->broadcastMessage("[HG] DeathMatch starts in ".$timetodm." minutes.");
        }elseif($this->minute == ($this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time"))){
            $this->getServer()->broadcastMessage("[HG] DeathMatch starts NOW!");
            $this->getServer()->broadcastMessage("[HG] Chest has been refilled!");
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $this->spawns = 0;
                $spawn = $this->getNextSpawn();
                $p->teleport($spawn);
            }
            $this->refillChests();
        }elseif($this->minute < ($this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time")) and $this->minute > 0){
            $timeleft = $this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time") - $this->prefs->get("deathmatch_time") + $this->minute;
            $this->getServer()->broadcastMessage("[HG] ".$timeleft." minutes left");
        }elseif($this->minute == 0){
            $this->getServer()->broadcastMessage("[HG] Game ended!");
            $this->getServer()->shutdown();
        }
    }

    public function getNextSpawn(){
        $this->spawns++;
        $x = $this->prefs->get('spawn_locs')[$this->spawns][0];
        $y = $this->prefs->get('spawn_locs')[$this->spawns][1];
        $z = $this->prefs->get('spawn_locs')[$this->spawns][2];
        $spawn = new Vector3($x, $y, $z);
        return $spawn;
    }

    public function refillChests(){
        foreach($this->getServer()->getLevelByName($this->prefs->get("world"))->getTiles() as $t){
            if($t instanceof Chest){
                if($t->isPaired()){
                    $inv = $t->getInventory();
                }else{
                    $inv = $t->getRealInventory();
                }
                $inv->clearAll();
                foreach($this->prefs->get('chest_items') as $i){
                    $inv->addItem(new Item($i[0], $i[1], $i[2]));
                }
            }
        }
    }
}