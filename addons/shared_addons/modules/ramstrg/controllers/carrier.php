<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	ramstrg 
 * @subpackage 	Ramstrg Module
 */
class carrier extends Public_Controller
{
   private $site_id;
   private $carrier_id;

   public function __construct()
   {
	  parent::__construct();
	  Events::trigger('is_logged_in');

	  $this->carrier = $this->load->model('ramstrg_carrier_m');
	  $this->user_carrier = $this->load->model('ramstrg_users_carrier_m');

	  $this->load->library('form_validation');
	  $this->lang->load('ramstrg');

	  $this->site_id =  $this->uri->segment(5);

	  $this->template
		 ->append_css('module::ramstrg.css')
		 ->append_css('module::calendar.css')
		 ->append_css('module::jquery.datetimepicker.css')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::ramstrg.js');

	  $this->set_carrier_id();
   }

   // --------------------------------------------------------------------
   /**
	* start here
	*/

   public function index($offset = 0)
   {

	  $this->session->set_userdata('book_url',current_url());


	  $data = $this->input->post('data');
	  $validation_rules = array(
								array(
									  'field'   => 'data[name]',
									  'label'   => 'Name',
									  'rules'   => 'required'
									  )
								);

	  $this->form_validation->set_rules($validation_rules);


	  $errors = '';
	  if($this->input->post('submit'))
		 {
			if ($this->form_validation->run() === FALSE)
			   {
				  $errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() .'
				</div></div>';
			   }
			else
			   {
				  $data = $this->input->post('data');
				  if($this->carrier_id < 1)
					 {
						$carrier_id = $this->carrier->insert($data);

						$u_carrier['user_id'] = $this->current_user->id;
						$u_carrier['carrier_id'] = $carrier_id;
						$this->user_carrier->insert($u_carrier);
						$this->session->set_userdata('carrier_id', $carrier_id);
					 }
				  else
					 {
						$this->carrier->update($this->carrier_id, $data);
					 }

				  redirect(current_url());

			   }
		 }

// fehler label wenn user angemelded/registriert aber noch keine angaben zum spediteur gemacht sind
	  $err_details = '';
	  if($this->session->userdata('carrier_id') == '')
		 {
			$err_details = $this->lang->line('ramstrg:enter_carrier_details');
		 }

	  $data = $this->carrier->get_by('id', $this->carrier_id);

	  $this->template
		 ->title('')
		 ->append_js('module::calendar.js')
		 ->set('fields',$this->carrier_form_fields($data))
		 ->set('err_enter_details', $err_details)
		 ->set('form_errors', $errors)
		 ->build('carrier_details');
 
   }
   function object_to_array($object) {
	  return (array) $object;
   }
   // --------------------------------------------------------------------
   /**
	* carrier id setzen
	* 
	* @access 		private
	* @param 		
	* @return 		
	* 
	*/
   private function set_carrier_id()
   {
	  $carrUsers = $this->user_carrier->get_by('user_id',$this->current_user->id);
	  if($carrUsers != '')
		 {
			$this->carrier_id =$carrUsers->carrier_id;
		 }
   }

   // --------------------------------------------------------------------
   /**
	* carrier detail form
	* 
	* @access 	private	
	* @param 	array 	
	* @return 	array	
	* 
	*/
   private function carrier_form_fields($data = '')
   {
	  if(is_object($data))
		 {
			$data = (array) $data;
		 }	

	  $namePreFx = '';
	  $idPreFx = 'carrier_';

	  if($this->input->post('data'))
		 {
			$data = $this->input->post('data');
		 }
	  else
		 {
			$data = $data;
		 }
	  if(!isset($data['date']))
		 {
			//$data['date'] = date('d.m.Y',$this->uri->segment(4));
		 }

	  $formfields['open'] = form_open();
	  $formfields['close'] = form_close();
	  $formfields['submit'] = form_submit('submit', 'Senden');


	  $fields = $this->db->field_data('ramstrg_carrier');

	  foreach ($fields as $field)
		 {
			$field->name;
			$field->type;
			$field->max_length;
			$field->primary_key;


			// standard input	  
			$conf = array(
						  'name'        => 'data[' . $field->name . ']',
						  'id'          => $idPreFx  . $field->name,
						  'maxlength'   => $field->max_length,
						  'value'       => set_value('data[date]', isset($data[$field->name]) ? $data[$field->name] : ''),
						  );

			$formfields[$namePreFx . $field->name] = form_input($conf);

		 } 

	  return $formfields;
   } 

}
