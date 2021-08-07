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
        if(!$pk instanceof BlockActorDataPacket && !$pk instanceof BatchPacket && isset($this->signs[$event->getPlayer()->getName()])) {
            SignToDiscord::sendToWebhook(
                $event->getPlayer()->getName() . " > Sign > " . implode(", ", $this->signs[$event->getPlayer()->getName()])
            );
            unset($this->signs[$event->getPlayer()->getName()]);
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        if(isset($this->signs[$event->getPlayer()->getName()])) {
            SignToDiscord::sendToWebhook(
                $event->getPlayer()->getName() . " > Sign > " . implode(", ", $this->signs[$event->getPlayer()->getName()])
            );
            unset($this->signs[$event->getPlayer()->getName()]);
        }
    }
}