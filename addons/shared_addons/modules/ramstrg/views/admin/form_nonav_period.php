<section class="title">
	<!-- We'll use $this->method to switch between ramstrg.create & ramstrg.edit -->
	<h4><?php echo lang('ramstrg:'.$this->method); ?></h4>
</section>

<?

		$day_names = lang('ramstrg:day_names');

?>


<section class="item">
	<div class="content">

		<?php 


		echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

		
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
					<td colspan="5">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>

				<tr>
					<td><?php echo $data['d_start'] ; ?></td>
					<td><?php echo $data['d_end'] ; ?></td>
				

					<td><?php echo $data['t_start'] ; ?></td>
					<td><?php echo $data['t_end'] ; ?></td>
				</tr>
			</tbody>
		</table>
		

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>

		<?php echo form_close(); ?>

	</div> <!-- /content -->

</section>
