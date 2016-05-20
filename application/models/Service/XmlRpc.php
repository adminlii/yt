<?php
class Service_XmlRpc {
	/**
	 * newPost
	 *
	 * @param int $mailAccountId        	
	 * @param array $Tos        	
	 * @param array $Ccs        	
	 * @param array $Bccs        	
	 * @param string $subject        	
	 * @param string $body        	
	 * @param array $attachmentNames        	
	 * @param array $attachments        	
	 * @return string
	 */
	function sendMail($mailAccountId, $Tos, $Ccs=array(), $Bccs=array(), $subject="", $body="", $attachmentNames=array(), $attachments=array()) {
		
	    return print_r($Tos,true);
	}
	
}