<?php
// Output screenshot:
// http://cl.ly/NsqF
// -------------------------------------------------------

include_once 'console.php';

// ::log method usage
// -------------------------------------------------------
Console::log('Im Red!', 'red');
Console::log('Im Blue on White!', 'white', true, 'blue');

Console::log('I dont have an EOF', false);
Console::log("\tThis is where I come in.", 'light_green');
Console::log('You can swap my variables', 'black', 'yellow');
Console::log(str_repeat('-', 60));

// Direct usage
// -------------------------------------------------------
echo Console::blue('Blue Text') . "\n";
echo Console::black('Black Text on Magenta Background', 'magenta') . "\n";
echo Console::red('Im supposed to be red, but Im reversed!', 'reverse') . "\n";
echo Console::red('I have an underline', 'underline') . "\n";
echo Console::blue('I should be blue on light gray but Im reversed too.', 'light_gray', 'reverse') . "\n";

// Ding!
// -------------------------------------------------------
echo Console::bell();