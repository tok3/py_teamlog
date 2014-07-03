<?php defined('BASEPATH') or exit('No direct script access allowed');
class Module_Ramstrg extends Module {
   public $version = '0.0.9';
   public $namespace = "ramstrg";
   public function info()
   {
	  return array(
				   'name' => array(
								   'en' => 'Rampensteuerung'
								   ),
				   'description' => array(
										  'en' => 'Modul TeamLog Rampensteuerung'
										  ),
				   'frontend' => TRUE,
				   'backend' => TRUE,
			
				   'sections' => array(
									   'sites' => array(
														'name' 	=> 'ramstrg:sites', // These are translated from your language file
														'uri' 	=> 'admin/ramstrg',
														'shortcuts' => array(
																			 'create' => array(
																							   'name' 	=> 'ramstrg:create',
																							   'uri' 	=> 'admin/ramstrg/create',
																							   'class' => 'add'
																							   )
																			 )
														),
									   'ramps' => array(
														'name' 	=> 'ramstrg:ramps', // These are translated from your language file
														'uri' 	=> 'admin/ramstrg/ramp',
														'shortcuts' => array(
																			 'create' => array(
																							   'name' 	=> 'ramstrg:ramp_create',
																							   'uri' 	=> 'admin/ramstrg/ramp/create',
																							   'class' => 'add'
																							   )
																			 )
														)
									   )
				   );
   }
   public function admin_menu(&$menu)
   {
	  $menu['Rampensteuerung'] = array(
									   'Standorte' => 'admin/ramstrg/',
									   'Rampen' => 'admin/ramstrg/ramp/'
									   );
	  // beispiel sortierung
	  add_admin_menu_place('Rampensteuerung', 2);
	  return $menu;
   }
   public function install()
   {
	  $this->dbforge->drop_table('ramstrg_sites');
	  $this->dbforge->drop_table('ramstrg_sites_business_hours');
	  $this->dbforge->drop_table('ramstrg_sites_holidays');
	  $this->dbforge->drop_table('ramstrg_sites_ramps');
	  $this->dbforge->drop_table('ramstrg_bookings');
	  $this->dbforge->drop_table('ramstrg_carrier');
	  $this->dbforge->drop_table('ramstrg_trucks');
	  $this->dbforge->drop_table('ramstrg_users_carrier');
	  $this->dbforge->drop_table('ramstrg__trucks');
	  $this->dbforge->drop_table('ramstrg_ramp_nonavail');
	  $this->dbforge->drop_table('ramstrg_ramp_nonavail_period');

	  $this->db->delete('settings', array('module' => 'ramstrg'));


	  /*tabelle mit exakten daten und zeiten wenn rampe nicht verfügbar ist*/
	  $ramstrg_ramp_nonavail = array(
									 'id' => array(
												   'type' => 'INT',
												   'constraint' => '11',
												   'auto_increment' => TRUE
												   ),
									 'site_id' => array(
														'type' => 'INT',
														'constraint' => '11'
														),
												
									 'ramp_id' => array(
														'type' => 'INT',
														'constraint' => '11'
														),
									 'date_start' => array(
														   'type' => 'DATE'
														   ),
									 'date_end' => array(
														 'type' => 'DATE'
														 ),
									 'time_start' => array(
														   'type' => 'TIME'
														   ),
									 'time_end' => array(
														 'type' => 'TIME'
														 )
									 );
	  $this->dbforge->add_field($ramstrg_ramp_nonavail);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_ramp_nonavail') ;

	  /*tabelle mit periodischen angaben wann rampe nicht verfügbar ist*/
	  $ramstrg_ramp_nonavail_period = array(
											'id' => array(
														  'type' => 'INT',
														  'constraint' => '11',
														  'auto_increment' => TRUE
														  ),
											'site_id' => array(
															   'type' => 'INT',
															   'constraint' => '11'
															   ),
												
											'ramp_id' => array(
															   'type' => 'INT',
															   'constraint' => '11'
															   ),
											'day_start' => array(
																 'type' => 'INT',
																 'constraint' => '11'
																 ),
											'day_end' => array(
															   'type' => 'INT',
															   'constraint' => '11'
															   ),
											'time_start' => array(
																  'type' => 'time'
																  ),
											'time_end' => array(
																'type' => 'time'
																)
											);
	  $this->dbforge->add_field($ramstrg_ramp_nonavail_period);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_ramp_nonavail_period') ;


	  /*tabelle LKW */

	  $ramstrg_trucks = array(
							  'id' => array(
											'type' => 'INT',
											'constraint' => '11',
											'auto_increment' => TRUE
											),
							  'carrier_id' => array(
													'type' => 'INT',
													'constraint' => '11'
													),
							  'name' => array(
											  'type' => 'VARCHAR',
											  'constraint' => '266'
											  ),
							  'lic_number' => array(
													'type' => 'VARCHAR',
													'constraint' => '15'
													),
							  );
	  $this->dbforge->add_field($ramstrg_trucks);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_trucks') ;
	 
	  /*tabelle mit zuordnung user spedition */
	  $ramstrg_users_carrier = array(
									 'id' => array(
												   'type' => 'INT',
												   'constraint' => '11',
												   'auto_increment' => TRUE
												   ),
									 'carrier_id' => array(
														   'type' => 'INT',
														   'constraint' => '11'
														   ),
									 'user_id' => array(
														'type' => 'INT',
														'constraint' => '11'
														),
									 );
	  $this->dbforge->add_field($ramstrg_users_carrier);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_users_carrier') ;

	  /*tabelle mit firmenprofil Spediteur*/
	  $ramstrg_carrier = array(
							   'id' => array(
											 'type' => 'INT',
											 'constraint' => '11',
											 'auto_increment' => TRUE
											 ),
							   'name' => array(
											   'type' => 'varchar',
											   'constraint' => '255'
											   ),
							   'name_2' => array(
												 'type' => 'varchar',
												 'constraint' => '255'
												 ),
							   'tel' => array(
											  'type' => 'varchar',
											  'constraint' => '50'
											  ),
							   'email' => array(
												'type' => 'varchar',
												'constraint' => '50'
												),
							   'str' => array(
											  'type' => 'VARCHAR',
											  'constraint' => '155'
											  ),
							   'nr' => array(
											 'type' => 'VARCHAR',
											 'constraint' => '4'
											 ),
							   'plz' => array(
											  'type' => 'VARCHAR',
											  'constraint' => '5'
											  ),
							   'ort' => array(
											  'type' => 'VARCHAR',
											  'constraint' => '155'
											  )
							   );
	  $this->dbforge->add_field($ramstrg_carrier);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_carrier') ;
	  /*tabelle mit firmenprofil Spediteur*/
	  /*
	   $ramstrg_trucks = array(
	   'id' => array(
	   'type' => 'INT',
	   'constraint' => '11',
	   'auto_increment' => TRUE
	   ),
	   'carrier_id' => array(
	   'type' => 'INT',
	   'constraint' => '11'
	   ),
	   'name' => array(
	   'type' => 'varchar',
	   'constraint' => '55'
	   ),
	   'licence' => array(
	   'type' => 'varchar',
	   'constraint' => '15'
	   )
	   );
	   $this->dbforge->add_field($ramstrg_trucks);
	   $this->dbforge->add_key('id', TRUE);
	   $this->dbforge->create_table('ramstrg_trucks') ;
	  */	
	  /*tabelle buchungen pro standort*/
	  $ramstrg_bookings = array(
								'id' => array(
											  'type' => 'INT',
											  'constraint' => '11',
											  'auto_increment' => TRUE
											  ),
								'site_id' => array(
												   'type' => 'INT',
												   'constraint' => '11'
												   ),
								'carrier_id' => array(
													  'type' => 'INT',
													  'constraint' => '11'
													  ),
		
								'booking_placed TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
		
								'time_start' => array(
													  'type' => 'timestamp',
													  'constraint' => '',
													  'extra'=>'NO'
													  ),
								'time_end' => array(
													'type' => 'timestamp',
													'constraint' => '',
													'extra'=>'NO'
													),
								'lic_number' => array(
													  'type' => 'VARCHAR',
													  'constraint' => '15'
													  ),
								'load_type' => array('type' => 'ENUM', 
													 'constraint' => array('0', 'TL', 'LTL'), 
													 'default' => '0'),
								'supplier' => array(
													'type' => 'VARCHAR',
													'constraint' => '155'
													),
								'mnt_palettes' => array(
														'type' => 'integer',
														'constraint' => '2'
														),
								'weight_kg' => array(
													 'type' => 'integer',
													 'constraint' => '3'
													 ),
						
								);
	  $this->dbforge->add_field($ramstrg_bookings);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_bookings') ;
	
	  /*tabelle rampen*/
	  $ramstrg_sites_ramps = array(
								   'id' => array(
												 'type' => 'INT',
												 'constraint' => '11',
												 'auto_increment' => TRUE
												 ),
								   'site_id' => array(
													  'type' => 'INT',
													  'constraint' => '11'
													  ),
								   'name' => array(
												   'type' => 'VARCHAR',
												   'constraint' => '60'
												   ),
								   'description' => array(
														  'type' => 'VARCHAR',
														  'constraint' => '255'
														  )
								   );
	  $this->dbforge->add_field($ramstrg_sites_ramps);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_sites_ramps') ;
	
	  /*tabelle mit betriebsferien*/
	  $ramstrg_sites_holidays = array(
									  'id' => array(
													'type' => 'INT',
													'constraint' => '11',
													'auto_increment' => TRUE
													),
									  'site_id' => array(
														 'type' => 'INT',
														 'constraint' => '11'
														 ),
									  'date_start' => array(
															'type' => 'DATE'
															),
									  'date_end' => array(
														  'type' => 'DATE'
														  ),
									  'time_start' => array(
															'type' => 'TIME'
															),
									  'time_end' => array(
														  'type' => 'TIME'
														  )
									  );
	  $this->dbforge->add_field($ramstrg_sites_holidays);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_sites_holidays') ;
	  /*tabelle mit geschäftszeiten*/
	  $ramstrg_business_hours = array(
									  'id' => array(
													'type' => 'INT',
													'constraint' => '11',
													'auto_increment' => TRUE
													),
									  'site_id' => array(
														 'type' => 'INT',
														 'constraint' => '11'
														 ),
									  'day_start' => array(
														   'type' => 'INT',
														   'constraint' => '11'
														   ),
									  'day_end' => array(
														 'type' => 'INT',
														 'constraint' => '11'
														 ),
									  'time_start' => array(
															'type' => 'time'
															),
									  'time_end' => array(
														  'type' => 'time'
														  )
									  );
	  $this->dbforge->add_field($ramstrg_business_hours);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_sites_business_hours') ;
	  /*tabelle mit standorten*/
	  $ramstrg_sites = array(
							 'id' => array(
										   'type' => 'INT',
										   'constraint' => '11',
										   'auto_increment' => TRUE
										   ),
							 'name' => array(
											 'type' => 'VARCHAR',
											 'constraint' => '155'
											 ),
							 'slug' => array(
											 'type' => 'VARCHAR',
											 'constraint' => '155'
											 ),
							 'str' => array(
											'type' => 'VARCHAR',
											'constraint' => '155'
											),
							 'nr' => array(
										   'type' => 'VARCHAR',
										   'constraint' => '4'
										   ),
							 'plz' => array(
											'type' => 'VARCHAR',
											'constraint' => '5'
											),
							 'ort' => array(
											'type' => 'VARCHAR',
											'constraint' => '155'
											)
							 );
	  /*tabelle sites settings*/
	  $ramstrg_setting = array(
							   'slug' => 'ramstrg_setting',
							   'title' => 'Ramstrg Setting',
							   'description' => 'A Yes or No option for the Ramstrg module',
							   '`default`' => '1',
							   '`value`' => '1',
							   'type' => 'select',
							   '`options`' => '1=Yes|0=No',
							   'is_required' => 1,
							   'is_gui' => 1,
							   'module' => 'ramstrg'
							   );
	  $this->dbforge->add_field($ramstrg_sites);
	  $this->dbforge->add_key('id', TRUE);
	  if($this->dbforge->create_table('ramstrg_sites') AND
		 $this->db->insert('settings', $ramstrg_setting) AND
		 is_dir($this->upload_path.'ramstrg') OR @mkdir($this->upload_path.'ramstrg',0777,TRUE))
		 {
			return TRUE;
		 }
   }
   public function uninstall()
   {
	  $this->dbforge->drop_table('ramstrg_sites');
	  $this->dbforge->drop_table('ramstrg_sites_business_hours');
	  $this->dbforge->drop_table('ramstrg_sites_holidays');
	  $this->dbforge->drop_table('ramstrg_bookings');
	  $this->dbforge->drop_table('ramstrg_site_ramps');
	  $this->dbforge->drop_table('ramstrg_carrier');
	  $this->dbforge->drop_table('ramstrg_trucks');
	  $this->dbforge->drop_table('ramstrg_users_carrier');
	  $this->db->delete('settings', array('module' => 'ramstrg'));
	  return TRUE;
   }
   public function upgrade($old_version)
   {
	  // Your Upgrade Logic
	  /*tabelle mit exakten daten und zeiten wenn rampe nicht verfügbar ist*/
	  $ramstrg_ramp_nonavail = array(
									 'id' => array(
												   'type' => 'INT',
												   'constraint' => '11',
												   'auto_increment' => TRUE
												   ),
									 'ramp_id' => array(
														'type' => 'INT',
														'constraint' => '11'
														),
									 'date_start' => array(
														   'type' => 'DATE'
														   ),
									 'date_end' => array(
														 'type' => 'DATE'
														 ),
									 'time_start' => array(
														   'type' => 'TIME'
														   ),
									 'time_end' => array(
														 'type' => 'TIME'
														 )
									 );
	  $this->dbforge->add_field($ramstrg_ramp_nonavail);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_ramp_nonavail') ;

	  /*tabelle mit periodischen angaben wann rampe nicht verfügbar ist*/
	  $ramstrg_ramp_nonavail_period = array(
											'id' => array(
														  'type' => 'INT',
														  'constraint' => '11',
														  'auto_increment' => TRUE
														  ),
											'ramp_id' => array(
															   'type' => 'INT',
															   'constraint' => '11'
															   ),
											'day_start' => array(
																 'type' => 'INT',
																 'constraint' => '11'
																 ),
											'day_end' => array(
															   'type' => 'INT',
															   'constraint' => '11'
															   ),
											'time_start' => array(
																  'type' => 'time'
																  ),
											'time_end' => array(
																'type' => 'time'
																)
											);
	  $this->dbforge->add_field($ramstrg_ramp_nonavail_period);
	  $this->dbforge->add_key('id', TRUE);
	  $this->dbforge->create_table('ramstrg_ramp_nonavail_period') ;


	  return TRUE;
		
   }
   public function help()
   {
	  // Return a string containing help info
	  // You could include a file and return it here.
	  return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
   }
}
/* End of file details.php */