<?php

/**
 * DotCoreDateFormElement - Defines a form element used for date input
 *
 * @author perrin
 */
class DotCoreDateFormElement extends DotCoreInputFormElement {

	public function  __construct($name) {
		parent::__construct($name);

		$this->AddClass('date-input-div');
	}

	/**
	 * Sets the default value of this form element as a timestamp
	 * @param int $timestamp 
	 */
	public function SetDefaultTimestampValue($timestamp) {
		$date = date('Y-m-d', $timestamp);
		$this->SetDefaultValue($date);
	}

	public function __toString() {
		$input = '';
		$value = $this->GetSavedValue();
		
		if($value)
		{
			$date_components = split("-", $value);
			$year = $date_components[0];
			$month = $date_components[1];
			$day = $date_components[2];
		}
		else
		{
			$year = date("Y");
			$month = date("m");
			$day = date("d");
		}
		$input .= '<div class="'.$this->GetClass().'">';
		$input .= '<select id="'.$this->GetID().'_day" name="'.$this->GetName().'_day">';
		for($i = 1; $i <= 31; $i++)
		{
			$selected = '';
			if($day == $i)
			{
				$selected = ' selected="selected"';
			}
			$input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		$input .= '</select>';

		$input .= '/';

		$input .= '<select id="'.$this->GetID().'_month" name="'.$this->GetName().'_month">';
		for($i = 1; $i <= 12; $i++)
		{
			$selected = '';
			if($month == $i)
			{
				$selected = ' selected="selected"';
			}
			$input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		$input .= '</select>';

		$input .= '/';

		$currYear = intval(date('Y'));
		$input .= '<select id="'.$this->GetID().'_year" name="'.$this->GetName().'_year">';
		for($i = $currYear - 50; $i <= $currYear + 50; $i++)
		{
			$selected = '';
			if($year == $i)
			{
				$selected = ' selected="selected"';
			}
			$input .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		$input .= '</select>';
		$input .= '</div>';
		return $input;
	}

	/**
	 * Checks whether the value for this element was submitted
	 * @return boolean
	 */
	public function IsValueSet() {
		return
			key_exists($this->GetName().'_day', $_REQUEST) &&
			key_exists($this->GetName().'_month', $_REQUEST) &&
			key_exists($this->GetName().'_year', $_REQUEST);
	}

	public function GetSubmittedValue()
	{
		$day = $_REQUEST[$this->GetName().'_day'];
		$month = $_REQUEST[$this->GetName().'_month'];
		$year = $_REQUEST[$this->GetName().'_year'];
		return $year.'-'.$month.'-'.$day;
	}
	
}
?>
