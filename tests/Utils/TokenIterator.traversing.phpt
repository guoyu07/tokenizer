<?php

/**
 * Test: Nette\Utils\TokenIterator traversing
 */

use Nette\Utils\TokenIterator;
use Nette\Utils\Tokenizer;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


test(function () {
	$tokenizer = new Tokenizer([
		T_DNUMBER => '\d+',
		T_WHITESPACE => '\s+',
		T_STRING => '\w+',
	]);
	$traverser = new TokenIterator($tokenizer->tokenize('say 123'));

	Assert::false($traverser->isPrev());
	Assert::true($traverser->isNext());
	Assert::same([], $traverser->nextAll(T_DNUMBER));
	Assert::same([
		['say', 0, T_STRING],
		[' ', 3, T_WHITESPACE],
	], $traverser->nextUntil(T_DNUMBER));
	Assert::true($traverser->isCurrent(T_WHITESPACE));
	Assert::true($traverser->isPrev());
	Assert::true($traverser->isNext());
	Assert::true($traverser->isPrev(T_STRING));
	Assert::false($traverser->isPrev(T_DNUMBER));
	Assert::true($traverser->isNext(T_DNUMBER));
	Assert::true($traverser->isNext(T_STRING, T_DNUMBER));
	Assert::same([], $traverser->nextUntil(T_STRING, T_DNUMBER, T_WHITESPACE));
	Assert::same([['123', 4, T_DNUMBER]], $traverser->nextAll());
	Assert::true($traverser->isPrev());
	Assert::false($traverser->isNext());
});


test(function () {
	$tokenizer = new Tokenizer([
		T_DNUMBER => '\d+',
		T_WHITESPACE => '\s+',
		T_STRING => '\w+',
	]);
	$traverser = new TokenIterator($tokenizer->tokenize('say 123'));
	$traverser->ignored[] = T_WHITESPACE;

	Assert::same(-1, $traverser->position);
	Assert::same(['say', 0, T_STRING], $traverser->nextToken());
	Assert::same(0, $traverser->position);

	$traverser->position = -1;
	Assert::null($traverser->nextToken(T_DNUMBER));
	Assert::same(-1, $traverser->position);
	Assert::same(['say', 0, T_STRING], $traverser->nextToken(T_STRING));
	Assert::same(0, $traverser->position);

	$traverser->position = -1;
	Assert::same([], $traverser->nextAll(T_DNUMBER));
	Assert::same(-1, $traverser->position);
	Assert::same([['say', 0, T_STRING]], $traverser->nextAll(T_STRING));
	Assert::same(0, $traverser->position);

	$traverser->position = -1;
	Assert::same([], $traverser->nextUntil(T_STRING));
	Assert::same(-1, $traverser->position);
	Assert::same([['say', 0, T_STRING]], $traverser->nextUntil(T_WHITESPACE));
	Assert::same(0, $traverser->position);

	$traverser->position = -1;
	Assert::same(-1, $traverser->position);
	Assert::same([
		['say', 0, T_STRING],
		[' ', 3, T_WHITESPACE],
	], $traverser->nextUntil(T_DNUMBER));
	Assert::same(1, $traverser->position);


	$traverser->position = 0;
	Assert::null($traverser->nextToken(T_STRING));
	Assert::same(0, $traverser->position);
	Assert::same(['123', 4, T_DNUMBER], $traverser->nextToken(T_STRING, T_DNUMBER));
	Assert::same(2, $traverser->position);

	$traverser->position = 0;
	Assert::same([], $traverser->nextAll(T_STRING));
	Assert::same(0, $traverser->position);
	Assert::same([['123', 4, T_DNUMBER]], $traverser->nextAll(T_STRING, T_DNUMBER));
	Assert::same(2, $traverser->position);

	$traverser->position = 0;
	Assert::same([], $traverser->nextUntil(T_WHITESPACE));
	Assert::same(0, $traverser->position);
	Assert::same([[' ', 3, T_WHITESPACE]], $traverser->nextUntil(T_STRING, T_DNUMBER));
	Assert::same(1, $traverser->position);


	$traverser->position = 2;
	Assert::null($traverser->nextToken());
	Assert::null($traverser->nextToken());
	Assert::same(3, $traverser->position);

	$traverser->position = 2;
	Assert::null($traverser->nextToken());
	Assert::null($traverser->nextToken(T_STRING, T_DNUMBER, T_WHITESPACE));
	Assert::same(3, $traverser->position);

	$traverser->position = 2;
	Assert::same([], $traverser->nextAll());
	Assert::same([], $traverser->nextAll(T_STRING, T_DNUMBER, T_WHITESPACE));
	Assert::same(3, $traverser->position);

	$traverser->position = 2;
	Assert::same([], $traverser->nextUntil(T_STRING, T_DNUMBER, T_WHITESPACE));
	Assert::same(2, $traverser->position);
});


test(function () {
	$tokenizer = new Tokenizer([
		'\d+',
		'\s+',
		'\w+',
	]);
	$traverser = new TokenIterator($tokenizer->tokenize('say 123'));
	Assert::null($traverser->nextToken('s'));
	Assert::same(['say', 0], $traverser->nextToken('say'));
	Assert::same([' ', 3], $traverser->nextToken());
});