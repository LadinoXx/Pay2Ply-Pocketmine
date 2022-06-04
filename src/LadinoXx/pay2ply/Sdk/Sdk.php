<?php

/**
 * Não mexa aqui se não sabe oque está fazendo. esse Sdk é a parte mais importante do plugin que contem as verificações.
*/

namespace LadinoXx\pay2ply\Sdk;

use pocketmine\player\Player;
use pocketmine\Server;

class Sdk {
  
  public $API = "https://api.pay2ply.com/";
  
  public $token;
  
  public function getToken() {
    return $this->token;
  }
  
  public function setToken($token) {
    $this->token = $token;
  }
  
  public function getDispenses() {
    $responses = $this->get();
    return $responses;
  }
  
  public function updateDispense($name, $id) {
    $this->update($name, $id);
  }
  
  private function getServer() {
    return Server::getInstance();
  }
  
  public function get() {
    try {
      $url = $this->API . "plugin";
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $headers = array(
        "Authorization: " . $this->getToken(),
        "User-Agent: Java client",
        "Content-Type: application/json"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp);
        if (!is_array($resp)) return null;
        if (isset($resp["status"]) and $resp["status"] >= 500) {
        $this->getServer()->getLogger()->info("[Pay2Ply] A API da Pay2Ply encontra-se indisponível no momento.");
      }
      if (isset($resp["status"]) and $resp["status"] == 423) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O pagamento de sua loja encontra-se pendente.");
      }
      if (isset($resp["status"]) and $resp["status"] == 400) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O token do servidor não foi encontrado na API.");
      }
      if (isset($resp["status"]) and $resp["status"] == 401) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O token do servidor não foi encontrado na API.");
      }

      if (isset($resp["status"]) and $resp["status"] == 403) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O IP do servidor não é o mesmo deste servidor, configure-o.");
      }
      if (!isset($resp["status"])) {
        return $resp;
      }
    } catch (Exception $e) {
      $this->getServer()->getLogger()->info("[Pay2Ply] " . $e);
      return null;
    }
    return null;
  }
  
  public function update($username, $id) {
    try {
      $url = $this->API . "plugin/actived/" . $username . "/" . $id;
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $headers = array(
        "Authorization: " . $this->getToken(),
        "User-Agent: Java client",
        "Content-Type: application/json"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp);
        if ($resp->status >= 500) {
        $this->getServer()->getLogger()->info("[Pay2Ply] A API da Pay2Ply encontra-se indisponível no momento.");
      }
      if ($resp->status == 423) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O pagamento de sua loja encontra-se pendente.");
      }
      if ($resp->status == 400) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O token do servidor não foi encontrado na API.");
      }
      if ($resp->status == 401) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O token do servidor não foi encontrado na API.");
      }

      if ($resp->status == 403) {
        $this->getServer()->getLogger()->info("[Pay2Ply] O IP do servidor não é o mesmo deste servidor, configure-o.");
      }
    } catch (Exception $e) {
      $this->getServer()->getLogger()->info("[Pay2Ply] " . $e);
    }
  }
  
}