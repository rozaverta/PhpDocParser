<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 18:59
 */

namespace RozaVerta\PhpDocParser\Exceptions;

class FileNotFoundException extends NotFoundException
{
	protected $file;

	public function __construct( $message = "", $file, $previous = null )
	{
		parent::__construct( $message, NotFoundException::IS_FILE, $previous );
		$this->file = $file;
	}

	public function getFileName()
	{
		return $this->file;
	}
}