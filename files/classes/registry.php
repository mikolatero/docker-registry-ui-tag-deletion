<?php
require("config.php");
require("restclient.php");

Class RegistryClass {

  public $api;

  public function __construct() {
    $this->api = new RestClient([
        'base_url' => _API_BASE_URL_,
        'decoders' => ['vnd' => "json_decode"],
        'headers' => ['Accept' => 'application/vnd.docker.distribution.manifest.v2+json']
    ]);
  }

  public function getValue($url){
    $result = $this->api->get($url);
    if($result->info->http_code == 200) {
        return $result->decode_response();
    }
    else {
        return "BAD RESPONSE";
    }
  }

  public function getDigestFromHeaders($url,$what_header){
    $result = $this->api->get($url);
    if($result->info->http_code == 200) {
        return $result->headers->$what_header;
    }
    else {
        return("BAD RESPONSE");
    }

  }

  public function getCatalog(){
    $url = "_catalog?n=500";
    return $this->getValue($url);
  }

  public function getTags($name) {
    $url = "$name/tags/list";
    return $this->getValue($url);
  }

  public function getDigestFromTag($repo,$tag) {
    $url = "$repo/manifests/$tag";
    $header = "docker_content_digest";
    return $this->getDigestFromHeaders($url,$header);
  }

  public function deleteTag($repo,$tag) {
    $sha256 = $this->getDigestFromTag($repo,$tag);
    $url = "$repo/manifests/$sha256";
    $result = $this->curl_del($url);

    if ($result == '202') {
      return 0;
    } else {
      return 1;
    }
  }

  public function curl_del($path, $json = '')
  {
    $url = _API_BASE_URL_.$path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.docker.distribution.manifest.v2+json'));
    curl_exec($ch);
    $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $result;
  }


}


?>
