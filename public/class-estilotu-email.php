<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://estoesweb.com
 * @since      1.0.0
 *
 * @package    Estilotu
 * @subpackage Estilotu/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Estilotu
 * @subpackage Estilotu/public
 * @author     Carlos Carmona <ccarmona@estoesweb.com>
 */
class Estilotu_Email {

	private $to = [];
	private $cc = [];
	private $bcc = [];
	private $subject = '';
	private $from = 'EstiloTu <info@estilotu.com>';
	private $headers = [];
	private $attachments = [];
	private $variables = [];
	private $template = FALSE;
	private $sendAsHTML = TRUE;
	
	public function __construct(){}

	/**
	 * Set recipients
	 * @param  Array|String $to
	 * @return Object $this
	 */
	public function to($to) {

		if ( is_array( $to ) ):
			$this->to = $to;
		
		else:
			$this->to = [$to];
		
		endif;
		
		return $this;
	}

	/**
	 * Get recipients
	 * @return Array $to
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * Set Cc recipients
	 * @param  String|Array $cc
	 * @return Object $this
	 */
	public function cc($cc){

		if ( is_array( $cc ) ) :
			$this->cc = $cc;
		
		else:
			$this->cc = [$cc];

		endif;	

		return $this;
	}
	
	/**
	 * Get Cc recipients
	 * @return Array $cc
	 */
	public function getCc(){
		return $this->cc;
	}


	/**
	 * Set Email Bcc recipients
	 * @param  String|Array $bcc
	 * @return Object $this
	 */
	public function bcc($bcc){

		if(is_array($bcc)):
			$this->bcc = $bcc;

		else:
			$this->bcc = [$bcc];

		endif;

		return $this;
	}

	/**
	 * Set email Bcc recipients
	 * @return Array $bcc
	 */
	public function getBcc(){
		return $this->bcc;
	}
	
	/**	
	 * Set email Subject
	 * @param  Srting $subject
	 * @return Object $this
	 */
	public function subject($subject){
		$this->subject = $subject;
		return $this;
	}
	
	/**
	 * Retruns email subject
	 * @return Array
	 */
	public function getSubject(){
		return $this->subject;
	}
	
	/**
	 * Set From header
	 * @param  String
	 * @return Object $this
	 */
	public function from($from){
		$this->from = $from;
		return $this;
	}
	
	/**	
	 * Set the email's headers
	 * @param  String|Array  $headers [description]
	 * @return Object $this
	 */
	public function headers($headers){

		if ( is_array( $headers ) ):
			$this->headers = $headers;
		
		else:
			$this->headers = [$headers];

		endif;
		
		return $this;
	}

	/**
	 * Retruns headers
	 * @return Array
	 */
	public function getHeaders(){
		return $this->headers;
	}
	
	/**
	 * Returns email content type
	 * @return String
	 */
	public static function HTMLFilter(){
		return 'text/html';
	}
	
	/**	
	 * Set email content type
	 * @param  Bool $html
	 * @return Object $this
	 */
	public function sendAsHTML($html){
		$this->sendAsHTML = $html;
		return $this;
	}

	/**	
	 * Attach a file or array of files.
	 * Filepaths must be absolute.
	 * @param  String|Array $path 
	 * @throws Exception
	 * @return Object $this
	 */
	public function attach($path){
		
		if ( is_array( $path ) ){
		
			$this->attachments = [];
		
			foreach($path as $path_) {
				if(!file_exists($path_)){
					throw new Exception("Attachment not found at $path");
				}else{
					$this->attachments[] = $path_;
				}
			}
		
		} else {
		
			if ( !file_exists( $path ) ){
				throw new Exception("Attachment not found at $path");
			}
		
			$this->attachments = [$path];
		}
		
		return $this;
	}


	/**
	 * Set the template file
	 * @param  string $template  Path to HTML template
	 * @param  array  $variables
	 * @throws Exception
	 * @return Object $this
	 */
	public function template($template, $variables = []){
		
		$template = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/emails/' . $template ;
		
		if(!file_exists($template)){
			throw new Exception('File not found');
		}
		
		if(is_array($variables)){ 
			$this->variables = $variables;
		}
		
		$this->template = $template;
		
		return $this;
	}
	
	/**
	 * Renders the template
	 * @return string
	 */
	private function render(){
		$template = file_get_contents($this->template);
		
		preg_match_all('/\{\{\s*.+?\s*\}\}/', $template, $matches);
		
		foreach($matches[0] as $match){
			
			$var = str_replace('{', '', str_replace('}', '', preg_replace('/\s+/', '', $match)));
			
			if(isset($this->variables[$var])){
				$template = str_replace($match, $this->variables[$var], $template);
			}
		}
		return $template;
	}
	
	/**
	 * Builds Email Headers
	 * @return string email headers
	 */
	private function buildHeaders(){
		$headers = '';
		$headers .= implode("\r\n", $this->headers) ."\r\n";
		
		foreach($this->bcc as $bcc){
			$headers .= sprintf("Bcc: %s \r\n", $bcc);
		}
		
		foreach($this->cc as $cc){
			$headers .= sprintf("Cc: %s \r\n", $cc);
		}
		
		if(!empty($this->from)){
			$headers .= sprintf("From: %s \r\n", $this->from);
		}
		return $headers;
	}
	/**
	 * Set the wp_mail_content_type filter, if necessary 
	 */
	private function beforeSend(){
		if(count($this->to) === 0){
			throw new Exception('You must set at least 1 recipient');
		}
		if(empty($this->template)){
			throw new Exception('You must set a template');
		}
		if($this->sendAsHTML){
			add_filter('wp_mail_content_type', [ $this , 'HTMLFilter']);
		}
	}		
	/**
	 * Sends a rendered email using
	 * WordPress's wp_mail() function
	 * @return bool
	 */
	public function send(){
		$this->beforeSend();
		return wp_mail($this->to, $this->subject, $this->render(), $this->buildHeaders(), $this->attachments);
	}

}