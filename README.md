# PhpDoc Parser

The project is under development.

---

This is a easy PHP 7+ parser written in PHP.

```php
<?php
/**
 * File description comment
 */

namespace Foo\Bar;

use RozaVerta\PhpDocParser\Components\ClassComponent;
use RozaVerta\PhpDocParser\Components\MethodComponent;
use RozaVerta\PhpDocParser\Components\NameSpaceComponent;
use RozaVerta\PhpDocParser\Factory;
use RozaVerta\PhpDocParser\Interfaces\DocBlock;

class YourClassName
{
	/**
	 * Test method
	 * @return bool
	 */
	public function test() { return true; }
}

$reflector = Factory::parseFile(__FILE__);
$docs = $reflector->getDocBlocks(); // file phpDoc block collection
$ns = $reflector->getNameSpaces(); // file namespaces collection
$classes = $reflector->getClasses(); // file classes collection

echo $docs->getDescription() . "\n\n";
// output >>
// File description comment

/** @var NameSpaceComponent $name_space */
foreach( $ns as $name_space)
{
	echo $name_space->getNameSpace() . "\n";
	echo $name_space->getName() . "\n";
	echo $name_space->getFullName() . "\n\n";
	
	// output >>
	// Bar
	// Foo
	// Foo\Bar
}

/** @var ClassComponent $class */
foreach( $classes as $class )
{
	echo $class->getNameSpace() . "\n";
	echo $class->getName() . "\n";
	echo $class->getFullName() . "\n\n";
	
	// output >>
	// Foo\Bar
	// YourClassName
	// Foo\Bar\YourClassName

	/** @var MethodComponent $method */
	foreach($class->getMethodComponents() as $method)
	{
		echo $method->getNameSpace() . "\n";
		echo $method->getName() . "\n";
		echo $method->getFullName() . "\n\n";

		// output >>
		// Foo\Bar\YourClassName
		// test
		// Foo\Bar\YourClassName::test
		
		/** @var DocBlock $doc_block */
		foreach( $method->getDocBlocks() as $doc_block)
		{
			if( $doc_block->isTextNode() )
			{
				echo "- text:\n" . $doc_block->getContext() . "\n";
			}
			else
			{
				echo "- phpDoc: @" . $doc_block->getName() . "\n" . $doc_block->getContext();
			}
		}

		// output >>
		// - text:
		// Test method
		// - phpDoc: @return
		// bool
	}
}
```