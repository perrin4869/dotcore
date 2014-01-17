<?php

/**
 * Feature used to 
 * @author perrin
 *
 */
class FeatureEventsList extends DotCoreFeature
{
	public function __construct(DotCoreFeatureRecord $record, $parameters = array())
	{
		parent::__construct($record, $parameters);

		DotCorePageRenderer::GetCurrent()->RegisterHeaderContent('
<link rel="stylesheet" href="'.$this->GetFeatureUrl().'/events_list.css" type="text/css" />
');
	}

	/**
	 * Shows the contact form to the user
	 *
	 */
	public function GetFeatureContent()
	{
		
		$messages = $this->GetMessages(); // Needed to get all the correct messages in the correct language
		$events_bll = new DotCoreEventBLL();

		$string = '';
		$string .= '<div class="events-viewer">';
		
		if(isset($_REQUEST['event_id'])) {
			$event = $events_bll
				->Fields(
					array(
						$events_bll->getFieldTitle(),
						$events_bll->getFieldDescription(),
						$events_bll->getFieldDate(),
						$events_bll->getFieldDetails()
					)
				)
				->ByEventID($_REQUEST['event_id'])
				->SelectFirstOrNull();
			$events_bll->FinalizeSelection();

			if($event == NULL) {
				$string .= '<p class="feedback">'.$messages['EventNotFound'].'</p>';
			}
			else {
				$string .= '
				<h3>'.$event->getEventTitle().'</h3>
				<strong>'.$event->getEventDate().'</strong><br />
				<strong>'.$event->getEventDescription().'</strong>
				<div class="event-contents">'.$event->getEventDetails().'</div>';
			}
		}

		if($event == NULL) {
			
			$events_bll->ByEventLanguageID(DotCorePageRenderer::GetCurrent()->GetLanguage()->getLanguageID());
			if(!isset($_REQUEST['month'])) {
				$month = date('m');
			}
			else {
				$month = $_REQUEST['month'];
			}

			if(!isset($_REQUEST['year'])) {
				$year = date('Y');
			}
			else {
				$year = $_REQUEST['year'];
			}

			if(!isset($_REQUEST['day']))
			{
				$events_bll
					->AndBy()
					->ByMonth($year,$month);
			}
			else
			{
				$events_bll
					->AndBy()
					->ByDate($year,$month,$_REQUEST['day']);
			}


			$search_results = $events_bll
				->Fields(array(
						$events_bll->getFieldDescription(),
						$events_bll->getFieldDetails(),
						$events_bll->getFieldDate(),
						$events_bll->getFieldTitle()
				))
				->OrderedByDate()
				->Select();

			$count_results = count($search_results);
			if($count_results == 0)
			{
				if(!isset($_REQUEST['day'])) {
					$no_events_message = $messages['NoEventsInMonth'];
					$no_events_message = str_replace('[month]', $messages['Month'.intval($month)], $no_events_message);
					$no_events_message = str_replace('[year]', $year, $no_events_message);
					$string .= '<p class="feedback">'.$no_events_message.'</p>';
				}
				else {
					$no_events_message = $messages['NoEventsInDate'];
					$no_events_message = str_replace('[month]', $messages['Month'.intval($month)], $no_events_message);
					$no_events_message = str_replace('[year]', $year, $no_events_message);
					$no_events_message = str_replace('[day]', $_REQUEST['day'], $no_events_message);
					$string .= '<p class="feedback">'.$no_events_message.'</p>';
				}
			}
			else
			{
				$string .= '<ul class="events-search-results">';
				// Print the search results
				for($i = 0; $i < $count_results; $i++)
				{
					$result = $search_results[$i];
					$class = ($i == 0) ? ' class="first"' : '';
					$string .= '<li '.$class.'>'.$this->GetResultMarkup($result).'</li>';
				}
				$string .= '</ul>';
			}

			$next_month_message = $messages['NextMonth'];
			$prev_month_message = $messages['PrevMonth'];

			$page_record = DotCorePageRenderer::GetCurrent()->GetPageRecord();
			$url = DotCorePageBLL::GetPagePath($page_record);
			if($events_bll->HasEventsAfter($year,$month))
			{
				if($month == 12) {
					$next_year = $year + 1;
					$next_month = 1;
				}
				else {
					$next_year = $year;
					$next_month = $month + 1;
				}
				$next_month_message = '<a href="'.$url.'?year='.$next_year.'&month='.$next_month.'">'.$next_month_message.'</a>';
			}
			if($events_bll->HasEventsBefore($year,$month))
			{
				if($month == 1) {
					$prev_year = $year - 1;
					$prev_month = 12;
				}
				else {
					$prev_year = $year;
					$prev_month = $month - 1;
				}
				$prev_month_message = '<a href="'.$url.'?year='.$prev_year.'&month='.$prev_month.'">'.$prev_month_message.'</a>';
			}

			$string .= '<div class="events-nav">' . $prev_month_message . ' | ' . $next_month_message . '</div>';
		}

		$string .= '</div>';
		return $string;
	}

	/**
	 * Gets the markup required to print the event found by the search
	 *
	 * @param DotCoreEventRecord $event
	 * @return string
	 */
	public function GetResultMarkup(DotCoreEventRecord $event)
	{
		$messages = $this->GetMessages(); // Needed to get all the correct messages in the correct language
		$page_record = DotCorePageRenderer::GetCurrent()->GetPageRecord();
		
		$result = '';
		$url = DotCorePageBLL::GetPagePath($page_record);
		$url .= '?event_id=' . $event->getEventID();

		$result .= '
			<h3><a href="'.$url.'">'.$event->getEventTitle().'</a></h3>
			<strong>'.$event->getEventDate().'</strong><br />
			<strong>'.$event->getEventDescription().'</strong>
			<a class="more-details" href="'.$url.'">'.$messages['MoreDetails'].'</a>';

		return $result;
	}
}

?>