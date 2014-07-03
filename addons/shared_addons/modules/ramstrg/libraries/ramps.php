<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ramps {


   var $CI;
   var $lang;

   private $slot_size;
   private $mnt_ramps;
   private $slotInfo = array();
   private $slotInfoFormat = 'Y-m-d H:i:s';

   function __construct()
   {
	  $this->CI = &get_instance();

	  $this->set_slot_size();
   }
	


   function test()
   {

	  echo $this->slot_size;
   }


   function is_disposable($_site_id, $_start, $_ende = '')
   {

	  /// aktive rampen für standord ermitteln und in class var setzen 
	  $this->activeRamps($_site_id);

	  // wenn ende leer dann ende start + slot size
	  if($_ende == '')
		 {
			$_ende = $_start + $this->slot_size;
		 }


	  // zeitspanne in sec	 
	  $tspanSec = $_ende - $_start;

	  // zeitspanne durch slot grösse = needed slots
	  $neededSlots = $tspanSec / $this->slot_size;

	  $slots = array();
	  $slotStart = $_start;

	  $disposable = TRUE; 
	  for($i = 0; $i <  $neededSlots; $i++)
		 {
			// timespamt slot start 
			$slots[$i] = $slotStart; 
			$this->slotInfo[$i]['start'] = $slotStart;
			$this->slotInfo[$i]['end'] = $slotStart + $this->slot_size;
			$this->slotInfo[$i]['f_start'] = date($this->slotInfoFormat,$slotStart);
			$this->slotInfo[$i]['f_end'] = date($this->slotInfoFormat,$slotStart + $this->slot_size);

			//wenn in zeitspanne 1 slot nicht buchbar dann ist zeitspanne nicht buchbar 

			if($this->slot_booked($_site_id, $slotStart) >= $this->mnt_ramps)
			   {
				  $disposable =  FALSE;
				  $this->slotInfo[$i]['booked'] = 1;

			   }
			else
			   {
				  $this->slotInfo[$i]['booked'] = 0;

			   }

			$slotStart += $this->slot_size;
		 }
	  
	  return $disposable;

   }



   // --------------------------------------------------------------------
   /**
	* anzahl belegter slots pro standort 
	* jeder slot kann für jede rampe vergeben werden also 2 rampen = 2x die selbe zeit verfuegbar 
	* 
	* @access 	private	
	* @param 	integer site_id / standort id	
	* @parem	timestamp 	eindeutige id eines 30 min slots  		
	* @return 	integer	buchungen auf slot, slot darf pro rampe 1 x gebucht werden	
	* 
	*/
   private function slot_booked($_site_id,$slot)
   {

	  $this->CI->db->select('*');

	  $this->CI->db->where('site_id', $_site_id);
	  $this->CI->db->where('UNIX_TIMESTAMP(time_start) <=', $slot);
	  $this->CI->db->where('UNIX_TIMESTAMP(time_end) >', $slot);

	  $query = $this->CI->db->get('ramstrg_bookings');
	  $result = $query->result();

	  $booked = count($result);

	  $slotNonAvalable = $this->check_nonavail($slot,$_site_id);	  

	  $booked += $slotNonAvalable;

	  return $booked;
   }

   // --------------------------------------------------------------------
   /**
	* check ob eine rampe des standortes zur zeit als belegt markiert ist ()
	* 
	* @access 	public	
	* @param 	unix timestamp	
	* @param 	integer 	standort id
	* @return 	boolean	
	* 
	*/
   private function check_nonavail($_tstamp, $_site_id)
   {
	  $time = date('YmdHis',$_tstamp);

	  $this->CI->db->select("*, CONCAT(REPLACE(date_start,'-',''), REPLACE(time_start, ':', ''),'') AS start_comp,  CONCAT(REPLACE(date_end,'-',''), REPLACE(time_end, ':', ''),'') AS end_comp");
	  $this->CI->db->from('ramstrg_ramp_nonavail');
  	  $this->CI->db->having("CONCAT(REPLACE(date_start,'-',''), REPLACE(time_start, ':', ''),'') <= " .$time);
  	  $this->CI->db->having("CONCAT(REPLACE(date_end,'-',''), REPLACE(time_end, ':', ''),'') >= " .($time +1));

	  $this->CI->db->where('site_id',$_site_id);
	  $this->CI->db->group_by('ramp_id');


	  $query = $this->CI->db->get();

	  $result = $query->result();
	  

	  $slotNonAvalable_p = $this->check_nonavail_period($_tstamp,$_site_id);	  

	  $check_byDateAndPeriod = array_merge($slotNonAvalable_p,$result);	  


	  $slots = array();
	  foreach($check_byDateAndPeriod as $key => $item)
		 {
			$slots[$item->ramp_id] = 'booked'; // rampen über id zusammenführen da slot nur einmal ausgeschlossen werden kann über exaktes datum oder periode. wenn beids zutrifft ist trotzdem nur 1 slot als belegt markiert !!! 
			}

	  return count($slots);
   }

   // --------------------------------------------------------------------
   /**
	* check ob eine rampe des standortes zur zeit als belegt markiert ist (periodisch z.b. dienstag - freitags von 10:00 - 12:00)
	* 
	* @access 	public	
	* @param 	unix timestamp	
	* @param 	integer 	standort id
	* @return 	boolean	
	* 
	*/
   private function check_nonavail_period($_tstamp, $_site_id) 
   {

	  $day =  date('w', $_tstamp);
	  $time =  date('His', $_tstamp);

	  $this->CI->db->select("*");
	  $this->CI->db->from('ramstrg_ramp_nonavail_period');
	  $this->CI->db->where('day_start <= ' .$day);
	  $this->CI->db->where('day_end >= ' .$day);

	  $this->CI->db->having("REPLACE(time_start, ':', '') <= " .$time);
	  $this->CI->db->having("REPLACE(time_end, ':', '') >= " .$time);

	  $this->CI->db->where('site_id',$_site_id);

	  $query = $this->CI->db->get();

	  $result = $query->result();

			return $result;
   }


   // --------------------------------------------------------------------
   /**
	* anzahl verfügbarer rampen ermitteln
	* 
	* @access 	private	
	* @param 	in	
	* @return 	re	
	* 
	*/
   private function activeRamps($_site_id)
   {
	  $this->CI->db->select('count(*) AS ramp_mnt');


	  $this->CI->db->where('site_id', $_site_id);
	  $this->CI->db->where('active',1);
	  $query = $this->CI->db->get('ramstrg_sites_ramps');
	  $result = $query->result();

	  if(count($result) == 1)
		 {
			$this->mnt_ramps = $result[0]->ramp_mnt;
			return TRUE;
		 }
	  else
		 {
			$this->mnt_ramps = 0;
			return FALSE;
		 }

   }




   // --------------------------------------------------------------------
   /**
	* set slot size in seconds according to multiplier
	* 
	* @access 	public	
	* @param 	float	floating point 0.5 half hour 0.25 quarter
	* @return 	void	
	* 
	*/
   public function set_slot_size($_multiplier = 0.5)
   {

	  $hour = 3600;
	  $slot = $hour * $_multiplier; // slot halbe stunde
	  $this->slot_size = $slot;

   }

   // --------------------------------------------------------------------
   /**
	* public function get_slot_info
	* 
	* @access 	public	
	* @param 	void	
	* @return 	array	
	* 
	*/
   public function get_slot_info()
   {
	  return $this->slotInfo;
   }

}

// END Calendar_week class

/* End of file Calendar_week.php */
/* Location: ./system/application/libraries/Calendar_week.php */