<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:09
 */

namespace RozaVerta\PhpDocParser\Interfaces;


interface DocComponent extends \JsonSerializable
{
	public function getNameSpace();

	public function getName();

	public function getFullName();

	public function getDocBlocks();
}