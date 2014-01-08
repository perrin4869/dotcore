<?php

/**
 * DotCoreInputFormElement defines a base class for form input elements
 *
 * @author perrin
 */
abstract class DotCoreInputFormElement extends DotCoreFormElement {

    public function  __construct($name) {
        parent::__construct($name);

        $this->id = $name;
    }

    /**
     * Stores the ID of this element
     * @var string
     */
    private $id = NULL;

    /**
     * Defines whether the value is restored between page posts
     * @var boolean
     */
    private $save_value = TRUE;

    /**
     * Holds a custom class to give to the input element
     * @var string
     */
    private $classes = array();

    /**
     * Holds the value to set to this element if no value is set
     * @var mixed
     */
    private $default_value = NULL;

    /**
     * Used to signal whether HTML characters should be encoded prior to printing the value of the element
     * @var bool
     */
    private $accepts_html = FALSE;

    /*
     *
     * Accessors:
     *
     */

    /*
     * Getters:
     */

    public function GetID() {
        return $this->id;
    }

    public function GetDefaultValue() {
        return $this->default_value;
    }

    public function GetClass() {
        return join(' ', $this->classes);
    }

    public function AcceptsHTML() {
        return $this->accepts_html;
    }

    /*
     * Setters:
     */

    public function SetID($id) {
        $this->id = $id;
    }

    public function SetDefaultValue($default_value) {
        $this->default_value = $default_value;
    }

    public function SetAcceptsHTML($bool)
    {
        $this->accepts_html = $bool;
    }

    public function AddClass($class)
    {
        array_push($this->classes, $class);
    }

    public function RemoveClass($class)
    {
        if(($key = array_search($class, $this->classes)) !== FALSE)
        {
            $this->classes[$key] = NULL;
            $this->classes = array_values($this->classes);
        }
    }

    /*
     * Both:
     */

    public function ValueIsSaved($bool = NULL) {
        if($bool === NULL) {
            return $this->save_value;
        }
        else {
            $this->save_value = $bool;
        }
    }

    /*
     *
     * Methods:
     *
     */

    /**
     * Gets the value currently residing in this element
     * @return mixed
     */
    public function GetValue() {
        if($this->IsValueSet()) {
            return $this->GetSubmittedValue();
        }
        else {
            return $this->GetDefaultValue();
        }
    }

    /**
     * Gets the value to display in the element after a postback
     * @return mixed
     */
    protected function GetSavedValue()
    {
        $val = ($this->ValueIsSaved()) ? $this->GetValue() : $this->GetDefaultValue();
        /*
         * We do not html encode it because input fields show encoded charcters normally
         * i.e., &amp; will be shown simply as "&". Meaning that it'll be transparent to the user
         * If he inserts characters that are not allowed, they're encoded by the fields
         * So that the record never gets HTML where it doesn't belong
        if(!$this->accepts_html)
        {
            $val = htmlspecialchars($val);
        }
         * 
         */
        return $val;
    }

    /**
     * Gets the value submitted by this element
     * @return mixed
     */
    public function GetSubmittedValue() {
        return $_REQUEST[$this->GetName()];
    }

    /**
     * Checks whether the value for this element was submitted
     * @return boolean
     */
    public function IsValueSet() {
        return key_exists($this->GetName(), $_REQUEST);
    }

}

?>
