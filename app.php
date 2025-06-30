<?php

require 'vendor/autoload.php';

use Hogwarts\Controller\MenuController;

$menu = new MenuController();
$menu->mostrarMenuPrincipal();
