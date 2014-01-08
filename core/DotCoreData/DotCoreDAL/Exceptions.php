<?php

class NotReadyForInsertionException extends DotCoreException {}
class InvalidRestraintClosingException extends DotCoreException {}
class InvalidKeyFieldException extends DotCoreException {}
class InvalidFieldException extends DotCoreException {}
class InvalidFulltextException extends DotCoreException {}

/**
 * An exception thrown whenever a unique field tries to be populated with a non-unique value
 * @author perrin
 *
 */
class UniqueKeyException extends DotCoreException
{
    public function  __construct($keys = array(), $message = NULL) {
        parent::__construct($message);

        $this->keys = $keys;
    }

    /**
     * Holds the keys whose unique values were violated
     * @var array
     */
    private $keys = array();

    public function SetKeys($keys) {
        $this->keys = $keys;
        return $this;
    }

    public function GetKeys() {
        return $this->keys;
    }
}

?>
