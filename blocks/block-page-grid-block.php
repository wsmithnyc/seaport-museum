<?php

use Blocks\WidgetGridBlock;

require_once ('blocks_autoload.php');

try {
    $gridBlock = new WidgetGridBlock();

    echo $gridBlock->getOutput();
} catch (Exception $e) {
    echo "<div><h3>An error occurred loading the WidgetGridBlock.</h3>{$e->getMessage()}</div>";
}