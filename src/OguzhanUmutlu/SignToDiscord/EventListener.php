<?php

namespace OguzhanUmutlu\SignToDiscord;

use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;

class EventListener implements Listener {
    public $signs = [];
    public function onPlace(SignChangeEvent $event) {
        $player = $event->getPlayer();
        $lines = $event->getLines();
        $this->signs[$player->getName()] = $lines;
    }

    public function onPacketReceive(DataPacketReceiveEvent $event) {
        $pk = $event->getPacket();
        if(!$pk instanceof BlockActorDataPacket && !$pk instanceof BatchPacket)
            $this->check($event);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $this->check($event);
    }

    public function check($event): void {
        if(isset($this->signs[$event->getPlayer()->getName()])) {
            SignToDiscord::sendToWebhook(
                $event->getPlayer()->getName() . " > Sign > " . implode(", ", array_filter($this->signs[$event->getPlayer()->getName()], function($n){return is_string($n) && strlen($n) > 0;}))
            );
            unset($this->signs[$event->getPlayer()->getName()]);
        }
    }
}