<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components;

use RozaVerta\PhpDocParser\Exceptions\NotFoundException;
use RozaVerta\PhpDocParser\Interfaces\DocComponentReflector;

class FunctionComponent extends ComponentAbstract implements DocComponentReflector
{
	use Traits\ComponentReflectorTrait;

	public function __construct( $name, $name_space, $doc_blocks = null )
	{
		$func_name = $name;
		if($name_space)
		{
			$func_name = $name_space . "\\" . $func_name;
		}

		try {
			$this->reflector = new \ReflectionClass($func_name);
			$native_doc_block = $this->reflector->getDocComment();
			if( $native_doc_block )
			{
				$doc_blocks = $native_doc_block;
			}
		}
		catch(\ReflectionException $e) {
			throw new NotFoundException( $e->getMessage(), 0, $e );
		}

		parent::__construct( $name, $name_space, $doc_blocks );
	}
}