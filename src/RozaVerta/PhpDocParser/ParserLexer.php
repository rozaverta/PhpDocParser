<?php
/**
 * Created by IntelliJ IDEA.
 * User: GoshaV [Maniako] <gosha@rozaverta.com>
 * Date: 01.05.2018
 * Time: 18:07
 */

namespace RozaVerta\PhpDocParser;

use RozaVerta\PhpDocParser\Components\ClassComponent;
use RozaVerta\PhpDocParser\Components\ConstantComponent;
use RozaVerta\PhpDocParser\Components\FunctionComponent;
use RozaVerta\PhpDocParser\Components\NameSpaceComponent;

class ParserLexer extends AbstractParser
{
	private const TYPE_NULL = 1;

	private const TYPE_NS = 2;

	private const TYPE_CLASS = 3;

	private $lexeme = [];

	private $pos = 0;

	private $all = 0;

	private $depth = 0;

	private $level = 0;

	private $code = false;

	private $type = "NULL";

	private $doc_comment = false;

	private $name_space = false;

	private $file_doc_comment = false;

	private $name_spaces = [];

	private $classes = [];

	private $functions = [];

	private $constants = [];

	public function __construct( string $text )
	{
		parent::__construct();

		$this->lexeme = token_get_all($text);
		$this->pos = 0;
		$this->all = count($this->lexeme);
		$this->type = self::TYPE_NULL;

		$this->parse();
	}

	private function parse()
	{
		$start = true;

		while( $this->pos < $this->all )
		{
			$token = $this->lexeme[$this->pos++];
			$type = is_array($token) ? $token[0] : 0;

			/* open php code : <?php */
			if( ! $this->code )
			{
				$this->code = $type === T_OPEN_TAG;
				continue;
			}

			/* close php code : ?> */
			if( $type === T_CLOSE_TAG )
			{
				$this->code = false;
				continue;
			}

			if( $type === T_DOC_COMMENT && ($this->type === self::TYPE_NULL || $this->type === self::TYPE_NS) )
			{
				if( $start && $this->type === self::TYPE_NULL && $this->doc_comment )
				{
					$this->file_doc_comment = $this->doc_comment;
					$start = false;
				}

				$this->doc_comment = $token[1];
				continue;
			}

			if( $type === 0 && $token === "{" || ($type === T_CURLY_OPEN || $type === T_DOLLAR_OPEN_CURLY_BRACES) && strpos($token[1], '{') !== false )
			{
				$this->depth ++;
			}

			else if( $type === 0 && $token === "}" )
			{
				if($this->depth -- > 0) {
					if( $this->depth === 0 && $this->type === self::TYPE_NS && $this->level > 0 )
					{
						$this->level = 0;
						$this->type = self::TYPE_NULL;
						$this->name_space = false;
					}
					else if( $this->depth === $this->level && $this->type === self::TYPE_CLASS )
					{
						$this->type = ($this->level > 0 || $this->name_space) ? self::TYPE_NS : self::TYPE_NULL;
					}
				}
				else
				{
					throw new \InvalidArgumentException("ParserLexer error, close quote ($this->pos)" . print_r($this->lexeme, true));
				}
			}

			else if( $this->type === self::TYPE_NULL || $this->type === self::TYPE_NS )
			{
				if( $type === T_NAMESPACE && ($this->type === self::TYPE_NULL || $this->level < 1) )
				{
					$found = $this->getData(0, [";", "{"], false);
					if( $found->count > 1 )
					{
						throw new \InvalidArgumentException("ParserLexer error, namespace syntax");
					}

					$this->type = self::TYPE_NS;
					if($found->count > 0)
					{
						$this->name_space = $found->found[0];
						$this->name_spaces[] = $this->name_space;
					}
					else if($found->token !== "{")
					{
						throw new \InvalidArgumentException("ParserLexer error, namespace token syntax");
					}
					else
					{
						$this->name_space = false;
					}

					if($found->token === "{")
					{
						$this->level = 1;
						$this->depth ++;
					}

					$this->isDoc($start);
				}
				else if( $type === T_CLASS || $type === T_TRAIT || $type === T_INTERFACE )
				{
					$found = $this->getData(0, ["{"]);

					$this->classes[] = [
						"type" => strtolower( substr( token_name($type), 2) ),
						"name" => $found->found[0],
						"name_space" => $this->name_space
					];

					$this->type = self::TYPE_CLASS;
					$this->doc_comment = false;
					$this->depth ++;
				}
				else if( $type === T_FUNCTION && strtolower($token[1]) === "function" && $this->depth === $this->level )
				{
					$found = $this->getData(0, ["("], false);
					if($found->count)
					{
						if($found->count > 1)
						{
							throw new \InvalidArgumentException("ParserLexer error, function syntax");
						}

						$this->functions[] = [
							"name" => $found->found[0],
							"name_space" => $this->name_space,
							"doc_comment" => $this->doc_comment
						];

						$this->doc_comment = false;
					}
					else
					{
						$this->isDoc( $start );
					}
				}
				else if( $type === T_CONST )
				{
					$found = $this->getData(0, [";"]);
					if( $found->count < 2 || $found->found[1] !== "=" )
					{
						throw new \InvalidArgumentException("ParserLexer error, const ready fail");
					}

					$this->constants[] = [
						"name" => $found->found[0],
						"name_space" => $this->name_space,
						"doc_comment" => $this->doc_comment
					];

					$this->doc_comment = false;
				}
				else if( $type !== T_WHITESPACE )
				{
					$this->isDoc( $start );
				}
				else
				{
					continue;
				}
			}

			if($start)
			{
				$start = false;
			}
		}

		if( $this->depth !== 0 || $this->level !== 0 )
		{
			throw new \InvalidArgumentException("ParserLexer error, end of");
		}

		$ref = $this->getReflector();

		if( $this->file_doc_comment )
		{
			foreach(Factory::parseDocBlocks($this->file_doc_comment) as $doc_block)
			{
				$ref->addDocBlock($doc_block);
			}
		}

		// name spaces

		foreach( $this->name_spaces as $name_space )
		{
			$ref->addNameSpace( new NameSpaceComponent($name_space) );
		}

		foreach( $this->classes as $class )
		{
			$ref->addClass( new ClassComponent($class['name'], $class['name_space']) );
		}

		foreach( $this->constants as $constant )
		{
			$ref->addConstant( new ConstantComponent($constant['name'], $constant['name_space'], $constant['doc_comment']) );
		}

		foreach( $this->functions as $function )
		{
			$ref->addFunction( new FunctionComponent($function['name'], $function['name_space'], $function['doc_comment']) );
		}
	}

	private function isDoc( $start )
	{
		if($this->doc_comment)
		{
			if($start)
			{
				$this->file_doc_comment = $this->doc_comment;
			}
			$this->doc_comment = false;
		}
	}

	private function getData( $types = 0, array $string = [], $notEmpty = true )
	{
		$str = "";
		$end_type = 0;
		$end_text = "";

		while( $this->pos < $this->all )
		{
			$token = $this->lexeme[$this->pos++];
			$type = is_array($token) ? $token[0] : 0;

			if( $type === 0 )
			{
				if( in_array($token, $string, true) )
				{
					$end_text = $token;
					break;
				}
				$str = rtrim($str) . " " . trim($token);
			}
			else if( $type & $types )
			{
				$end_type = $type;
				break;
			}
			else if( $type === T_WHITESPACE )
			{
				$str = rtrim($str) . " ";
			}
			else if( $type === T_STRING || $type === T_NS_SEPARATOR || $type === T_DOUBLE_COLON )
			{
				$str .= trim($token[1]);
			}
		}

		$str = trim($str);
		if( ($notEmpty && !strlen($str)) || ($end_text === 0 && !strlen($end_text)) )
		{
			throw new \InvalidArgumentException("Parse error, needle not found");
		}

		$std = new \stdClass();
		$std->found = strlen($str) ? explode(" ", $str) : [];
		$std->count = count($std->found);
		$std->type  = $end_type;
		$std->token = $end_text;

		return $std;
	}
}