<section class="title">
	<h4><?php echo lang('ramstrg:non_avail_period') . ' ' . lang('ramstrg:add'); ?></h4>
</section>

<section class="item">
	<div class="content">
		<?php 
		
		$hidden = array('ramp_id' => $this->uri->rsegment(3) );

		echo form_open_multipart(site_url('admin/ramstrg/ramp/insertNonAvPeriod'), 'class="crud" id="businessHours"', $hidden ); 
		?>


		<!-- öffnungszeiten --> 
		<fieldset>

			<table>
				<thead>
					<tr>
						<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:from'); ?></th>
						<th><?php echo lang('ramstrg:day') . ' ' . lang('ramstrg:to'); ?></th>
						<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:from'); ?></th>
						<th><?php echo lang('ramstrg:time') . ' ' . lang('ramstrg:to'); ?></th>

					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4">
							<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
						</td>
					</tr>
				</tfoot>
				<tbody>

					<?php 
					$day_names = lang('ramstrg:day_names');
					?>
					<tr>
						<td><?php echo $d_start; ?></td>
						<td><?php echo $d_end; ?></td>
						<td><?php echo $t_start; ?></td>
						<td><?php echo $t_end; ?></td>
					</tr>

				</tbody>
			</table>

			<div class="table_action_buttons">
				<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
			</div>
		</fieldset>
		<!-- ende öffnungszeiten -->
		<?php echo form_close(); ?>
	</section>
</div>