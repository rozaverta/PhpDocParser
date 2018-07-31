<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:04
 */

namespace RozaVerta\PhpDocParser\Collections;

use RozaVerta\PhpDocParser\Components\ConstantComponent;

class ConstantComponentCollection extends AbstractComponentCollection
{
	protected function hasInstanceOf( $instance )
	{
		return $instance instanceof ConstantComponent;
	}
}