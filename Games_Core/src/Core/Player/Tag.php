<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/07/20
 * Time: 16:46
 */

namespace Core\Player;

use Core\DataFile;
use pocketmine\Player;

class Tag
{
	const BLACK = 0;
	const DARK_BLUE = 1;
	const DARK_GREEN = 2;
	const DARK_AQUA = 3;
	const DARK_RED = 4;
	const PURPLE = 5;
	const GOLD = 6;
	const GRAY = 7;
	const DARK_GRAY = 8;
	const BLUE = 9;
	const LIGHT_GREEN = 10;
	const AQUA = 11;
	const RED = 12;
	const PINK = 13;
	const YELLOW = 14;
	const WHITE = 15;
	const NO_COLLOR = 16;

	private static $colors = [];

	public static function registerColors()
	{
		Tag::$colors[Tag::BLACK] = "§0";
		Tag::$colors[Tag::DARK_BLUE] = "§1";
		Tag::$colors[Tag::DARK_GREEN] = "§2";
		Tag::$colors[Tag::DARK_AQUA] = "§3";
		Tag::$colors[Tag::DARK_RED] = "§4";
		Tag::$colors[Tag::PURPLE] = "§5";
		Tag::$colors[Tag::GOLD] = "§6";
		Tag::$colors[Tag::GRAY] = "§7";
		Tag::$colors[Tag::DARK_GRAY] = "§8";
		Tag::$colors[Tag::BLUE] = "§9";
		Tag::$colors[Tag::LIGHT_GREEN] = "§a";
		Tag::$colors[Tag::AQUA] = "§b";
		Tag::$colors[Tag::RED] = "§c";
		Tag::$colors[Tag::PINK] = "§d";
		Tag::$colors[Tag::YELLOW] = "§e";
		Tag::$colors[Tag::WHITE] = "§f";
		Tag::$colors[Tag::NO_COLLOR] = "§r";
	}

	/**
	 * @param Player $player
	 * @return mixed
	 */
	public function getTag(Player $player)
	{
		$datafile = new DataFile($player->getName());
		$data = $datafile->get('USERDATA');
		return $data['tag'];
	}

	/**
	 * @param Player $player
	 * @param string $tag
	 * @param int $colorid
	 */
	public function setTag(Player $player, string $tag = "NoTag", int $colorid = 16)
	{
		$datafile = new DataFile($player->getName());
		$data = $datafile->get('USERDATA');
		if (mb_strlen($tag) >= 9) {
			$player->sendMessage("§7[§c失敗§7] §cタグは8文字以内にして下さい");
			return;
		}

		if ((0 <= $colorid) && ($colorid <= 16)) {
			$data['tag'] = Tag::$colors[$colorid] . $tag . "§r";
			$usertag = $data['tag'];
			$message = "§7[§a成功§7] §7あなたのタグを【 $usertag §7】に設定しました。";
		} else {
			$data['tag'] = "§r$tag";
			$message = "§7[§cエラー§7] §c指定したカラーIDが見つからなかった為デフォルトの色にしました。";
		}
		$datafile->write('USERDATA', $data);
		$player->sendMessage($message);
	}
}
