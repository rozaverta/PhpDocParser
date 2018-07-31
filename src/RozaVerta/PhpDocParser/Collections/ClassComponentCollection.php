<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:04
 */

namespace RozaVerta\PhpDocParser\Collections;

use RozaVerta\PhpDocParser\Components\ClassComponent;

class ClassComponentCollection extends AbstractComponentCollection
{
	protected function hasInstanceOf( $instance )
	{
		return $instance instanceof ClassComponent;
	}
}