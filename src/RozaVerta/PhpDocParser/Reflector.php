<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 31.07.2018
 * Time: 3:30
 */

namespace RozaVerta\PhpDocParser;

use RozaVerta\PhpDocParser\Interfaces\DocBlock;

class Reflector
{
	/**
	 * @var \SplFileInfo | null
	 */
	private $file = null;

	/**
	 * @var Collections\NameSpaceComponentCollection
	 */
	private $name_spaces;

	/**
	 * @var Collections\DocBlockCollection
	 */
	private $doc_blocks;

	/**
	 * @var Collections\ClassComponentCollection
	 */
	private $classes;

	/**
	 * @var Collections\FunctionComponentCollection
	 */
	private $functions;

	/**
	 * @var Collections\ConstantComponentCollection
	 */
	private $constants;

	public function __construct()
	{
		$this->doc_blocks = new Collections\DocBlockCollection();
		$this->name_spaces = new Collections\NameSpaceComponentCollection();
		$this->classes = new Collections\ClassComponentCollection();
		$this->functions = new Collections\FunctionComponentCollection();
		$this->constants = new Collections\ConstantComponentCollection();
	}

	public function hasFile()
	{
		return ! is_null($this->file);
	}

	/**
	 * @return mixed
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param \SplFileInfo $file
	 * @return $this
	 */
	public function setFile( \SplFileInfo $file )
	{
		$this->file = $file;
		return $this;
	}

	public function addNameSpace( Components\NameSpaceComponent $ns )
	{
		$this->name_spaces[] = $ns;
		return $this;
	}

	public function addConstant( Components\ConstantComponent $constant )
	{
		$this->constants[] = $constant;
		return $this;
	}

	public function addClass( Components\ClassComponent $class )
	{
		$this->classes[] = $class;
		return $this;
	}

	public function addFunction( Components\FunctionComponent $function )
	{
		$this->functions[] = $function;
		return $this;
	}

	public function addDocBlock( DocBlock $doc_block )
	{
		$this->doc_blocks[] = $doc_block;
		return $this;
	}

	/**
	 * @return Collections\NameSpaceComponentCollection
	 */
	public function getNameSpaces(): Collections\NameSpaceComponentCollection
	{
		return $this->name_spaces;
	}

	/**
	 * @return Collections\DocBlockCollection
	 */
	public function getDocBlocks(): Collections\DocBlockCollection
	{
		return $this->doc_blocks;
	}

	/**
	 * @return Collections\ClassComponentCollection
	 */
	public function getClasses(): Collections\ClassComponentCollection
	{
		return $this->classes;
	}

	/**
	 * @return Collections\FunctionComponentCollection
	 */
	public function getFunctions(): Collections\FunctionComponentCollection
	{
		return $this->functions;
	}

	/**
	 * @return Collections\ConstantComponentCollection
	 */
	public function getConstants(): Collections\ConstantComponentCollection
	{
		return $this->constants;
	}
}