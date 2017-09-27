<?php
require("classes/registry.php");

$registry = new RegistryClass();

switch ($_GET['data']) {
  case 'catalog':
    die(json_encode($registry->getCatalog()));
    break;

  case 'image':
    $name = $_GET['name'];
    die(json_encode($registry->getTags($name)));
    break;

  case 'deletetag':
    $repo = $_GET['image'];
    $tag = $_GET['tag'];
    die(json_encode($registry->deleteTag($repo,$tag)));
    break;

  default:
    die(0);
    break;
}

?>
