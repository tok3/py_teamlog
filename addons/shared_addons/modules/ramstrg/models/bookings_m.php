<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a ramstrg module for PyroCMS
 *
 * @author 		Tobias C. Koch
 * @package 	PyroCMS
 * @subpackage 	Ramstrg Module
 */
class bookings_m extends MY_Model {

   public function __construct()
   {		
	  parent::__construct();
		
	  /**
	   * default table für rampensteuerung standorte 
	   * 
	   */
	  $this->_table = 'ramstrg_bookings';
   }
	
   //create a new item
   public function create($to_insert)
   {
	  return $this->db->insert($this->_table, $to_insert);
   }

   // --------------------------------------------------------------------
   /**
	* get bookings overview
	* 
	* @access 	public	
	* @param 	interger	carrier id
	* @return 	arra	result objects
	* 
	*/
   public function get_bookings($_carrier_id = "")
   {
	  // aufgrund eines bugs mit CONCAT, active record und table prefix muss hier "haendisch" selectiert werden 

	  $sql = "SELECT " . $this->db->dbprefix . "ramstrg_bookings.*,DATE_FORMAT(booking_placed, '%d.%m.%Y') AS booking_placed, date_format(time_start, '%d.%m.%Y %H:%i') time_start, " . $this->db->dbprefix . "ramstrg_sites.name, CONCAT( " . $this->db->dbprefix . "ramstrg_sites.str, ' ', " . $this->db->dbprefix . "ramstrg_sites.nr) AS strasse, " . $this->db->dbprefix . "ramstrg_sites.plz, " . $this->db->dbprefix . "ramstrg_sites.ort FROM " . $this->db->dbprefix . "ramstrg_bookings JOIN " . $this->db->dbprefix . "ramstrg_sites ON " . $this->db->dbprefix . "ramstrg_bookings.site_id = " . $this->db->dbprefix . "ramstrg_sites.id";

	  $order_by = ' order by time_start ASC';
	  if($_carrier_id != '')
		 {
			$sql .= " WHERE " . $this->db->dbprefix . "ramstrg_bookings.carrier_id = ?" . $order_by; 
			$query =  $this->db->query($sql, array($_carrier_id));

		 }
	  else
		 {
			$query =  $this->db->query($sql);
		 }



	  $result = $query->result();

	  return $result;
   }
}
