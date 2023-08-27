<?php

declare(strict_types=1);

namespace Joestarfish\BroadSword\item;

use customiesdevs\customies\item\component\HandEquippedComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use Joestarfish\BroadSword\Main;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Sword;
use pocketmine\item\ToolTier;

class BroadSword extends Sword implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = 'Unknown') {
		parent::__construct($identifier, $name, ToolTier::NETHERITE());
		$this->initComponent(
			Main::getItemTexture(),
			new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS),
		);

		define('BROAD_SWORD_TYPE_ID', $this->getTypeId());

		$this->addComponent(new HandEquippedComponent());
	}

	public function getMaxDurability(): int {
		return Main::getItemDurability();
	}

	public function getAttackPoints(): int {
		return 0;
	}
}
