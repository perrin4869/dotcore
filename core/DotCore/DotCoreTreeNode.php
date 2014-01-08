<?php

/**
 * Defines a node in a hierarchial tree
 */
class DotCoreTreeNode {

    public function  __construct() {

    }

    /**
     * Holds the value of this node
     * @var mixed
     */
    public $value = NULL;

    /**
     * Holds hte child nodes
     * @var array
     */
    public $nodes = array();

}

?>
