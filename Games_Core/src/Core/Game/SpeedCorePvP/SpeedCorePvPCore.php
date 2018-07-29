<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/07/24
 * Time: 22:36
 */

namespace Core\Game\SpeedCorePvP;


use Core\DataFile;
use Core\Main;
use Core\Player\Level;
use Core\Player\Money;
use Core\Task\AutosetBlockTask;
use Core\Task\LevelCheckingTask;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Color;

class SpeedCorePvPCore
{
	protected $plugin;
	protected $bluecolor;
	protected $redcolor;
	protected $bluehp = 75;
	protected $redhp = 75;
	protected $bluecount = 0;
	protected $redcount = 0;
	protected $team = [];
	protected $gamemode = false;
	protected $money;
	protected $level;
	protected $fieldname = "corepvp";
	protected $point = [
		"blue.core" => [
			"x" => 52,
			"y" => 61,
			"z" => -100
		],
		"blue.spawn" => [
			"x" => 52,
			"y" => 66,
			"z" => -100
		],
		"red.core" => [
			"x" => 235,
			"y" => 61,
			"z" => 11
		],
		"red.spawn" => [
			"x" => 235,
			"y" => 66,
			"z" => 11
		]
	];

	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
		$this->bluecolor = new Color(0, 0, 255);
		$this->redcolor = new Color(255, 0, 0);
		$this->money = new Money();
		$this->level = new Level();
	}

	public static $blockids = [
		Block::IRON_ORE => 20,
		Block::GOLD_ORE => 20,
		Block::COAL_ORE => 15,
		Block::DIAMOND_ORE => 60,
		Block::LOG => 10,
		Block::MELON_BLOCK => 10
	];

	/**
	 * @return bool
	 */
	public function getGameMode(): bool
	{
		if ($this->gamemode) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param bool $bool
	 */
	public function setGameMode(bool $bool)
	{
		if ($bool) {
			$this->gamemode = true;
		} else {
			$this->gamemode = false;
		}
	}

	/**
	 * @param int $teamid
	 * @param int $hp
	 */
	public function setHP(int $teamid, int $hp)
	{
		switch ($teamid) {
			case 1:
				$this->redhp = $hp;
				break;
			case 2:
				$this->bluehp = $hp;
				break;
			default:
				return;
				break;
		}
	}

	/**
	 * @param int $teamid
	 * @return int
	 */
	public function getHP(int $teamid): int
	{
		switch ($teamid) {
			case 1:
				return $this->redhp;
				break;
			case 2:
				return $this->bluehp;
				break;
			default:
				return 0;
				break;
		}
	}

	/**
	 * @param int $teamid
	 * @param int $count
	 */
	public function setPlayerCount(int $teamid, int $count)
	{
		switch ($teamid) {
			case 1:
				$this->redcount = $count;
				break;
			case 2:
				$this->bluecount = $count;
				break;
			default:
				return;
				break;
		}
	}

	/**
	 * @param int $teamid
	 */
	public function AddPlayerCount(int $teamid)
	{
		switch ($teamid) {
			case 1:
				$this->redcount++;
				break;
			case 2:
				$this->bluecount++;
				break;
			default:
				return;
				break;
		}
	}

	/**
	 * @param int $teamid
	 * @return int
	 */
	public function getPlayerCount(int $teamid): int
	{
		switch ($teamid) {
			case 1:
				return $this->redcount;
				break;
			case 2:
				return $this->bluecount;
				break;
			default:
				return 0;
				break;
		}
	}

	/**
	 * @param Player $player
	 */
	public function setSpawn(Player $player)
	{
		$level = $this->plugin->getServer()->getLevelByName($this->fieldname);
		$red = $this->point["red.spawn"];
		$blue = $this->point["blue.spawn"];
		if ($this->team[$player->getName()] === "Red") {
			$player->setSpawn(new Position($red["x"], $red["y"], $red["z"], $level));
			$player->teleport(new Position($red["x"], $red["y"], $red["z"], $level));
		} else {
			$player->setSpawn(new Position($blue["x"], $blue["y"], $blue["z"], $level));
			$player->teleport(new Position($blue["x"], $blue["y"], $blue["z"], $level));
		}
	}

	/**
	 * @param Player $player
	 * @param Block $block
	 */
	public function GameJoin(Player $player, Block $block)
	{
		if ($player->getLevel()->getName() === $this->fieldname) {
			if ($block->getId() === Block::EMERALD_BLOCK) {
				$this->setGameMode(true);
				if (!isset($this->team[$player->getName()])) {
					if (empty($this->team[$player->getName()])) {
						if ($this->redcount < $this->bluecount) {
							$this->team[$player->getName()] = "Red";
							$this->AddPlayerCount(1);
							$this->setSpawn($player);
							$this->Kit($player);
							$player->sendMessage("§aあなたは §cRed §aTeamになりました。");
						} elseif ($this->bluecount > $this->redcount) {
							$this->team[$player->getName()] = "Blue";
							$this->AddPlayerCount(2);
							$this->setSpawn($player);
							$this->Kit($player);
							$player->sendMessage("§aあなたは §9Blue §aTeamになりました。");
						}
					} else {
						if (mt_rand(0, 1) === 0) {
							$this->team[$player->getName()] = "Red";
							$this->AddPlayerCount(1);
							$this->setSpawn($player);
							$this->Kit($player);
							$player->sendMessage("§aあなたは §cRed §aTeamになりました。");
						} else {
							$this->team[$player->getName()] = "Blue";
							$this->AddPlayerCount(2);
							$this->setSpawn($player);
							$this->Kit($player);
							$player->sendMessage("§aあなたは §9Blue §aTeamになりました。");
						}
					}
				} else {
					$player->sendMessage("§cあなたは既にチームに所属しています。");
				}
			}
		}
	}

	/**
	 * @param Player $player
	 */
	public function GameQuit(Player $player)
	{
		if (isset($this->team[$player->getName()])) {
			if ($this->team[$player->getName()] === "Red") {
				unset($this->team[$player->getName()]);
				$this->ReducePlayerCount(1);
				$player->sendMessage("§cRed §aTeamから退出しました。");
			} elseif ($this->team[$player->getName()] === "Blue") {
				unset($this->team[$player->getName()]);
				$this->ReducePlayerCount(2);
				$player->sendMessage("§9Blue §aTeamから退出しました。");
			}
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function DropItem(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if ($player->getLevel()->getName() === $this->fieldname) {
			if ($block->getId() === Block::IRON_ORE) {
				$event->setDrops([Item::get(Item::IRON_INGOT, 0, 1)]);
			} elseif ($block->getId() === Block::GOLD_ORE) {
				$event->setDrops([Item::get(Item::GOLD_INGOT, 0, 1)]);
			} elseif ($block->getId() === Block::MELON_BLOCK) {
				$event->setDrops([Item::get(Item::MELON, 0, 16)]);
			} elseif ($block->getId() === Block::LOG) {
				$event->setDrops([Item::get(Item::LOG, 0, 1)]);
			} elseif ($block->getId() === Block::COAL_ORE) {
				$event->setDrops([Item::get(Item::COAL, 0, 1)]);
			}
			if (isset(self::$blockids[$block->getId()])) {
				if (isset($event->getDrops()[0])) {
					$player->getInventory()->addItem($event->getDrops()[0]);
					$event->setDrops([Item::get(Item::AIR, 0, 0)]);
					$this->plugin->getScheduler()->scheduleDelayedTask(new AutosetBlockTask($this->plugin, $block), self::$blockids[$block->getId()] * 20);
				}
			}
		}
	}

	/**
	 * @param int $teamid
	 */
	public function ReducePlayerCount(int $teamid)
	{
		switch ($teamid) {
			case 1:
				$this->redcount--;
				break;
			case 2:
				$this->bluecount--;
				break;
			default:
				return;
				break;
		}
	}

	/**
	 * @param Player $player
	 */
	public function Kit(Player $player)
	{
		$armors = [
			"leather_cap" => Item::get(Item::LEATHER_CAP, 0, 1),
			"leather_tunic" => Item::get(Item::LEATHER_TUNIC, 0, 1),
			"leather_pants" => Item::get(Item::LEATHER_PANTS, 0, 1),
			"leather_boots" => Item::get(Item::LEATHER_BOOTS, 0, 1)
		];
		$weapons = [
			"stone_sword" => Item::get(Item::STONE_SWORD, 0, 1),
			"bow" => Item::get(Item::BOW, 0, 1),
			"gold_pickaxe" => Item::get(Item::GOLD_PICKAXE, 0, 1)
		];
		if ($this->team[$player->getName()] === "Red") {
			if ($armors instanceof Durable and $armors instanceof Armor) {
				$armors->setUnbreakable(true);
				$armors->setCustomColor($this->redcolor);
			}
		} else {
			if ($armors instanceof Durable and $armors instanceof Armor) {
				$armors->setUnbreakable(true);
				$armors->setCustomColor($this->bluecolor);
			}
		}
		if ($weapons instanceof Durable) {
			$weapons->setUnbreakable(true);
		}
		$armor = $player->getArmorInventory();
		$armor->setHelmet($armors['leather_cap']);
		$armor->setChestplate($armors['leather_tunic']);
		$armor->setLeggings($armors['leather_pants']);
		$armor->setBoots($armors['leather_boots']);
		$player->getInventory()->addItem($weapons['stone_sword']);
		$player->getInventory()->addItem($weapons['bow']);
		$player->getInventory()->addItem($weapons['gold_pickaxe']);
		$player->getInventory()->addItem(Item::get(Item::BREAD, 0, 64));
		$player->getInventory()->addItem(Item::get(Item::ARROW, 0, 64));
	}

	/**
	 * @param Player $player
	 */
	public function Respawn(Player $player)
	{
		if ($player->getLevel()->getName() === $this->fieldname) {
			$this->Kit($player);
			$player->addTitle("§cYou are dead", "§cあなたは死んでしまった", 20, 40, 20);
		}
	}

	/**
	 * @param Player $player
	 */
	public function AddDeathCount(Player $player)
	{
		if ($player->getLevel()->getName() === $this->fieldname) {
			$datafile = new DataFile($player->getName());
			$data = $datafile->get('COREPVP');
			$data['death'] += 1;
			$datafile->write('COREPVP', $data);
		}
	}

	/**
	 * @param Player $player
	 */
	public function AddKillCount(Player $player)
	{
		if ($player->getLevel()->getName() === $this->fieldname) {
			$datafile = new DataFile($player->getName());
			$data = $datafile->get('COREPVP');
			$data['kill'] += 1;
			$datafile->write('COREPVP', $data);
			$rand = mt_rand(1, 50);
			$this->money->addMoney($player->getName(), $rand);
			$player->sendMessage("§a+$rand §6V§bN§eCoin");
			$this->level->LevelSystem($player);
			$this->plugin->getScheduler()->scheduleDelayedTask(new LevelCheckingTask($this->plugin, $player), 20);
		}
	}

	/**
	 * @param Player $player
	 */
	public function AddWinCount(Player $player)
	{
		if ($player->getLevel()->getName() === $this->fieldname) {
			$datafile = new DataFile($player->getName());
			$data = $datafile->get('COREPVP');
			$data['win'] += 1;
			$datafile->write('COREPVP', $data);
		}
	}

	/**
	 * @param Player $player
	 */
	public function AddLoseCount(Player $player)
	{
		if ($player->getLevel()->getName() === $this->fieldname) {
			$datafile = new DataFile($player->getName());
			$data = $datafile->get('COREPVP');
			$data['lose'] += 1;
			$datafile->write('COREPVP', $data);
		}
	}

	/**
	 * @param Player $player
	 */
	public function AddBreakCoreCount(Player $player)
	{
		if ($player->getLevel()->getName() === $this->fieldname) {
			$datafile = new DataFile($player->getName());
			$data = $datafile->get('COREPVP');
			$data['breakcore'] += 1;
			$datafile->write('COREPVP', $data);
		}
	}

	/**
	 * @param EntityDamageEvent $event
	 */
	public function Damage(EntityDamageEvent $event)
	{
		$entity = $event->getEntity();
		if ($entity->getLevel()->getName() === $this->fieldname) {
			if ($event instanceof EntityDamageByEntityEvent and $entity instanceof Player) {
				if ($this->team[$entity->getName()] !== false) {
					$damager = $event->getDamager();
					if ($damager instanceof Player) {
						if ($this->team[$damager->getName()] === $this->team[$entity->getName()]) {
							$event->setCancelled(true);
						}
					}
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function BreakCore(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if ($this->getGameMode()) {
			$red = $this->point["red.core"];
			$blue = $this->point["blue.core"];
			if ($player->getLevel()->getName() === $this->fieldname) {
				if ($block->getX() === $red["x"] && $block->getY() === $red["y"] && $block->getZ() === $red["z"]) {
					if ($this->team[$player->getName()] === "Blue") {
						if ($this->redcount >= 3 && $this->bluecount >= 3) {
							$event->setCancelled(true);
							$this->redhp--;
							$this->money->addMoney($player->getName(), 10);
							$this->AddBreakCoreCount($player);
							$player->sendMessage("§a+10 §6V§bN§eCoin");
							$this->level->LevelSystem($player);
							$this->plugin->getServer()->broadcastPopup("§cRed §6のコアが削られています。");
							$this->plugin->getScheduler()->scheduleDelayedTask(new LevelCheckingTask($this->plugin, $player), 20);
							if ($this->redhp <= 0) {
								$this->EndGame("Blue");
							}
						} else {
							$event->setCancelled(true);
							$player->sendMessage("§cプレイヤーが足りない為コアを削る事は出来ません。");
						}
					}
				} elseif ($block->getX() === $blue["x"] && $block->getY() === $blue["y"] && $block->getZ() === $blue["z"]) {
					if ($this->team[$player->getName()] === "Red") {
						if ($this->bluecount >= 3 && $this->redcount >= 3) {
							$event->setCancelled(true);
							$this->bluehp--;
							$this->money->addMoney($player->getName(), 10);
							$this->AddBreakCoreCount($player);
							$player->sendMessage("§a+10 §6V§bN§eCoin");
							$this->level->LevelSystem($player);
							$this->plugin->getServer()->broadcastPopup("§9Blue §6のコアが削られています。");
							$this->plugin->getScheduler()->scheduleDelayedTask(new LevelCheckingTask($this->plugin, $player), 20);
							if ($this->bluehp <= 0) {
								$this->EndGame("Red");
							}
						} else {
							$event->setCancelled(true);
							$player->sendMessage("§cプレイヤーが足りない為コアを削る事は出来ません。");
						}
					}
				}
			}
		}
	}

	/**
	 * @param string $team
	 */
	public function EndGame(string $team)
	{
		foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
			if ($player->getLevel()->getName() === $this->fieldname) {
				if ($this->team[$player->getName()] === $team) {
					$this->money->addMoney($player->getName(), 3000);
					$this->AddWinCount($player);
					$player->sendMessage("§7[§bSpeed§aCore§cPvP§7] おめでとうございます。あなたのチームが勝利しました。\n§7[§bSpeed§aCore§cPvP§7] §63000§6V§bN§eCoin増えました。");
				} else {
					$this->money->addMoney($player->getName(), 500);
					$this->AddLoseCount($player);
					$player->sendMessage("§7[§bSpeed§aCore§cPvP§7] 残念...あなたのチームは敗北しました。\n§7[§bSpeed§aCore§cPvP§7] §6500§6V§bN§eCoin増えました。");
				}
			}
		}
		unset($this->team);
		$this->setHP(1, 75);
		$this->setHP(2, 75);
		$this->SetPlayerCount(1, 0);
		$this->SetPlayerCount(2, 0);
		$this->setGameMode(false);
	}
}