<?php

use Blocks\WidgetGridBlock;

require_once ('blocks_autoload.php');

$gridBlock = new WidgetGridBlock();

echo $gridBlock->getOutput();
