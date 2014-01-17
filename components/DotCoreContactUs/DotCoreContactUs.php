<?php

class ContactUsParserEmptyEmailException extends DotCoreException {}
class ContactUsParserInvalidEmailException extends DotCoreException {}
class ContactUsParserSendingException extends DotCoreException {}

/**
 *
 * @author perrin
 */
class DotCoreContactUs extends DotCoreObject {

	/**
	 * Constructor for DotCoreContactUsParser
	 * 
	 */
	public function  __construct() {
		
	}

	/**
	 *
	 * @var DotCoreMessages 
	 */
	private static $fields = NULL;
	/**
	 *
	 * @var DotCoreMessages
	 */
	private static $messages = NULL;

	/**
	 *
	 * @param DotCoreLanguageRecord $lang
	 * @return DotCoreMessages
	 */
	public static function GetContactUsFields(DotCoreLanguageRecord $lang) {
		if(self::$fields == NULL) {
			self::$fields = new DotCoreMessages(
				self::GetConfigFolder().'contact_fields.php',
				array(
					$lang->getLanguageCode()
				)
			);
		}
		return self::$fields;
	}

	public static function GetMessages(DotCoreLanguageRecord $lang) {
		if(self::$messages == NULL) {
			self::$messages = new DotCoreMessages(
				self::GetConfigFolder().'lang.php',
				array(
					$lang->getLanguageCode()
				)
			);
		}
		return self::$messages;
	}

	public static function GetConfigFolder() {
		$contact_us_configuration = DotCoreExternalComponentsAutoloader::GetComponentsConfiguration('DotCoreContactUs');
		if(isset($contact_us_configuration['config_folder'])) {
			return $contact_us_configuration['config_folder'];
		}
		else {
			return DotCoreConfig::$LOCAL_COMPONENTS_PATH.'DotCoreContactUs/';
		}
	}

	public static function ParseContactUsForm($values, DotCoreLanguageRecord $lang, $suffix = '') {
		$messages = self::GetMessages($lang);

		$email = $values['contact_us_email'.$suffix];
		if(empty($email))
		{
			throw new ContactUsParserEmptyEmailException();
			
		}
		else
		{
			if(!is_email($email))
			{
				throw new ContactUsParserInvalidEmailException();
			}
		}

		// Escape all value's HTML entities
		$body = '<div>';
		$fields = self::GetContactUsFields($lang);
		$keys = $fields->GetMessagesKeys();
		$count_keys = count($keys);
		for($i = 0; $i < $count_keys; $i++) {
			$key = $keys[$i];
			$body .= $fields[$key].': '.LineBreaksToBr(htmlspecialchars($values[$key.$suffix]));
			$body .= '<br />';
		}
		$body .= '</div>';
		
		$mail = new PHPMailer(); // defaults to using php "mail()"

		$contact_us_recipients_bll = new DotCoreContactUsRecipientBLL();
		$contact_us_recipients = $contact_us_recipients_bll
			->Fields(
				array(
					$contact_us_recipients_bll->getFieldName(),
					$contact_us_recipients_bll->getFieldEmail()
				)
			)
			->ByLanguageID($lang->getLanguageID())
			->Select();
		$recipients_count = count($contact_us_recipients);

		if($recipients_count > 0) {
			for($i = 0; $i < $recipients_count; $i++)
			{
				$contact_us_recipient = $contact_us_recipients[$i];
				$mail->AddAddress(
					$contact_us_recipient->getContactUsRecipientEmail(),
					$contact_us_recipient->getContactUsRecipientName());
			}

			$mail->FromName = $title;
			$mail->Subject = $messages['contact_us_subject'];
			$mail->CharSet = 'utf-8';
			$mail->AddReplyTo($email, $name_value);
			$mail->MsgHTML($body);
			if(!$mail->Send()) {
				throw new ContactUsParserSendingException();
			}
		}
		return TRUE;
	}

}
?>
