<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/07/19
 * Time: 12:28
 */

namespace Core\Event;

use Core\Game\FFAPvP\FFAPvPCore;
use Core\Main;
use Core\Player\KillSound;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class PlayerDeath
{
    protected $plugin;
    protected $ffapvp;
    protected $killsound;
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->ffapvp = new FFAPvPCore($this->plugin);
        $this->killsound = new KillSound($this->plugin);
    }
    public function event(PlayerDeathEvent $event)
    {
        $event->setDeathMessage(null);
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();
        if ($player->getLevel()->getName() === "ffapvp") {
            $event->setDrops([Item::get(0, 0, 0)]);
            $player->setMaxHealth(20);
            $this->ffapvp->AddDeathCount($player);
            if ($cause instanceof EntityDamageByEntityEvent) {
                $damager = $cause->getDamager();
                if ($damager instanceof Player) {
                    $this->ffapvp->AddKillCount($damager);
                    $damager->setMaxHealth($damager->getMaxHealth() + 1);
                    $damager->getInventory()->addItem(Item::get(Item::GOLDEN_APPLE, 0, 1));
                    $this->killsound->PlaySound($damager);
                }
            }
        }
    }
}
