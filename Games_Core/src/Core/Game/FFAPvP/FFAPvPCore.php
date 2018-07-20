<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/07/14
 * Time: 16:58
 */

namespace Core\Game\FFAPvP;

use Core\DataFile;
use Core\Main;
use Core\Player\Money;
use pocketmine\Player;

class FFAPvPCore
{
    protected $money;
    public $worldname = "ffapvp";
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->money = new Money();
    }
    public function AddDeathCount(Player $player)
    {
        if ($player->getLevel()->getName() === $this->worldname) {
            $datafile = new DataFile($player->getName());
            $data = $datafile->get('FFAPVP');
            $data['death'] += 1;
            $datafile->write('FFAPVP', $data);
            $player->addTitle("§cYou are dead", "§cあなたは死んでしまった", 20, 40, 20);
        }
    }
    public function AddKillCount(Player $player)
    {
        if ($player->getLevel()->getName() === $this->worldname) {
            $datafile = new DataFile($player->getName());
            $data = $datafile->get('FFAPVP');
            $data['kill'] += 1;
            $datafile->write('FFAPVP', $data);
            $rand = mt_rand(1, 50);
            $this->money->addMoney($player->getName(), $rand);
            $player->sendMessage("§a+$rand §6V§bN§eCoin");
        }
    }
}
