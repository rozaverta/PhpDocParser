<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 18:07
 */

namespace RozaVerta\PhpDocParser;

use RozaVerta\PhpDocParser\Collections\DocBlockCollection;
use RozaVerta\PhpDocParser\DocBlocks\DocBlock;
use RozaVerta\PhpDocParser\Exceptions\FileNotFoundException;
use RozaVerta\PhpDocParser\Exceptions\ParserErrorException;

class Factory
{
	public static function getDocBlocksClasses()
	{
		static $dumb = null;
		if( !isset($dumb) )
		{
			$dumb = [];
			$reflector = new \ReflectionClass( DocBlock::class );
			$prefix = $reflector->getNamespaceName() . "\\Doc";

			foreach([
				        "api",
				        "author",
				        "category",
				        "copyright",
				        "deprecated",
				        "example",
				        "filesource",
				        "global",
				        "ignore",
				        "internal",
				        "license",
				        "link",
				        "method",
				        "package",
				        "param",
				        "property",
				        "property-read",
				        "property-write",
				        "return",
				        "see",
				        "since",
				        "source",
				        "subpackage",
				        "throws",
				        "todo",
				        "uses",
				        "var",
				        "version"] as $name) {
				$dumb[$name] =
					$prefix .
					ucfirst($name[0]) .
					preg_replace_callback('/-([a-z])/', static function($m) { return ucfirst($m[1]); }, substr($name, 1))
				;
			}
		}

		return $dumb;
	}

	public static function parseClass( $class_name ): Reflector
	{
		if( ! class_exists($class_name, true) )
		{
			throw new \InvalidArgumentException("Class '{$class_name}' not found");
		}

		$ref = new \ReflectionClass($class_name);

		$file = $ref->getFileName();
		if( $file )
		{
			return self::parseFile($file);
		}
		else
		{
			return (new ParserNativeReflectionClass($ref))->getReflector();
		}
	}

	public static function parseFile( $file, $include_once = true ): Reflector
	{
		$file_info = new \SplFileInfo($file);

		if( ! $file_info->isFile() )
		{
			throw new FileNotFoundException("File '{$file}' not found");
		}

		if( $include_once && $file_info->getExtension() === "php" )
		{
			include_once $file;
		}

		$code = file_get_contents($file);
		if( $code === false )
		{
			throw new ParserErrorException("Can not ready file '{$file}'");
		}

		$parser = new ParserLexer($code);
		$ref = $parser->getReflector();
		$ref->setFile($file_info);

		return $ref;
	}

	public static function parse( $code ): Reflector
	{
		return (new ParserLexer($code))->getReflector();
	}

	public static function parseDocBlocks(string $text): DocBlockCollection
	{
		return (new DocBlocks($text))->getCollection();
	}
}