<?php

/**
 * Test: Nette\Utils\Tokenizer::tokenize with names
 */

declare(strict_types=1);

use Nette\Utils\Tokenizer;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$tokenizer = @new Tokenizer([ // class is deprecated
	T_DNUMBER => '\d+',
	T_WHITESPACE => '\s+',
	T_STRING => '\w+',
]);
$tokens = $tokenizer->tokenize("say \n123");
Assert::same([
	[Tokenizer::VALUE => 'say', Tokenizer::OFFSET => 0, Tokenizer::TYPE => T_STRING],
	[Tokenizer::VALUE => " \n", Tokenizer::OFFSET => 3, Tokenizer::TYPE => T_WHITESPACE],
	[Tokenizer::VALUE => '123', Tokenizer::OFFSET => 5, Tokenizer::TYPE => T_DNUMBER],
], $tokens);

Assert::exception(function () use ($tokenizer) {
	$tokenizer->tokenize('say 123;');
}, Nette\Utils\TokenizerException::class, "Unexpected ';' on line 1, column 8.");
