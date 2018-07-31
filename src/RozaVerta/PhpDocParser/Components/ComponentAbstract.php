<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 19:55
 */

namespace RozaVerta\PhpDocParser\Components;

use RozaVerta\PhpDocParser\Collections\DocBlockCollection;
use RozaVerta\PhpDocParser\Factory;
use RozaVerta\PhpDocParser\Interfaces\DocComponent;

abstract class ComponentAbstract implements DocComponent
{
	protected $name;

	protected $name_space;

	protected $doc_blocks;

	protected $name_namespace_delimiter = "\\";

	public function __construct( $name, $name_space, $doc_blocks = null )
	{
		$this->name = $name;
		$this->name_space = $name_space;
		$this->doc_blocks = is_null( $doc_blocks ) ? new DocBlockCollection() : Factory::parseDocBlocks( $doc_blocks );
	}

	public function getNameSpace()
	{
		return $this->name_space;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getFullName()
	{
		return ( $this->name_space ? ( $this->name_space . $this->name_namespace_delimiter ) : "" ) . $this->name;
	}

	public function getDocBlocks()
	{
		return $this->doc_blocks;
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return [
			"name" => $this->getName(),
			"name_space" => $this->getNameSpace(),
			"full_name" => $this->getFullName(),
			"doc_blocks" => $this->getDocBlocks()->jsonSerialize()
		];
	}
}