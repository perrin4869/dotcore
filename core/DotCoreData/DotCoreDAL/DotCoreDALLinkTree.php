<?php
// +------------------------------------------------------------------------+
// | DotCoreDALLinkTree.php                                              |
// +------------------------------------------------------------------------+
// | Copyright (c) Julian Grinblat 2009. All rights reserved.               |
// | Version       0.01                                                     |
// | Last modified 12/03/2010                                               |
// | Email         juliangrinblat@gmail.com                                 |
// | Web           http://www.dotcore.co.il                                 |
// +------------------------------------------------------------------------+

/**
 * Class DotCoreDALSelectQuery
 * Internal class used by DotCoreDAL classes to store and execute queries
 *
 * @version   0.01
 * @author    Julian Grinblat <juliangrinblat@gmail.com>
 * @copyright Julian Grinblat
 * @package   DotCore Project
 * @subpackage external
 */
class DotCoreDALLinkTree extends DotCoreTree {

    public function  __construct(DotCoreDAL $root_dal) {
        parent::__construct();
        $this->root_dal = $root_dal;
    }
    
    /*
     *
     * Properties and accessors:
     *
     */

    /**
     *
     * @var DotCoreDAL
     */
    private $root_dal = NULL;

    /**
     *
     * @return DotCoreDAL
     */
    public function GetRootDAL() {
        return $this->root_dal;
    }

    /*
     *
     * Methods
     *
     */

    public function GetFirstGenerationLinks() {
        return $this->root;
    }

    public function AddLink(DotCoreDALLink $link, DotCoreDALPath $path = NULL) {
        $link_name = $link->GetLinkName();
        if($path == NULL || count($path) < 1) {
            if(!key_exists($link_name, $this->root->nodes)) {
                if(!$link->IsLinkingDALSet()) {
                    $link->SetLinkingDAL($this->GetRootDAL());
                }
                $node = new DotCoreTreeNode();
                $node->value = $link;
                $this->root->nodes[$link_name] = $node;
            }
        }
        else {
            $node = $this->GetLinkNode($path, $linking_dal);
            if($node !== NULL) {
                if(!key_exists($link_name, $node->nodes)) {
                    if(!$link->IsLinkingDALSet()) {
                        $link->SetLinkingDAL($linking_dal);
                    }
                    $new_node = new DotCoreTreeNode();
                    $new_node->value = $link;
                    $node->nodes[$link_name] = $new_node;
                }
            }
            else {
                throw new Exception('DotCoreDALSelectQuery::AddLink - A link is missing in the path.');
            }
        }
    }

    /**
     * Removes the link with name $link_name from the links of this Query
     * @param string $link_name
     * @param DotCoreDALPath
     */
    public function RemoveLink($link_name, DotCoreDALPath $path = NULL)
    {
        if($path == NULL || count($path) < 1) {
            $this->root->nodes[$link_name] = NULL;
        }
        else {
            $node = $this->GetLinkNode($path);
            unset($node->nodes[$link_name]);
        }
    }

    /**
     * Return the node which holds the link at the end of the DotCoreDALPath $path
     * If an additional parameter is provided, it'll be filled with a reference to the DAL at the end of the path
     * @param DotCoreDALPath $path
     * @param $linked_dal
     * @return DotCoreTreeNode
     */
    public function GetLinkNode(DotCoreDALPath $path, &$linked_dal = NULL) {
        $count_path = count($path);
        if($count_path < 1) {
            return NULL;
        }
        else {
            $curr_node = $this->root->nodes[$path[0]];
            $linked_dal = $curr_node->value->GetRelationship()->GetOppositeDAL($this->GetRootDAL());
            for($i = 1; $i < $count_path && $curr_node != NULL; $i++) {
                $curr_node = $curr_node->nodes[$path[$i]];
                $linked_dal = $curr_node->value->GetRelationship->GetOppositeDAL($linked_dal);
            }
            return $curr_node;
        }
    }

    /**
     *
     * @param string $link_name
     * @param DotCoreDALPath $path
     * @return DotCoreDALLink
     */
    public function GetLink($link_name, DotCoreDALPath $path = NULL) {
        if($path == NULL) {
            return $this->root->nodes[$link_name];
        }
        else {
            $link_node = $this->GetLinkNode($path);
            if($link_node) {
                return $link_node->nodes[$link_name];
            }
            else {
                throw new Exception('DotCoreDALSelectQuery::GetLink - Invalid DotCoreDALPath');
            }
        }
    }

    /**
     * Gets the join statement for this Link Tree
     */
    public function GetStatement() {
        $join = '';
        $this->GetJoinStatement($join, $this->GetRootDAL(), $this->GetFirstGenerationLinks());
        return $join;
    }

    /**
     *
     * @param string $join
     * @param DotCoreDALPath $path
     */
    private function GetJoinStatement(&$join, $curr_dal, $node, DotCoreDALPath $path = NULL) {
        $nodes = $node->nodes;
        if($path == NULL) {
            $path = new DotCoreDALPath();
        }
        foreach($nodes as $node) {
            $link = $node->value;
            $join .= $link->GetStatement($path).' ';
            $param_path = clone($path);
            $param_path->append($link->GetLinkName());
            $this->GetJoinStatement($join, $link->GetRelationship()->GetOppositeDAL($curr_dal), $node, $param_path);
        }
    }

}

?>
