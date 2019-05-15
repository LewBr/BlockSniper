<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class UndoCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "undo", Translation::COMMANDS_UNDO_DESCRIPTION, "/undo [amount]", ["u"]);
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var Player $sender */
		$store = SessionManager::getPlayerSession($sender)->getRevertStore();
		if($store->getUndoCount() === 0){
			$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_UNDO_NO_UNDO));

			return;
		}

		$undoAmount = 1;
		if(isset($args[0])){
			$undoAmount = (int) $args[0];
			$totalUndo = $store->getUndoCount();
			if($undoAmount > $totalUndo || $args[0] === "all"){
				$undoAmount = $totalUndo;
			}
		}

		$store->restoreLatestUndo($undoAmount);
		$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_UNDO_SUCCESS) . TF::AQUA . " (" . $undoAmount . ")");
	}
}
