<section class="title">
  <!-- We'll use $this->method to switch between ramstrg.create & ramstrg.edit -->
  <h4><?php echo lang('ramstrg:'.$this->method); ?></h4>
</section>

<section class="item">
  <div class="content">

	<?php 

	   echo '<script> var formMode = "'.$this->uri->segment('3').'";</script>';

echo form_open_multipart($this->uri->uri_string(), 'class="crud" id="formRamp"'); 
?>


<div class="tabs">
  <ul class="tab-menu">
	<li><a href="#sites-panel-details"><span>Details</span></a></li>
				<li><a href="#sites-panel-business-hours"><span><?php echo lang('ramstrg:non_avail_period'); ?></span></a></li>
				<li><a href="#sites-panel-holidays"><span><?php echo lang('ramstrg:non_avail_date'); ?></span></a></li>

  </ul>
  <div  class="form_inputs" id="sites-panel-details">
	<!-- details--> 
	<fieldset>
	  <ul>
		<li class="<?php echo alternator('', 'even'); ?>">
		  <label for="active"><?php echo lang('ramstrg:active'); ?></label>
		  <div class="input"><?php echo form_hidden('ramp[active]', '0')  . form_checkbox('ramp[active]', '1', $value['active']); ?>
		  </div>
		</li>

		<li class="<?php echo alternator('', 'even'); ?>">
		  <label for="site_id"><?php echo lang('ramstrg:site'); ?> <span>*</span></label>
		  <div class="input"><?php echo form_dropdown('ramp[site_id]', $sites, $value['site_id']); ?>
		  </div>
		</li>
		<li class="<?php echo alternator('', 'even'); ?>">
		  <label for="name"><?php echo lang('ramstrg:name'); ?> <span>*</span></label>
		  <div class="input"><?php echo form_input('ramp[name]', set_value('ramp[name]', $value['name']), 'class="width-20"'); ?>
		  </div>
		</li>
		<li class="<?php echo alternator('', 'even'); ?>">
		  <label for="name"><?php echo lang('ramstrg:description'); ?></label>
		  <div class="input"><?php echo form_textarea('ramp[description]', set_value('ramp[description]', $value['description']), 'cols="2"'); ?>
		  </div>
		</li>
	  </ul>
	</fieldset>
	<!-- ende details -->
  </div> 

			<div  class="form_inputs" id="sites-panel-business-hours">
				<!-- öffnungszeiten --> 
				<fieldset>


					<?php if (!empty($non_av_period)): ?>

					<table>
						<thead>
							<tr>
								<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
								<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:from'); ?></th>
								<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:to'); ?></th>
								<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:from'); ?></th>
								<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:to'); ?></th>
								
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5">
									<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
								</td>
							</tr>
						</tfoot>
						<tbody>

							<?php 
							$day_names = lang('ramstrg:day_names');
							foreach( $non_av_period as $item ): ?>
							<tr>
								<td><?php echo form_checkbox('del_bh[]', $item['id']); ?></td>
								<td><?php echo $item['d_start']; ?></td>
								<td><?php echo $item['d_end']; ?></td>
								<td><?php echo $item['t_start']; ?></td>
								<td><?php echo $item['t_end']; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<span class="table_action_buttons">
					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
				</span>

				<?php echo anchor('admin/ramstrg/ramp/create_nonav_period/' . $this->uri->rsegment(3), lang('ramstrg:add'), 'target="_blank" class="" id="newBH"') ?>


			<?php else: ?>
			<div class="no_data"><?php echo lang('ramstrg:no_items'); ?>
				<?php echo anchor('admin/ramstrg/ramp/create_nonav_period/' . $this->uri->rsegment(3), lang('ramstrg:add'), 'target="_blank" class="green" id="newBH"') ?>
			</div>
		<?php endif;?>

	</fieldset>
	<!-- ende öffnungszeiten -->

</div>		

<div  class="form_inputs" id="sites-panel-holidays">
	<!-- öffnungszeiten --> 
	<fieldset>


		<?php if (!empty($non_av)): ?>

		<table>
			<thead>
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
					<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:from'); ?></th>
					<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:from'); ?></th>
					<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:to'); ?></th>
					<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:to'); ?></th>

				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>

				<?php 
				$day_names = lang('ramstrg:day_names');
				foreach( $non_av as $item ): ?>
				<tr>
					<td><?php echo form_checkbox('del_holidays[]', $item['id']); ?></td>
					<td><?php echo $item['date_start']; ?></td>
					<td><?php echo $item['time_start']; ?></td>
					<td><?php echo $item['date_end']; ?></td>
					<td><?php echo $item['time_end']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<span class="table_action_buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
	</span>

	<?php echo anchor('admin/ramstrg/ramp/create_nonav/' . $this->uri->rsegment(3), lang('ramstrg:add'), 'target="_blank" class="green" id="newHD"') ?>


<?php else: ?>
	<div class="no_data"><?php echo lang('ramstrg:no_items'); ?>


		<?php echo anchor('admin/ramstrg/ramp/create_nonav/' . $this->uri->rsegment(3), lang('ramstrg:add'), 'target="_blank" class="green" id="newHD"') ?>
	</div>
<?php endif;?>

</fieldset>
<!-- ende öffnungszeiten -->
</div>

</div>
<div class="buttons">
  <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save','save_exit', 'cancel') )); ?>
</div>

<?php echo form_close(); ?>

</div> <!-- /content -->

</section>
