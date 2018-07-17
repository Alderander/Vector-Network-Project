<?php
/*
 * __      __       _             _   _      _                      _    _____           _           _
 * \ \    / /      | |           | \ | |    | |                    | |  |  __ \         (_)         | |
 *  \ \  / /__  ___| |_ ___  _ __|  \| | ___| |___      _____  _ __| | _| |__) | __ ___  _  ___  ___| |_
 *   \ \/ / _ \/ __| __/ _ \| '__| . ` |/ _ \ __\ \ /\ / / _ \| '__| |/ /  ___/ '__/ _ \| |/ _ \/ __| __|
 *    \  /  __/ (__| || (_) | |  | |\  |  __/ |_ \ V  V / (_) | |  |   <| |   | | | (_) | |  __/ (__| |_
 *     \/ \___|\___|\__\___/|_|  |_| \_|\___|\__| \_/\_/ \___/|_|  |_|\_\_|   |_|  \___/| |\___|\___|\__|
 *                                                                                     _/ |
 *                                                                                    |__/
 * Dev: InkoHX
 * ServerSoftware: PocketMine-MP
 * LICENSE: MIT
 */

namespace Core;

use Core\Commands\gamehelp;
use Core\Commands\ping;
use Core\Commands\stats;
use Core\Task\Tip;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{
    public static $datafolder;
    public static $instance = null;
    public function onEnable() : void
    {
        date_default_timezone_set("Asia/Tokyo");
        self::$datafolder = $this->getDataFolder();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getScheduler()->scheduleRepeatingTask(new Tip($this), 180*20);
        $this->getLogger()->info(TextFormat::GREEN."Games_Coreを読み込みました。");
    }
    public function onDisable() : void
    {
        $this->getLogger()->info(TextFormat::RED."Games_Coreを終了しました。");
    }
    private function registerCommands()
    {
        $commands = [
            new gamehelp($this),
            new ping($this),
            new stats($this)
       ];
        $this->getServer()->getCommandMap()->registerAll($this->getName(), $commands);
    }
    public function onLoad() : void
    {
        self::$instance = $this;
        $this->registerCommands();
    }
}
