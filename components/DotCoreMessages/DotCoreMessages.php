<?php

/**
 * Class used for managing the languages of the system
 * @author perrin
 *
 */
class DotCoreMessages extends DotCoreObject implements ArrayAccess, Countable
{
	
    public function __construct($filename = NULL, $languages = NULL)
    {
        if($filename != NULL) {
            $this->filename = $filename;

            if($languages == NULL)
            {
                $this->languages = array();
            }
            else
            {
                $this->languages = $languages;
            }

            include $filename;
            $this->messages = $messages;
        }
        else {
            $this->messages = array(); // Just make an empty messages object
        }
    }


    /*
     *
     * Properties:
     *
     */

    const VALUES = 'values';
    const LABELS = 'labels';

    /**
     * Holds the file to which this messages object is associated
     * @var string
     */
    private $filename = NULL;

    /**
     * Holds the messages and labels as defined in the language file
     * @var array
     */
    private $messages = NULL;

    /**
     * Holds the code of the languages, in order of priority, by which the message value is chosen
     * @var array
     */
    private $languages = NULL;

    /**
     * Defines whether the language should be guessed, or an empty string should be returned
     * in case the message doesn't exist in the requested languages
     * @var boolean
     */
    private $on_empty_guess = FALSE;

    /**
     * Holds the code of the languages, in order of priority, by which the labels are chosen
     * @var array
     */
    private $labels_languages = NULL;

    /**
     * Defines whether the language should be guessed, or an empty string should be returned
     * in case the message doesn't exist in the requested languages
     * @var boolean
     */
    private $labels_on_empty_guess = TRUE;



    /*
     *
     * Accessors:
     *
     */

    public function getFilename() {
        return $this->filename;
    }

    public function getMessagesArray() {
        return $this->messages;
    }

    /**
     * Gets the languages in order as they are used in order to choose the language of the message, according to availability
     * @return array
     */
    public function getMessagesLanguages() {
        return $this->languages;
    }

    public function getOnEmptyGuess() {
        return $this->on_empty_guess;
    }

    /**
     * Gets the languages in order as they are used in order to choose the language of the label, according to availability
     * @return array
     */
    public function getLabelsLanguages() {
        return $this->labels_languages;
    }

    public function getLabelsOnEmptyGuess() {
        return $this->labels_on_empty_guess;
    }

    /**
     *
     * @param string $filename
     * @return DotCoreMessages
     */
    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }

    /**
     *
     * @param array $languages
     * @return DotCoreMessages
     */
    public function setMessagesLanguages($languages) {
        $this->languages = $languages;
        return $this;
    }

    /**
     *
     * @param boolean $bool
     * @return DotCoreMessages
     */
    public function setOnEmptyGuess($bool) {
        $this->on_empty_guess = $bool;
        return $this;
    }

    /**
     *
     * @param array $languages
     * @return DotCoreMessages
     */
    public function setLabelsLanguages($languages) {
        $this->labels_languages = $languages;
        return $this;
    }

    /**
     *
     * @param boolean $bool
     * @return DotCoreMessages
     */
    public function setLabelsOnEmptyGuess($bool) {
        $this->labels_on_empty_guess = $bool;
        return $this;
    }

    /**
     * Gets the messages stored in $file if one is given inside a dictionary
     * If no file is given, it'll return the default website dictionary
     * @param string $file
     * @param string $language_code The code of the language whose messages are desired
     * @param array $fallback_languages The languages to choose from, in order, in case a message does not exist in $language_code
     * @return DotCoreMessages
     */
    public static function GetMessages($file, $language_code = NULL, $fallback_languages = NULL)
    {
        $languages = array();
        if($language_code != NULL) {
            $languages[0] = $language_code;

            if($fallback_languages != NULL)
            {
                if(!is_array($fallback_languages))
                {
                    $fallback_languages = array($fallback_languages);
                }
                $languages = array_merge($languages, $fallback_languages);
            }
        }

        // Assume the file exists - assume the calling function checked it
        return new DotCoreMessages($file, $languages);
    }

    /**
     * Internal method used to choose a message, taking into account the available languages, and the languages requested
     * @param array $messages
     * @param array $languages
     */
    protected static function GetMessageByLanguages($messages, $languages, $on_empty_guess = FALSE) {
        if(!is_array($messages))
        {
            $result = '';
        }
        elseif(empty($languages))
        {
            // No specific language was requested - draw the first one
            $result = current($messages);
        }
        else {
            $count_languages = count($languages);
            $found = FALSE;
            for($i = 0; $i < $count_languages; $i++)
            {
                if(key_exists($languages[$i], $messages))
                {
                    $found = TRUE;
                    $result = $messages[$languages[$i]];
                    break;
                }
            }

            if(!$found)
            {
                if($on_empty_guess == TRUE)
                {
                    $result = current($messages);
                }
                else
                {
                    $result = '';
                }
            }
        }

        return $result;
    }

    public function GetMessage($message_label) {
        $messages = $this->messages[$message_label][self::VALUES];
        return self::GetMessageByLanguages($messages, $this->languages, $this->on_empty_guess);
    }
    
    public function IsMessageTranslated($message, $language_code) {
        return
            is_array($this->messages[$message]) &&
            is_array($this->messages[$message][self::VALUES]) &&
            key_exists($language_code, $this->messages[$message][self::VALUES]);
    }

    public function GetMessageByLanguage($message_label, $language_code) {
        return $this->messages[$message_label][self::VALUES][$language_code];
    }

    public function SetMessage($message, $language_code, $value) {
        $this->messages[$message][self::VALUES][$language_code] = $value;
    }

    public function GetLabel($message_label) {
        $labels = $this->messages[$message_label][self::LABELS];
        return self::GetMessageByLanguages($labels, $this->labels_languages, $this->labels_on_empty_guess);
    }

    /**
     * Gets all the messages available
     * @return array
     */
    public function GetMessagesKeys() {
        return array_keys($this->messages);
    }

    /**
     * Saves the changes to the messages inside $file_name (or the stored file if any)
     * @param string $file_name
     *
     * @return boolean TRUE on success, FALSE on failure
     */
    public function SaveToFile($file_name = NULL) {

        if($file_name == NULL) {
            $file_name = $this->filename;
        }

        // Build the giant string
        $class = $this->GetType();
        $result = '';
        $result .= '<?php
$messages = array();
        ';

        foreach($this->messages as $msg_key => $message) {
            $result .= '
$messages[\''.$msg_key.'\'] = array();
$messages[\''.$msg_key.'\']['.$class.'::VALUES] = array(
';
            $values_array = array();
            foreach($message[self::VALUES] as $lang_code => $value)
            {
                array_push($values_array, '\''.$lang_code.'\' => \''.str_replace('\'', '\\\'', $value).'\'');
            }
            $result .= "\t" . join(",\n\t", $values_array);
            $result .= '
);
$messages[\''.$msg_key.'\']['.$class.'::LABELS] = array(
';
            $labels_array = array();
            foreach($message[self::LABELS] as $lang_code => $value)
            {
                array_push($labels_array, '\''.$lang_code.'\' => \''.$value.'\'');
            }
            $result .= "\t" . join(",\n\t", $labels_array);
            $result .= '
);';
        }

        $result .= '?>';

        return file_put_contents($file_name, $result) == TRUE;
    }

    /*
     *
     * ArrayAccess implementation
     *
     */

    /**
     * Defined by ArrayAccess interface
     * Set a value given it's key e.g. $A['title'] = 'foo';
     * @param mixed key (string or integer)
     * @param mixed value
     * @return void
     */
    function offsetSet($key, $value) {
        throw new Exception('Can\'t set by offset.');
    }

    /**
     * Defined by ArrayAccess interface
     * Return a value given it's key e.g. echo $A['title'];
     * @param mixed key (string or integer)
     * @return mixed value
     */
    function offsetGet($key) {
        return $this->GetMessage($key);
    }

    /**
     * Defined by ArrayAccess interface
     * Unset a value by it's key e.g. unset($A['title']);
     * @param mixed key (string or integer)
     * @return void
     */
    function offsetUnset($key) {
        throw new Exception('Can\'t unset by offset.');
    }

    /**
     * Defined by ArrayAccess interface
     * Check value exists, given it's key e.g. isset($A['title'])
     * @param mixed key (string or integer)
     * @return boolean
     */
    function offsetExists($offset) {
        return key_exists($offset, $this->messages);
    }

    /*
     *
     * Countable implementation
     *
     */

    /**
     * Defined by Countable interface
     * Returns the number of items in this this list (when requested by count(), for example)
     * @return int
     *
     */
    public function count()
    {
        return count($this->arr);
    }

    public function merge(DotCoreMessages $messages) {
        $this->messages = array_merge($messages->getMessagesArray(), $this->messages);
        // The second parameter overrides the values in the first parameter
    }
}

?>
