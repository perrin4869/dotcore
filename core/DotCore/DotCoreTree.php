<?php

/**
 * Defines a node in a hierarchial tree
 */
class DotCoreTree {

	public function  __construct(DotCoreTreeNode $root = NULL) {
		if($root == NULL) {
			$root = new DotCoreTreeNode();
		}
		$this->root = $root;
	}

	/**
	 * Holds the root of the tree
	 * @var DotCoreTreeNode
	 */
	public $root;

}

?>
