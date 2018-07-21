<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/07/21
 * Time: 12:31
 */

namespace Core\Task;


use Core\Player\Level;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class LevelCheckingTask extends PluginTask
{
    protected $level, $player;
    public function __construct(Plugin $plugin, Player $player)
    {
        parent::__construct($plugin);
        $this->level = new Level();
        $this->player = $player;
    }
    public function onRun(int $currentTick)
    {
        $this->level->Checking($this->player);
    }
}