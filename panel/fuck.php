<?php
$trans = array("hello" => "hi", "hi" => "poop");

$str = "hi all, I said hello";

echo 'original: ' . $str .'<br>';

echo 'replaced: ' . strtr($str, $trans);
?>