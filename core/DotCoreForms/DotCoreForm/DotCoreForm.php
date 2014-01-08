<?php

/**
 * DotCoreForm - Holds a form for any purpose
 *
 * @author perrin
 */
class DotCoreForm extends DotCoreObject {

    public function  __construct($form_name, $action, $method = self::FORM_METHOD_POST) {
        $this->form_name = $form_name;
        $this->action = $action;
        $this->form_method = $method;
    }

    const FORM_METHOD_POST = 'post';
    const FORM_METHOD_GET = 'get';

    /**
     * Holds the name of this form, important in scripts with multiple forms
     * @var string
     */
    private $form_name = NULL;

    /**
     * Holds the elements of this form
     * @var array
     */
    private $form_elements = array();

    /**
     * Holds the action of this form (the page to which this form is submitted)
     * @var string
     */
    private $action = NULL;

    /**
     * Defines whether this form support file upload
     * @var bool
     */
    private $supports_upload = FALSE;

    /**
     * Holds all the errors accumulated in the form
     * @var array
     */
    private $messages = array();

    /**
     * Holds global errors for this form
     * @var array
     */
    private $errors = array();

    /**
     * Holds the labels for each element
     * @var array
     */
    private $labels = array();

    /**
     * Stores the method (POST, GET) of this form
     * @var string
     */
    private $form_method;

    /**
     * Holds errors for specific elements
     * @var array
     */
    private $elements_errors = array();

    /**
     * Holds the markup to insert between form elements
     * @var array
     */
    private $markup = array();

    /**
     * Gets the name of this form
     * @return string
     */
    public function GetFormName() {
        return $this->form_name;
    }

    /**
     * Adds a new element to the elements in the form
     * @param DotCoreFormElement $element
     * @return DotCoreForm
     */
    public function AddFormElement(DotCoreFormElement $element, $label = NULL) {
        $this->form_elements[$element->GetName()] = $element;
        if($label != NULL) {
            $this->labels[$element->GetName()] = $label;
        }
        $element->SetForm($this);
        return $this;
    }

    /**
     * Removes the element by the element name $element_name from the form
     * @param string $element_name
     */
    public function RemoveFormElement($element_name) {
        $this->form_elements[$element_name] = NULL;
    }

    /**
     * Gets the form element by the name $element_name
     * @param string $element_name
     * @return DotCoreFormElement
     */
    public function GetFormElement($element_name) {
        return $this->form_elements[$element_name]; // Return the element only
    }

    /**
     * Inserts markup that will appear before the next element that is inserted
     * @param string $markup
     */
    public function InsertMarkup($markup) {
        $markup_position = count($this->form_elements);
        if(key_exists($markup_position, $this->markup)) {
            $this->markup[$markup_position] .= $markup;
        }
        else {
            $this->markup[$markup_position] = $markup;
        }
    }

    /**
     * Adds $message into the messages accumulated from parsing this form
     * @param string $messages
     * @return DotCoreForm
     */
    public function AddMessage($messages) {
        array_push($this->messages, $messages);
        return $this;
    }

    /**
     * Gets the messages accumulated in the parsing of this form
     * @return array
     */
    public function GetMessages() {
        return $this->messages;
    }

	/**
     * Adds an error to this form
     * @param string $error
     * @param string $element_name Optional, can associate the passed error to a certain element
     * @return DotCoreForm
     */
    public function AddError($error, $element_name = NULL) {
    	if(key_exists($element_name, $this->form_elements))
    	{
            if(!key_exists($element_name, $this->elements_errors))
            {
                $this->elements_errors[$element_name] = array();
            }
            array_push($this->elements_errors[$element_name], $error);
    	}
    	else
    	{
            array_push($this->errors, $error);
    	}
        return $this;
    }

    /**
     * Returns whether or not this form has errors in its parsing
     * @return boolean
     */
    public function HasErrors() {
    	return (count($this->errors) != 0 || count($this->elements_errors) != 0);
    }

    /**
     * Checks whether there's an error associated with the form element $form_element_name
     * @param string $form_element_name
     * @return boolean
     */
    public function HasError($form_element_name) {
    	return isset($this->elements_errors[$form_element_name]);
    }

    /**
     * Gets the action of this form, i.e., where this form is
     * @return string
     */
    public function GetAction() {
        return $this->action;
    }

    /**
     * Sets the action of this form
     * @param string $action
     * @return DotCoreForm
     */
    public function SetAction($action) {
        $this->action = $action;
        return $this;
    }

    /**
     * Gets the method of this form
     * @return string
     */
    public function GetFormMethod() {
        return $this->form_method;
    }

    /**
     * Sets the method of this form to $method
     * @param string $method
     * @return DotCoreForm
     */
    public function SetFormMethod($method) {
        $this->form_method = $method;
        return $this;
    }

    /**
     * Checks whether this form supports upload of files
     * @return boolean
     */
    public function SupportsUpload() {
        return $this->supports_upload;
    }

    /**
     * Sets whether or not this form supports file upload
     * @param boolean $boolean
     * @return DotCoreForm
     */
    public function SetSupportsUpload($boolean) {
        $this->supports_upload = $boolean;
        return $this;
    }

    /**
     * Gets the form elements stored in this form
     * @return array
     */
    public function GetFormElements() {
        return $this->form_elements;
    }

    public function WasSubmitted() {
        return isset($_REQUEST[$this->form_name]);
    }

    public function  __toString() {
        // Print the resulting form

        $enctype = $this->supports_upload ? ' enctype="multipart/form-data"' : '';
        $form_content = '';
        $i = 0;
        foreach($this->form_elements as $element) {
            if(key_exists($element->GetName(), $this->labels)) {
                $label = '
                    <div class="label-div">
                        <label for="' . $element->GetID() . '">' . $this->labels[$element->GetName()] . ':</label>
                    </div>';
            }
            else {
                $label = '';
            }

            if(isset($this->elements_errors[$element->GetName()])) {
                $count_errors = count($this->elements_errors[$element->GetName()]);
                $validation_div = '';
                for($i = 0; $i < $count_errors; $i++)
                {
                    $validation_div .= '
                        <div class="field-validation">'.$this->elements_errors[$element->GetName()][$i].'</div>';
                }
            }
            else {
                $validation_div = '';
            }

            $extra_content = $element->GetExtraContent();
            $extra = (!empty($extra_content)) ? '<div class="extra-div">'.$extra_content.'</div>' : '';

            if(key_exists($i, $this->markup)) {
                $form_content .= $this->markup[$i];
            }

            $form_content .= '
                <div class="form-field">
                '.$label.'
                <div class="input-div">'.$element->__toString() .'</div>
                '.$validation_div.'
                '.$extra.'
                </div>';

            $i++;
        }
        // Check if there is markup added AFTER the last element
        if(key_exists($i, $this->markup)) {
            $form_content .= $this->markup[$i];
        }

        $errors = '';
        $total_messages = array_merge($this->messages, $this->errors);
        $count_messages = count($total_messages);
        if($count_messages > 0)
        {
            $errors .= '<div class="feedback">';
            for($i = 0; $i < $count_messages; $i++)
            {
                if($i > 0) {
                    $errors .= '<br />';
                }
                $errors .= $total_messages[$i];
            }
            $errors .= '</div>';
        }

        $result = '
            <form id="'.$this->form_name.'" method="'.$this->form_method.'" action="'.$this->action.'"'.$enctype.'>
                '.$errors.'
                '.$form_content.'
                <div><input type="hidden" name="'.$this->form_name.'" value="submit" /></div>
            </form>';

        return $result;
    }

}

?>
