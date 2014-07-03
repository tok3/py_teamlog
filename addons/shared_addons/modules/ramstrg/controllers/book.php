<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	ramstrg 
 * @subpackage 	Ramstrg Module
 */
class Book extends Public_Controller
{
   private $site_id;

   public function __construct()
   {
	  parent::__construct();

	  $this->site_id =  $this->uri->segment(5);
	  /*
	   <link href="css/flick/jquery-ui-1.10.4.custom.css" rel="stylesheet">
	   <script src="js/jquery-1.10.2.js"></script>
	   <script src="js/jquery-ui-1.10.4.custom.js"></script>
	  */
	  // Load the required classes
	  $this->load->model('ramstrg_sites_m');
	  $this->business_hours =  $this->load->model('ramstrg_sites_business_hours_m');
	  $this->holidays =  $this->load->model('ramstrg_sites_holidays_m');
	  $this->load->model('bookings_m');
	  $this->load->model('general_m');

	  $this->load->library('form_validation');
	  $this->lang->load('ramstrg');

	  $this->validation_rules = array(
									  array(
											'field'   => 'data[art]',
											'label'   => 'Art der Ladung',
											'rules'   => 'required|callback_bussinesshour_check'
											)
									  ,									
									  array(
											'field'   => 'data[date]',
											'label'   => 'Datum',
											'rules'   => 'required|callback_is_holiday|callback_slot_available'
											),
									  array(
											'field'   => 'data[mnt_palette]',
											'label'   => 'Anzahl Paletten',
											'rules'   => 'required|is_natural_no_zero'
											),
									  array(
											'field'   => 'data[kg]',
											'label'   => 'Gewicht',
											'rules'   => 'required|is_natural_no_zero'
											)
									  ,
			  array(
											'field'   => 'data[supplier]',
											'label'   => 'Lieferant',
											'rules'   => 'required'
											)
									  ,
			array(
											'field'   => 'data[lic_number]',
											'label'   => 'Kennzeichen',
											'rules'   => 'required'
											)
									  ,
						
									  );




	  $this->template
		 ->append_css('module::ramstrg.css')
		 ->append_css('module::calendar.css')
		 ->append_css('module::jquery.datetimepicker.css')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::ramstrg.js');


   }


   // --------------------------------------------------------------------
   /**
	* 
	*/
   public function index($offset = 0)
   {
	  if($this->session->userdata('carrier_id') == '')
		 {
	  Events::trigger('set_carrier');
		 }

	  // check if user is logged in if not store url to redirect to bookform after login 
	  if (!$this->ion_auth->logged_in()) 
		 {
 			$this->session->set_userdata('book_url',current_url());
 			redirect('users/login');
		 }

	  $data = $this->input->post('data');

	  $date = date_parse_from_format('d.m.Y H:i', $data['date'] . ' ' . $data['time']); 
	  $start_tStamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

	  $this->holidays->is_holiday($start_tStamp,$this->site_id);	  

	  $this->form_validation->set_rules($this->validation_rules);

	  $errors = '';
	  if($this->input->post('submit'))
		 {
			if ($this->form_validation->run() === FALSE)
			   {
				  $errors = '<div class="medium-12 small-12 columns"><div class="alert-box secondary radius" data-alert="">
				' . validation_errors() . anchor('ramstrg/showCal/'.date('Y/m/d',$start_tStamp) . '/' . $this->site_id, '[' . $this->lang->line('ramstrg:show_plan').']').'
				</div></div>';
			   }
			else
			   {
				  $data = $this->input->post('data');

				  $date = date_parse_from_format('d.m.Y H:i', $data['date'] . ' ' . $data['time']); 
				  $start_tStamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
				  $insDat['time_start']  = date('Y-m-d H:i:s',$start_tStamp);

				  // teilladung plus 30 min 
				  $end['LTL'] = mktime($date['hour'], $date['minute'] + 30, $date['second'], $date['month'], $date['day'], $date['year']);

				  // komplettladung plus 1 h
				  $end['TL'] = mktime($date['hour'] + 1, $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

				  $insDat['load_type'] = $data['art'];
				  $insDat['time_end'] = date('Y-m-d H:i:s', $end[$insDat['load_type']]);
				  $insDat['site_id'] = $this->site_id;

				  $insDat['carrier_id'] = $this->session->userdata('carrier_id');

				  if($this->session->userdata('carrier_id') > 0)

					 {
						//				  $insDat['carrier_id'] = $this->session->userdata('carrier_id');
					 }

				  $insDat['mnt_palettes'] = $data['mnt_palette'];
				  $insDat['weight_kg'] = $data['kg'];

				  $insDat['lic_number'] = $data['lic_number'];
				  $insDat['supplier'] = $data['supplier'];

				  $this->bookings_m->create($insDat);

				  redirect('ramstrg/showCal/'.date('Y/m/d',$start_tStamp) . '/' . $insDat['site_id']);

			   }
		 }
	  $this->template
		 ->title('Buchen')
		 ->append_js('module::calendar.js')
		 ->set('fields',$this->getFormFields())
		 ->set('form_errors', $errors)
		 ->build('bookForm');
   }

   // --------------------------------------------------------------------
   /**
	* form inputs
	* 
	*/

  private function getFormFields()
   {

	  $sent = $this->input->post('data');

	  if(!isset($sent['date']))
		 {
			//$sent['date'] = date('d.m.Y',$this->uri->segment(4));
		 }

	  $formelements['open'] = form_open();
	  $formelements['close'] = form_close();
	  $formelements['submit'] = form_submit('submit', 'Senden');


	  // datum	  
	  $data = array(
					'name'        => 'data[date]',
					'id'          => 'del_date',
					'value'       => set_value('data[date]', isset($sent['date']) ? $sent['date'] : date('d.m.Y',$this->uri->segment(4))),
					);

	  $formelements['del_date'] = form_input($data);

	  // uhrzeit
	  $data = array(
					'name'        => 'data[time]',
					'id'          => 'del_time',
					'value'       => set_value('data[time]', isset($sent['time']) ? $sent['time'] :  date('H:i',$this->uri->segment(4))),

					);

	  $formelements['del_time'] = form_input($data);


	  //radio art teilladung 
	  $data = array(
					'name'        => 'data[art]',
					'id'          => 'del_art',
					'value'       => 'LTL', // Less Than Truck Load

					);

	  $formelements['del_art_teil'] = form_radio($data, '', isset($sent['art']) && $sent['art'] == 'LTL' ? true : false);

	  //radio art komplettladung
	  $data = array(
					'name'        => 'data[art]',
					'id'          => 'del_art',
					'value'       => 'TL',// Truck Load

					);

	  $formelements['del_art_kompl'] = form_radio($data, '',  isset($sent['art']) && $sent['art'] == 'TL' ? true : false);


	  // anzal palletten
	  $data = array(
					'name'        => 'data[mnt_palette]',
					'id'          => 'del_mnt_pal',
					'value'       => $sent['mnt_palette'],

					);

	  $formelements['del_mnt_pal'] = form_input($data);

	  // gewicht 
	  $data = array(
					'name'        => 'data[kg]',
					'id'          => 'del_kg',
					'value'       => $sent['kg'],

					);

	  $formelements['del_kg'] = form_input($data);

  // lieferant 
	  $data = array(
					'name'        => 'data[supplier]',
					'id'          => 'del_suppl',
					'value'       => $sent['supplier'],

					);

	  $formelements['del_supplier'] = form_input($data);

// kennzeichen 
	  $data = array(
					'name'        => 'data[lic_number]',
					'id'          => 'del_lic',
					'value'       => $sent['lic_number'],

					);

	  $formelements['del_lic_number'] = form_input($data);



	  return $formelements;
   }

   // --------------------------------------------------------------------
   /**
	* validation geschaeftszeit ende fuer entladung ckeck ob ende noch in geschaeftszeit faellt 
	* 
	* @access 		
	* @param 		
	* @return 		
	* 
	*/
   public function bussinesshour_check($str)
   {

	  $data = $this->input->post('data');


	  $date = date_parse_from_format('d.m.Y H:i', $data['date'] . ' ' . $data['time']); 

	  $start_tStamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']) + 1;

	  // teilladung plus 30 min 
	  $end['LTL'] = mktime($date['hour'], $date['minute'] + 30, $date['second'], $date['month'], $date['day'], $date['year']);

	  // komplettladung plus 1 h
	  $end['TL'] = mktime($date['hour'] + 1, $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

	  $insDat['load_type'] = $data['art'];

	  $compEnd = $end[$insDat['load_type']] - 1;

	  if ($this->business_hours->in_business($start_tStamp, $this->site_id) === FALSE)
		 {
			$this->form_validation->set_message('bussinesshour_check', $this->lang->line('ramstrg:err_start_out_of_bh'));
			return FALSE;
		 }
	  elseif ($this->business_hours->in_business($compEnd, $this->site_id) === FALSE)

		 {
			$this->form_validation->set_message('bussinesshour_check', $this->lang->line('ramstrg:err_end_out_of_bh'));
				
			return FALSE;
		 }
	  else
		 {
			return TRUE;
		 }
   }


   // --------------------------------------------------------------------
   /**
	* validation ferien 
	* check ob startzeit in in geschaeftszeit liegt
	* 
	* @access 		
	* @param 		
	* @return 		
	* 
	*/
   public function is_holiday($str)
   {

	  $data = $this->input->post('data');


	  $date = date_parse_from_format('d.m.Y H:i', $data['date'] . ' ' . $data['time']); 
	  $start_tStamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);


	  if ($this->holidays->is_holiday($start_tStamp, $this->site_id) === TRUE)
		 {
			$this->form_validation->set_message('is_holiday', $this->lang->line('ramstrg:err_is_holiday'));
			return FALSE;
		 }
	  else
		 {
			return TRUE;
		 }
   }

   // --------------------------------------------------------------------
   /**
	* validation zeit slot noch verfügbar  
	* check ob startzeit noch verfügbar, und bei truck load anschliessender slot
	* 
	* @access 		
	* @param 		
	* @return 		
	* 
	*/
   public function slot_available($str)
   {

	  $data = $this->input->post('data');

	  $date = date_parse_from_format('d.m.Y H:i', $data['date'] . ' ' . $data['time']); 

	  $start_tStamp = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
	  if(!isset($data['art']) ||$data['art'] == 'LTL')
		 {
			$end_tStamp = mktime($date['hour'], $date['minute'] + 30, $date['second'], $date['month'], $date['day'], $date['year']);

		 } 
	  else
		 {
			$end_tStamp = mktime($date['hour'] + 1, $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		 }
	  
	  $dispos = new $this->ramps();
	  $available = $dispos->is_disposable($this->site_id, $start_tStamp, $end_tStamp);
	  $slotInfo = $dispos->get_slot_info();

 
	  if ($available === TRUE)
		 {
			return TRUE;

		 }
	  else
		 {
			if(count($slotInfo) == 2 && $slotInfo[0]['booked'] == 0)
			   {
				  $this->form_validation->set_message('slot_available', $this->lang->line('ramstrg:err_ltl_only'));
				  return FALSE;

			   }  

			$this->form_validation->set_message('slot_available', $this->lang->line('ramstrg:err_no_capacity'));
			return FALSE;

		 }
   }




   // --------------------------------------------------------------------
   /**
	* testareal
	* 
	*/
   function libchecktest()
   {

	  $dateStart['day'] = '07';
	  $dateStart['month'] = '04';
	  $dateStart['year'] = '2014';
	  $dateStart['hour'] = '10';
	  $dateStart['minute'] = '30';
	  $dateStart['second'] = '00'; 

	  $start_tStamp = mktime($dateStart['hour'], $dateStart['minute'], $dateStart['second'], $dateStart['month'], $dateStart['day'], $dateStart['year']);

	  $end_tStamp = mktime($dateStart['hour'] +2, $dateStart['minute'], $dateStart['second'], $dateStart['month'], $dateStart['day'], $dateStart['year']);


	  $dispos = $this->ramps->is_disposable(12,$start_tStamp ,$end_tStamp);

	  echo "<pre><code>";
	  print_r($this->ramps->get_slot_info());
	  echo "</code></pre>";
	  
	  if($dispos == TRUE)
		 {
			echo "buchbar";
		 }

   }


// --------------------------------------------------------------------
/**
* buchungen anzeigen
* 
* @access 	public	
* @param 	interger	carrier id 		
* @return 		
* 
*/
public function ings()
{
	  // check if user is logged in if not store url to redirect to bookform after login 
	  if (!$this->ion_auth->logged_in()) 
		 {
 			$this->session->set_userdata('book_url',current_url());
 			redirect('users/login');
		 }

   $_id = $this->session->userdata('carrier_id');

	  $data  = $this->format->object_to_array($this->bookings_m->get_bookings($_id));
// 	  echo "<pre><code>";
// 	  print_r($data);
// 	  echo "</code></pre>";
	  
	  $loadTypesLang =  $this->lang->line('ramstrg:load_type');

	  foreach($data as $key => $item)
		 {
			$data[$key]['load_type'] = $loadTypesLang[$item['load_type']];
			}

	  // grid konfigurieren und zurückgeben 
	  $conf['id'] = 'bookingsGrid';

	  $cols = array('booking_placed','name','time_start','supplier','lic_number', 'load_type','mnt_palettes','weight_kg');

	  $bookingsGrid = new sortable_grid($conf);
	  // $bookingsGrid->copy_col('id','del_id');
	  $heading = $this->lang->line('ramstrg:bookings_col_heading');
	  
	  	  $bookingsGrid->set_heading($heading);
	  	  $bookingsGrid->set_class('responsive');


	  $gridData = $bookingsGrid->arrangeCols($data,$cols);

	  $grid = $bookingsGrid->getGrid($gridData);

	  $this->template
		 ->title('')
		 ->set('grid',$grid)
		 ->append_css('module::jquery.datetimepicker.css')
		 ->append_css('module::tablesorter/style.css')
		 ->append_css('module::responsive-tables.css')
		 ->append_js('module::jquery.datetimepicker.js')
		 ->append_js('module::tablesorter/js/jquery.tablesorter.min.js')
		 ->append_js('module::responsive-tables.js')
		 ->build('bookings_list');

   
}
   /**
	* ende test
	* 
	*/
   // --------------------------------------------------------------------
   



}
