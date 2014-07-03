<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		tobias@mmsetc.de
 * @website		mmsetc.de
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class Ramstrg extends Public_Controller
{
   public function __construct()
   {
	  parent::__construct();


	  // Load the required classes
	  $this->load->model('ramstrg_sites_m');
	  $this->lang->load('ramstrg');


	  $this->template
		 ->append_css('module::ramstrg.css')
		 ->append_css('module::calendar.css')
		 ->append_js('module::jquery.min.js')
		 ->append_js('module::jquery.datetimepicker.js')

		 ->append_js('module::ramstrg.js');

   }

   /**
	* All items
	*/
   public function index($offset = 0)
   {


	  // set the pagination limit
	  $limit = 10;
		
	  $items = $this->ramstrg_sites_m->limit($limit)
		 ->offset($offset)
		 ->get_all();
			
	  $maps = '';

	  foreach($items as $key => $item)
		 {

			$config['center'] = $this->format->convert_spcialChars(utf8_decode($item->str.' '. $item->nr. ', ' . $item->plz . ' ' . $item->ort . ',germany'));

			$config['zoom'] = 12;
			$config['map_width'] = '100%';
			$config['map_height'] = '180px';

			$config['map_name'] = 'map_' . $item->plz;
			$config['map_div_id'] = 'map_' . $item->plz;
			$this->googlemaps->initialize($config);
			$marker['position'] = $config['center'];
			$this->googlemaps->add_marker($marker);
			$data['map'] = $this->googlemaps->create_map();


			$this->template->append_metadata($data['map']['js']);
			 $items[$key]->map = $data['map']['html'];
		 }
	  
	  // we'll do a quick check here so we can tell tags whether there is data or not
	  $items_exist = count($items) > 0;

	  // we're using the pagination helper to do the pagination for us. Params are: (module/method, total count, limit, uri segment)
	  $pagination = create_pagination('ramstrg', $this->ramstrg_sites_m->count_all(), $limit, 1);

	  $this->template
		 ->title($this->module_details['name'], 'the rest of the page title')
		 ->set('items', $items)
		 ->set('items_exist', $items_exist)
		 ->set('pagination', $pagination)
		 ->set('calendar_link',site_url('showCal/'.date('Y/m/d',time())))
		 ->build('index')
		 ;
   }

   public function showCal($year='', $month='', $day=''){

	  // site_id 
	  $standort = $this->uri->segment('6');

	  if ($year==null) {
		 $year = date('Y');
	  }

	  if ($month==null) {
		 $month = date('m');
	  }

	  if ($day==null) {
		 $day = date('d');        
	  }    

   
	  $this->load->library('calendar_week');


	  $content['calendar'] = '';      
				
	  //print_r($arr_Data);
	  $calendarPreference = array (
								   'start_day'    => 'monday',
								   'month_type'   => 'de',
								   'day_type'     => 'de',
								   'date'     => date(mktime(0, 0, 0, $month, $day, $year)),
								   'url' => 'ramstrg/showCal/',

								   'additionalSegments'=> '/'. $standort
								   );        

	  $cal = new $this->calendar_week($calendarPreference); 
	  // add Event start, end , text;


	  $b_hours_m = $this->load->model('ramstrg_sites_business_hours_m');
	  $b_holidays_m = $this->load->model('ramstrg_sites_holidays_m');
	  $bookings = $this->load->model('bookings_m');
	  $this->load->model('general_m');

	  //$this->news_model->get_many_by(array('active' => 1, 'another_column' => 'value'));


	  // betriebserferien von standort holen
	  foreach($b_holidays_m->get($standort) as $key  => $holidays)
		 {
			
			$event->format = 'Y-m-d H:i:s';
			$event->start = $holidays->date_start .' '.$holidays->time_start; // ergibt Y-m-d H:i:s
			$event->end = $holidays->date_end .' '.$holidays->time_end;
			$event->text = '';
			$event->add_class = 'vacc';

			$cal->set_event($event);
		 }



	  // geschaeftszeiten von standort holen 
	  foreach($b_hours_m->getBH($standort) as $key  => $businesshours)
		 {

			$event->format = 'w H:i';
			$event->start = $businesshours->time_start;
			$event->end = $businesshours->time_end;
			$event->day_start = $businesshours->day_start;
			$event->day_end = $businesshours->day_end;
			$event->text = '';
			$event->add_class = 'businessHours';
			$event->attributes = 'data-businessHour="1" data-standort=" ' . $standort. '"';
			$event->ckeckAvail = $standort;
			$cal->set_event($event);

		 }

	  $data = $bookings->get_many_by('site_id',$standort);
	  foreach($data as $key => $booking)
		 {


			$start = $this->format->make_time($booking->time_start);
			$end = $this->format->make_time($booking->time_end);

			/*
			$this->ramps->is_disposable($standort,$start ,$end);

			$slotInfo = $this->ramps->get_slot_info();

			foreach($slotInfo as $key => $info)
			   {
				  if($info['booked'] == 1)
					 {
			 
						$event->format = 'Y-m-d H:i:s';
						$event->start = $info['f_start'];
						$event->end = $info['f_end'];
						$event->text = '<i class="fi-lock"></i>';
						$event->add_class = 'booked';


						$cal->set_event($event);
					 }
			   }
			*/
		 }
	  
	  $calendar = $cal->get_calendar();

	  $this->template
		 ->title($this->module_details['name'], 'the rest of the page title')
		 ->set('cal',$calendar)
		 ->append_js('module::calendar.js')
		 ->build('calendar');
   }

   // --------------------------------------------------------------------
   /**
	* geschäftszeiten als json array ausgeben
	* 
	* @access 	public	
	* @param 	integer	standort it
	* @return 	json	
	* 
	*/
   public function get_business_hours($_id)
   {
	  $b_hours_m = $this->load->model('ramstrg_sites_business_hours_m');
	  $b_holidays_m = $this->load->model('ramstrg_sites_holidays_m');

	  $businesshours = $b_hours_m->getBH(12);
	  
	  $businessholidays = $b_holidays_m->get(12);

	  
	  echo json_encode($businesshours);

   }


   function test()
   {


	  Events::trigger('set_carrier');


	  $this->ramps->is_disposable(12,1400144400, 1400144400 + 3600);

			$slotInfo = $this->ramps->get_slot_info();
			echo "<pre><code>";
			print_r($slotInfo);
			echo "</code></pre>";
			
			

	  $bookings = $this->load->model('bookings_m');
	  $standort = $this->uri->segment('6');

	  $data = $bookings->get_many_by('site_id',$standort);

	  foreach($data as $key => $booking)
		 {


			$event->format = 'Y-m-d H:i:s';
			$event->start = $booking->time_start;
			$event->end = $booking->time_end;
			$event->text = '<i class="fi-lock"></i>';
			$event->add_class = 'booked';


			$cal->set_event($event);


			die;
		 }
	  

   }
}
