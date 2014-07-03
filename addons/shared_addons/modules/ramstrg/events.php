<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Ramstrg Events Class
 * 
 * @package        PyroCMS
 * @subpackage    Ramstrg Module
 * @category    events
 * @author        Jerel Unruh - PyroCMS Dev Team
 * @website        http://unruhdesigns.com
 */
class Events_Ramstrg {
    
   protected $ci;
    
   public function __construct()
   {
	  $this->ci =& get_instance();

	  $this->ci->load->model('files/file_folders_m');
	  $this->ci->load->add_package_path("addons/shared_addons/modules/ramstrg");
	  $this->users_carrier = $this->ci->load->model('ramstrg_users_carrier_m');

	  //register the public_controller event

	  Events::register('post_user_login', array($this, 'check_from_bookform'));


	  Events::register('post_user_login', array($this, 'set_carrier'));
	  Events::register('set_carrier', array($this, 'set_carrier'));

        
	  Events::register('is_logged_in', array($this, 'usercheck'));

   }

   // --------------------------------------------------------------------
   /**
	* falls user buchen wollte und nicht eingeloggt war, 
	* dann nach login wieder mit datum und uhrzeit auf buchungsform zurueck
	* 
	*/
   public function check_from_bookform()
   {
	  if($this->ci->session->userdata('book_url'))
		 {


			$book_url = $this->ci->session->userdata('book_url');
			$this->ci->session->unset_userdata('book_url');
			redirect($book_url);

		 }

   }

   // --------------------------------------------------------------------
   /**
	* 
	*/
   public function set_carrier()
   {
	  if(!is_object($this->ci->ion_auth->get_user()))
		 {
			return FALSE;
		 }

	   $reddirTarget = 'ramstrg/carrier';	  

	  $uData = $this->ci->ion_auth->get_user();
	   $res = $this->users_carrier->get_by('user_id',$uData->id );

	  if(isset($res->carrier_id) && $res->carrier_id > 1)
		 {

			$this->ci->session->set_userdata('carrier_id', $res->carrier_id);

		 }	
	  elseif(!isset($res->carrier_id) && (uri_string() != $reddirTarget) )
		 {
			redirect($reddirTarget);
		 }

   }

   // --------------------------------------------------------------------
   /**
	* check ob user eingeloggt ist ansonsten auf login form 
	* 
	*/
   public function usercheck()
   {

	  if (!isset($this->ci->current_user->id))
		 {
			redirect('users/login');
		 }
	  else
		 {
					$this->set_carrier();
		 }

   }
    
}
/* End of file events.php */
