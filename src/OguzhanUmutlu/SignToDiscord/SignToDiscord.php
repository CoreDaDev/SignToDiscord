<?php

namespace OguzhanUmutlu\SignToDiscord;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class SignToDiscord extends PluginBase {
    /*** @var SignToDiscord|null */
    private static $instance = null;
    /*** @var EventListener|null */
    private $listener = null;
    public function onEnable() {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->listener = new EventListener();
        $this->getServer()->getPluginManager()->registerEvents($this->listener, $this);
    }

    /*** @return SignToDiscord|null */
    public static function getInstance(): ?SignToDiscord {
        return self::$instance;
    }

    public static function sendToWebhook(string $message) {
        Server::getInstance()->getAsyncPool()->submitTask(new SendWebhookAsync(
            self::getInstance()->getConfig()->getNested("webhook-url"),
            str_replace("@", "\\@", $message),
            self::getInstance()->getConfig()->getNested("webhook-name")
        ));
    }

    public function onDisable() {
        foreach($this->listener->signs as $player => $signs) {
            SignToDiscord::sendToWebhook(
                $player . " > Sign > " . implode(", ", array_filter($signs, function($n){return is_string($n) && strlen($n) > 0;}))
            );
            unset($this->listener->signs[$player]);
        }
    }
}