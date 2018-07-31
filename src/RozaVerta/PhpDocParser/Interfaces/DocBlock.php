<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:09
 */

namespace RozaVerta\PhpDocParser\Interfaces;


interface DocBlock extends \JsonSerializable
{
	public function getName(): string;

	public function getText(): string;

	public function getContext(): string;

	public function getLines(): array;

	public function isValid(): bool;

	public function isTextNode(): bool;
}