<?php

namespace OguzhanUmutlu\SignToDiscord;

use pocketmine\plugin\PluginBase;

class SignToDiscord extends PluginBase {
    /*** @var SignToDiscord|null */
    private static $instance = null;

    public function onEnable() {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public static function sendToWebhook(string $message) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::getInstance()->getConfig()->getNested("webhook-url"));
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(["content" => $message." ** **", "username" => self::getInstance()->getConfig()->getNested("webhook-name")]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($curl);
    }

    /*** @return SignToDiscord|null */
    public static function getInstance(): ?SignToDiscord {
        return self::$instance;
    }
}