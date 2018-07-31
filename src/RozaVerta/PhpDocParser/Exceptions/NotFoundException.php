<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 18:59
 */

namespace RozaVerta\PhpDocParser\Exceptions;


class NotFoundException extends \InvalidArgumentException
{
	const IS_FILE     = 1;
	const IS_CLASS    = 2;
	const IS_METHOD   = 3;
	const IS_FUNCTION = 4;
	const IS_PROPERTY = 5;
	const IS_CONSTANT = 6;
	const IS_CALLBACK = 7;
}