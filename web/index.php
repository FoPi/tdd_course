<?php
require_once(dirname(__DIR__) . "/src/MessageParser.php");

$rawMessage = file_get_contents(dirname(__DIR__) . "/resource/messageParser");

$messageParser = new MessageParser();
echo $messageParser->parse($rawMessage);
