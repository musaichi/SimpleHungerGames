<?php
/**
 * Created by PhpStorm.
 * User: Luca Petrucci
 * Date: 03/02/2015
 * Time: 19:06
 */
namespace SimpleHungerGames;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
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
    private $totalminutes;
    private $minute;
    private $spawns = 0;

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
        $this->totalminutes = $this->prefs->get("waiting_time") + $this->prefs->get("game_time") + $this->prefs->get("deathmatch_time");
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
        $this->players = $this->players + 1;
        $event->setJoinMessage("[HG] ".$event->getPlayer()->getName()." joined the match!");
    }

    public function onQuit(PlayerQuitEvent $event){
        $this->players = $this->players - 1;
        $event->setQuitMessage("[HG] ".$event->getPlayer()->getName()." left the match!");
        if($this->players <= 1){
            $this->getServer()->broadcastMessage("[HG] Game ended!");
            $this->getServer()->shutdown();
        }
    }

    public function onDeath(PlayerDeathEvent $event){
        $this->players = $this->players - 1;
        $event->getEntity()->kick("Death");
        $event->setDeathMessage("[HG] ".$event->getEntity()->getName()." died!\nThere are ".$this->players." left.");
        if($this->players <= 1){
            $this->getServer()->broadcastMessage("[HG] Game ended!");
            $this->getServer()->shutdown();
        }
    }

    private function schedule(){
        $this->minute--;
        if($this->minute <= $this->totalminutes and $this->minute > ($this->totalminutes - $this->prefs->get("waiting_time"))) {
            $this->getServer()->broadcastMessage("[HG] Match will start in " . $this->totalminutes - $this->minute);
        }elseif($this->minute == ($this->totalminutes - $this->prefs->get("waiting_time"))){
            $this->getServer()->broadcastMessage("[HG] Game starts NOW!!!");
            $this->ingame = true;
        }elseif($this->minute < ($this->totalminutes - $this->prefs->get("waiting_time")) and $this->minute > ($this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time"))){
            $timetodm = $this->totalminutes - $this->minute + $this->prefs->get('deathmatch_time');
            $this->getServer()->broadcastMessage("[HG] DeathMatch starts in ".$timetodm." minutes.")
        }elseif($this->minute == ($this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time"))){
            $this->getServer()->broadcastMessage("[HG] DeathMatch starts NOW!");
            $this->getServer()->broadcastMessage("[HG] You will be teleported to spawn!");
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $this->spawns = 0;
                $spawn = $this->getNextSpawn();
                $p->teleport($spawn);
            }
        }elseif($this->minute < ($this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time")) and $this->minute > 0){
            $timeleft = $this->totalminutes - $this->prefs->get("waiting_time") - $this->prefs->get("game_time") - $this->prefs->get("deathmatch_time") + $this->minute;
            $this->getServer()->broadcastMessage("[HG] ".$timeleft." minutes left");
        }elseif($this->minute == 0){
            $this->getServer()->broadcastMessage("[HG] Game ended!");
            $this->getServer()->shutdown();
        }
    }

    private function getNextSpawn(){
        $this->spawns = $this->spawns + 1;
        $x = $this->prefs->get('spawn_locs')[$this->spawns][0];
        $y = $this->prefs->get('spawn_locs')[$this->spawns][1];
        $z = $this->prefs->get('spawn_locs')[$this->spawns][2];
        $spawn = new Vector3($x, $y, $z);
        return $spawn;
    }
}