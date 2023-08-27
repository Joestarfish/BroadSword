<?php

declare(strict_types=1);

namespace Joestarfish\BroadSword\scheduler;

use Joestarfish\BroadSword\Main;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class SwordTickingTask extends Task {
	private int $ticks = 0;

	public function __construct(private string $player_username) {
		// noop
	}

	public function onRun(): void {
		$this->ticks++;
		if (!($popup = Main::getPopup())['active']) {
			return;
		}

		$player = Server::getInstance()->getPlayerExact($this->player_username);

		if (!$player) {
			return;
		}

		// Limit the length between 10 and 50
		$bar_length = (int) min(max($popup['length'], 10), 50);
		$required_charging_ticks = Main::getChargingTicks();
		$current_progress = (int) min($this->ticks, $required_charging_ticks);

		$progress_length = intval(($current_progress / $required_charging_ticks) * $bar_length);

		$progress_char = str_repeat($popup['progress-char'], $progress_length);

		if ($progress_length == $bar_length) {
			$str = "{$popup['filled-color']}{$progress_char}";
		} else {
			$remaining_char = str_repeat($popup['progress-char'], $bar_length - $progress_length);
			$str = "{$popup['progress-color']}{$progress_char}{$popup['remaining-color']}{$remaining_char}";
		}

		switch ($popup['type']) {
			case 'action_bar':
				$player->sendActionBarMessage($str);
				break;
			case 'jukebox':
				$player->sendJukeboxPopup($str);
				break;
			case 'popup':
				$player->sendPopup($str);
				break;
			case 'tip':
				$player->sendTip($str);
				break;
			default:
				$player->sendActionBarMessage($str);
				break;
		}
	}

	public function reset() {
		$this->ticks = 0;
	}

	public function isReady() {
		return $this->ticks > Main::getChargingTicks();
	}
}
