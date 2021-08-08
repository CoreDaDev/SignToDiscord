<?php

namespace OguzhanUmutlu\SignToDiscord;

use pocketmine\scheduler\AsyncTask;

class SendWebhookAsync extends AsyncTask {
    public $webhookUrl = "";
    public $content = "";
    public $username = "";
    public function __construct(string $webhookUrl, string $content, string $username) {
        $this->webhookUrl = $webhookUrl;
        $this->content = $content;
        $this->username = $username;
    }

    public function onRun() {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->webhookUrl);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(["content" => $this->content." ** **", "username" => $this->username]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($curl);
    }
}