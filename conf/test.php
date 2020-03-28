<?php

$p_file = "test.txt";

$o_file = fopen($p_file,'a+');

print_r($o_file);
print_r(fwrite($o_file, 'oooo'));


fclose($o_file);
