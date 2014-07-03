<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Admin Page Layouts controller for the Pages module
 *
 * @author  PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Blog\Controllers
 */
class Admin_Ramp extends Admin_Controller
{

   /** @var int The current active section */
   protected $section = 'ramps';

   public function __construct()
   {
	  parent::__construct();



	  // Load all the required classes
	  $this->load->model('ramstrg_sites_m');
	  $this->load->model('ramstrg_ramp_m');

	  $this->load->model('general_m');
		

	  $this->lang->load('ramstrg');
	  $this->load->library('form_validation');


	  // Set the validation rules
	  $this->validation_rules = array(
									  array(
											'field' => 'ramp[name]', 
											'label' => 'Name', 
											'rules' => 'trim|max_length[100]|required'
											),
									  array(
											'field' => 'ramp[site_id]',
											'label' => 'Standort',
											'rules' => 'required|greater_than[0]'
											)


									  );
	  $this->form_validation->set_rules($this->validation_rules);
  
  // We'll set the partials and metadata here since they're used everywhere
	  $this->template->append_js('module::admin.js')->append_css('module::admin.css');
 }

   // --------------------------------------------------------------------
   /**
	* List all Sites/ Standorte
	*/
   public function index()
   {


	  if($this->input->is_ajax_request())
		 {
            $this->template->set_layout(false);   
		 }

	  $ramps = $this->ramstrg_ramp_m->get_overview();

	  $base_where = array('active' => 1);

	  $ramps = $this->ramstrg_ramp_m->get_overview();

	  // view mit liste der rampen erzeugen 
	  $this->template
		 ->title($this->module_details['name'])
		 ->set('ramps', $ramps)
		 ->set_partial('filters', 'admin/partials/testfilter')
		 ->append_js('admin/filter.js')
		 ->build('admin/rampen');
   }

   // --------------------------------------------------------------------
   /**
	* rampen zu standort hinzufuegen 
	*
	* @return void
	* @author 
	**/
   public function create()
   {

	  $this->form_validation->set_message('greater_than', $this->lang->line('ramstrg:err_select_req'));

	  // SET the validation rules from the array above
	  $this->form_validation->set_rules($this->validation_rules);

	  // check if the form validation passed

	  if ($this->form_validation->run())
		 {

			// See if the model can create the record
			if ($this->ramstrg_ramp_m->create($this->input->post('ramp'))) 
			   {
				  $insertID = $this->db->insert_id();
				  // All good...
				  $this->session->set_flashdata('success', lang('ramstrg.success'));
				  redirect('admin/ramstrg/ramp/edit/' . $insertID);
			   } 
			// Something went wrong. Show them an error
			else 
			   {
				  $this->session->set_flashdata('error', lang('ramstrg.error'));
				  redirect('admin/ramstrg/ramp/create');
			   }
		 }

	  $postValues = $this->input->post('ramp');

 	  // view mit liste der rampen erzeugen 

	  $this->template
		 ->title($this->module_details['name'])
		 ->set('value',$postValues)
		 ->set('sites',$this->_site_opt())
		 ->build('admin/form_rampe');
   }
   // --------------------------------------------------------------------
   /**
	* rampen zu standort bearbeiten 
	*
	* @return void
	* @author 
	**/
   public function edit($_id)
   {

	  
	  // geschÃ¤ftszeiten holen 


	  $this->general_m->set_table('ramstrg_ramp_nonavail_period');
	  $nonAvPeriod = $this->general_m->get_many_by('ramp_id',$_id);

	  $this->general_m->set_table('ramstrg_ramp_nonavail');
	  $nonAv = $this->general_m->get_many_by('ramp_id',$_id);


	  if ($this->form_validation->run())
		 {

			$this->_updNonAV_period($_POST['bh']);
			if(isset($_POST['del_bh']))
			   {
				  $this->_delNonAV_period($_POST['del_bh'],'ramstrg_ramp_nonavail_period');
			   }

			$this->_updNonAV($_POST['holidays']);
			if(isset($_POST['del_holidays']))
			   {

				  $this->_delNonAV_period($_POST['del_holidays'],'ramstrg_ramp_nonavail');
			   }
			// See if the model can create the record
			if ($this->ramstrg_ramp_m->update($_id, $this->input->post('ramp'))) 
			   {
				  // All good...
				  $this->session->set_flashdata('success', lang('ramstrg.success'));

				  ($_POST['btnAction'] == 'save_exit') ? redirect('admin/ramstrg/ramp') : redirect('admin/ramstrg/ramp/edit/'.$_id);  
			   } 
			else  // Something went wrong. Show them an error
			   {
				  $this->session->set_flashdata('error', lang('ramstrg.error'));
				  redirect('admin/ramstrg/edit');
			   }

		 }



	  $values =  (array) $this->ramstrg_ramp_m->get($_id);
 	  // view mit liste der rampen erzeugen 


	  $this->template
		 ->title($this->module_details['name'])
		 ->set('non_av_period',$this->_getNonAV_period_inputs($_id))
		 ->set('non_av',$this->_getNonAV_inputs($_id))
		 ->set('value',$values)
		 ->set('sites',$this->_site_opt())
		 ->build('admin/form_rampe');
   }



   // --------------------------------------------------------------------
   /**
	* rampe löschen
	* 
	* @access 	public	
	* @param 	int	id
	* @return 	void	
	* 
	*/
   public function delete($id = 0)
   {
	  // make sure the button was clicked and that there is an array of ids
	  if (isset($_POST['btnAction']) AND is_array($_POST['action_to'])) 
		 {
	  $this->general_m->set_table('ramstrg_ramp_nonavail_period');
	  $this->general_m->del_by_col($this->input->post('action_to'),'ramp_id');



		 } 
	  elseif (is_numeric($id)) 
		 {
			// they just clicked the link so we'll delete that one
			$this->ramstrg_ramp_m->delete($id);

	  $this->general_m->set_table('ramstrg_ramp_nonavail_period');
	  $this->general_m->del_by_col($id,'ramp_id');

		 }

	  
	  redirect('admin/ramstrg/ramp');
   }

   // --------------------------------------------------------------------
   /**
	* dropdown options site
	* 
	* @access 	private	
	* @param 	void	
	* @return 	array	
	*/
   private function _site_opt()
   {
	  $options[0] = $this->lang->line('ramstrg:select_first');
	  $sites = $this->ramstrg_sites_m->get_all();

	  foreach($this->ramstrg_sites_m->get_all() as $key => $site)
		 {
			$options[$site->id] = $site->name; 
		 }

	  return $options;
   }

   // --------------------------------------------------------------------
   /**
	* create non availability time by period // ajax called
	*
	* @return void
	* @author tobias
	**/
   public function create_nonav_period()
   {

	  $days = array_combine(range(0, 6), $this->lang->line('ramstrg:day_names'));
	  $data['d_start'] = form_dropdown('day_start', $days, 1, 'class="day_select"');
	  $data['d_end'] = form_dropdown('day_end', $days, 5, 'class="day_select"');
	  $data['t_start'] = $this->format->timeSelect((date('H:i', time())), 'start');
	  $data['t_end'] = $this->format->timeSelect(date('H:i', time()), 'end');

	  	  // Render the view and echo for ajax 
	  $form = $this->load->view('admin/form_create_nonav_period', $data, true);
	  echo $form;
   }


   // --------------------------------------------------------------------
   /**
	* create non availability time by exact date and time // ajax called
	*
	* @return void
	* @author tobias
	**/
   public function create_nonav()
   {

	  $data['d_start'] = form_input('date_start', '', 'maxlength="10" class="datepicker dpBH"');
	  $data['d_end'] = form_input('date_end', '', 'maxlength="10" class="datepicker dpBH"');

	  $data['t_start'] = $this->format->timeSelect((date('H:i', time())), 'start');
	  $data['t_end'] = $this->format->timeSelect(date('H:i', time()), 'end');

	  	  // Render the view and echo for ajax 
	  $form = $this->load->view('admin/form_create_nonav', $data, true);
	  echo $form;
   }

   // --------------------------------------------------------------------
   /**
	* belegtzeit periodisch akrualisieren
	*
	* @return void
	* @author
	**/
   private function _updNonAV_period($_non_av_period)
   {
	  $this->general_m->set_table('ramstrg_ramp_nonavail_period');

	  foreach ($_non_av_period as $id => $data) {
		 $upd['day_start'] = $data['day_start'];
		 $upd['day_end'] = $data['day_end'];
		 $upd['time_start'] = $data['hour_start'] . ':' . $data['min_start'];
		 $upd['time_end '] = $data['hour_end'] . ':' . $data['min_end'];

	  $this->general_m->update($id, $upd);


	  }
   }

   private function _updNonAV($_non_av_period)
   {
	  $this->general_m->set_table('ramstrg_ramp_nonavail');

	  foreach ($_non_av_period as $id => $data) {
		 $upd['date_start'] = $data['date_start'];
		 $upd['date_end'] = $data['date_end'];
		 $upd['time_start'] = $data['hour_start'] . ':' . $data['min_start'];
		 $upd['time_end '] = $data['hour_end'] . ':' . $data['min_end'];

	  $this->general_m->update($id, $upd);


	  }
   }

   // --------------------------------------------------------------------
   /**
	* geschaeftszeiten loeschen
	*
	* @return void
	* @author
	**/
   private function _delNonAV_period($_id, $_table = 'ramstrg_ramp_nonavail_period')
   {
	  $this->general_m->set_table($_table);

	  foreach ($_id as $key => $id) {
	  $this->general_m->delete($id);

	  }

   }

   // --------------------------------------------------------------------
   /**
	* Belegtzeiten  erstellen periodisch
	*
	* @return void
	* @author
	**/
   public function insertNonAvPeriod()
   {
	  $this->general_m->set_table('ramstrg_sites_ramps');
	  $rampData = $this->general_m->get_many_by('id',$this->input->get_post('ramp_id'));
	  	  
	  $insDat['ramp_id'] = $this->input->get_post('ramp_id');
	  $insDat['site_id'] = $rampData[0]->site_id;
	  $insDat['day_start'] = $this->input->get_post('day_start');
	  $insDat['day_end'] = $this->input->get_post('day_end');
	  $insDat['time_start'] = $this->input->get_post('hour_start') . ':' . $this->input->get_post('min_start');
	  $insDat['time_end'] = $this->input->get_post('hour_end') . ':' . $this->input->get_post('min_end');

	  $this->general_m->set_table('ramstrg_ramp_nonavail_period');
	  $this->general_m->insert($insDat);

	  redirect('admin/ramstrg/ramp/edit/' . $insDat['ramp_id']);
   }

   // --------------------------------------------------------------------
   /**
	* Belegtzeiten  erstellen exactes datum
	*
	* @return void
	* @author
	**/
   public function insertNonAv()
   {

	  $this->general_m->set_table('ramstrg_sites_ramps');
	  $rampData = $this->general_m->get_many_by('id',$this->input->get_post('ramp_id'));
	  	  
	  $insDat['ramp_id'] = $this->input->get_post('ramp_id');
	  $insDat['site_id'] = $rampData[0]->site_id;
	  $insDat['date_start'] = $this->input->get_post('date_start');
	  $insDat['date_end'] = $this->input->get_post('date_end');
	  $insDat['time_start'] = $this->input->get_post('hour_start') . ':' . $this->input->get_post('min_start');
	  $insDat['time_end'] = $this->input->get_post('hour_end') . ':' . $this->input->get_post('min_end');

	  $this->general_m->set_table('ramstrg_ramp_nonavail');
	  $this->general_m->insert($insDat);

	  redirect('admin/ramstrg/ramp/edit/' . $insDat['ramp_id']);
   }


   // --------------------------------------------------------------------
   /**
	* input felder belegtzeiten periodisch beziehen 
	*
	* @return void
	* @author
	**/
   private function _getNonAV_period_inputs($_ramp_id)
   {

	  $this->general_m->set_table('ramstrg_ramp_nonavail_period');
	  $nonAvPeriod = $this->general_m->get_many_by('ramp_id',$_ramp_id);

	  foreach ($nonAvPeriod as $key => $value) 
		 {

			$days = array_combine(range(0, 6), $this->lang->line('ramstrg:day_names'));

			$data[$key]['id'] = $value->id;

			$data[$key]['d_start'] = form_dropdown('bh[' . $value->id . '][day_start]', $days, $value->day_start, 'class="day_select"');

			$data[$key]['t_start'] = $this->format->timeSelect($value->time_start, 'start', 'bh[' . $value->id . '][%%]');

			$data[$key]['d_end'] = form_dropdown('bh[' . $value->id . '][day_end]', $days, $value->day_end, 'class="day_select"');

			$data[$key]['t_end'] = $this->format->timeSelect($value->time_end, 'end', 'bh[' . $value->id . '][%%]');

		 }

	  if(isset($data)) 
		 {
			return $data;
		 } 
	  else 
		 {
			return false;
		 }
   }

  // --------------------------------------------------------------------
   /**
	* input felder belegtzeiten exaktes datum beziehen 
	*
	* @return void
	* @author
	**/
   private function _getNonAV_inputs($_ramp_id)
   {

	  $this->general_m->set_table('ramstrg_ramp_nonavail');
	  $nonAv = $this->general_m->get_many_by('ramp_id',$_ramp_id);

	  foreach ($nonAv as $key => $value) {

		 $data[$key]['id'] = $value->id;

		 $data[$key]['date_start'] = form_input('holidays[' . $value->id . '][date_start]', $value->date_start, 'maxlength="10" class="datepicker dpBH"');
		 $data[$key]['time_start'] = $this->format->timeSelect($value->time_start, 'start', 'holidays[' . $value->id . '][%%]');

		 $data[$key]['date_end'] = form_input('holidays[' . $value->id . '][date_end]', $value->date_end, 'maxlength="10" class="datepicker dpBH"');
		 $data[$key]['time_end'] = $this->format->timeSelect($value->time_end, 'end', 'holidays[' . $value->id . '][%%]');


	  }

	  if (isset($data)) 
		 {
			return $data;
		 } 
	  else 
		 {
			return false;
		 }
   }


}
