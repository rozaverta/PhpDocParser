<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components;

class NameSpaceComponent extends ComponentAbstract
{
	public function __construct( $name, $name_space = false, $doc_blocks = null )
	{
		$name = trim($name, "\\");
		$end_of = strrpos($name, "\\");
		if( $end_of !== false )
		{
			$name_space = substr($name, 0, $end_of);
			$name = substr($name, $end_of + 1);
		}

		parent::__construct( $name, $name_space );
	}
}