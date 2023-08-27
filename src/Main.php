<?php

declare(strict_types=1);

namespace Joestarfish\BroadSword;

use customiesdevs\customies\item\CustomiesItemFactory;
use Joestarfish\BroadSword\item\BroadSword;
use Joestarfish\BroadSword\scheduler\SwordTickingTask;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	use SingletonTrait;

	private static Config $config;

	/**
	 * @var SwordTickingTask[] $players_ticking
	 */
	private static array $players_ticking = [];

	public function onLoad(): void {
		$this->setInstance($this);

		self::$config = $this->getConfig();

		CustomiesItemFactory::getInstance()->registerItem(
			BroadSword::class,
			'minecraft:broad_sword',
			self::getItemName(),
		);
	}

	private function validateConfig() {
		$valid = true;
		$prefix = '[Config Validation] ';

		if (self::getItemDurability() < 1) {
			$this->getLogger()->critical($prefix . 'Item durability must be higher than 1');
			$valid = false;
		}

		if (self::getItemAttackPoints() <= 0) {
			$this->getLogger()->critical($prefix . 'Item attack points must be higher than 0');
			$valid = false;
		}

		if (self::getChargingTicks() < 1) {
			$this->getLogger()->critical($prefix . 'Charging ticks must be higher than 1');
			$valid = false;
		}

		if (!isset(TextFormat::COLORS[self::getPopup()['progress-color']])) {
			$this->getLogger()->critical($prefix . 'Progress color must be a valid color');
			$valid = false;
		}

		if (!isset(TextFormat::COLORS[self::getPopup()['remaining-color']])) {
			$this->getLogger()->critical($prefix . 'Remaining color must be a valid color');
			$valid = false;
		}

		if (!isset(TextFormat::COLORS[self::getPopup()['filled-color']])) {
			$this->getLogger()->critical($prefix . 'Filled color must be a valid color');
			$valid = false;
		}

		return $valid;
	}

	public function onEnable(): void {
		if (!$this->validateConfig()) {
			$this->getServer()
				->getPluginManager()
				->disablePlugin($this);
			return;
		}

		$this->getServer()
			->getPluginManager()
			->registerEvents($this, $this);
	}

	public function onQuit(PlayerQuitEvent $event) {
		$this->stopPlayerTicking($event->getPlayer()->getName());
	}

	public function onHeld(PlayerItemHeldEvent $event) {
		$item = $event->getItem();
		$player = $event->getPlayer();

		if ($item->getTypeId() !== BROAD_SWORD_TYPE_ID) {
			$this->stopPlayerTicking($player->getName());
			return;
		}

		if ($this->getTaskForPlayer($player->getName())) {
			return;
		}

		$this->startPlayerTicking($player->getName());
	}

	public function onDamage(EntityDamageByEntityEvent $event) {
		$damager = $event->getDamager();

		if (!($task = $this->getTaskForPlayer($damager->getNameTag()))) {
			return;
		}

		if (!$task->isReady()) {
			if (!self::getCanAttackWhenLoading()) {
				$event->cancel();
			}

			if (self::getResetWhenAttackingWhileLoading()) {
				$task->reset();
			}

			return;
		}

		$event->setBaseDamage(self::getItemAttackPoints());

		if (!$event->isCancelled()) {
			$task->reset();
		}
	}

	private function getTaskForPlayer(string $username): ?SwordTickingTask {
		return self::$players_ticking[$username] ?? null;
	}

	private function startPlayerTicking(string $username) {
		if ($task = $this->getTaskForPlayer($username)) {
			$task->reset();
			return;
		}

		$task = new SwordTickingTask($username);
		$this->getScheduler()
			->scheduleRepeatingTask($task, 1)
			->getTask();

		self::$players_ticking[$username] = $task;
	}

	private function stopPlayerTicking(string $username) {
		if (!($player_ticking = $this->getTaskForPlayer($username))) {
			return;
		}

		$player_ticking->getHandler()->cancel();
		unset(self::$players_ticking[$username]);
	}

	public static function getItemTexture() {
		return self::$config->get('texture', 'netherite_sword');
	}

	public static function getItemName() {
		return self::$config->get('item-name', 'Broad Sword');
	}

	public static function getItemDurability() {
		return (int) self::$config->get('durability', 10);
	}

	public static function getChargingTicks() {
		return (int) self::$config->get('charging-ticks', 50);
	}

	public static function getItemAttackPoints() {
		return (float) self::$config->get('attack-points', 30);
	}

	public static function getCanAttackWhenLoading() {
		return (bool) self::$config->get('attack-when-loading', true);
	}

	public static function getResetWhenAttackingWhileLoading() {
		return (bool) self::$config->get('reset-when-attacking-while-loading', false);
	}

	public static function getPopup() {
		return [
			'active' => self::$config->getNested('popup.active', true),
			'type' => self::$config->getNested('popup.type', 'action_bar'),
			'progress-char' => self::$config->getNested('popup.progress-char', '|'),
			'length' => self::$config->getNested('popup.length', 20),
			'progress-color' => self::$config->getNested('popup.color.progress', '§a'),
			'remaining-color' => self::$config->getNested('popup.color.remaining', '§7'),
			'filled-color' => self::$config->getNested('popup.color.filled', '§g'),
		];
	}
}
