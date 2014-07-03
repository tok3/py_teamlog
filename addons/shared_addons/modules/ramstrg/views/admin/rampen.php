<section class="title">
  <h4><?php echo lang('ramstrg:ramp_list'); ?></h4>
</section>

<?php
  $active['0'] = 'Nein';
  $active['1'] = 'Ja';
?>
<section class="item">
  <div class="content">
	<?php echo form_open('admin/ramstrg/delete');?>
	
	<?php if (!empty($ramps)): ?>


	<table>
	  <thead>
		<tr>
		  <th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
		  <th><?php echo lang('ramstrg:site'); ?></th>
		  <th><?php echo lang('ramstrg:ramp'); ?></th>
		  <th class="rampActive"><?php echo lang('ramstrg:active'); ?></th>
		  <th><?php echo lang('ramstrg:plz'); ?></th>
		  <th><?php echo lang('ramstrg:ort'); ?></th>
		  
		  <th></th>
		</tr>
	  </thead>
	  <tfoot>
		<tr>
		  <td colspan="6">
			<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
		  </td>
		</tr>
	  </tfoot>
	  <tbody>
		<?php foreach( $ramps as $ramp ): ?>
		<tr>
		  <td><?php echo form_checkbox('action_to[]', $ramp->id); ?></td>
		  <td><?php echo $ramp->site_name; ?></td>
		  <td><?php echo $ramp->name; ?></td>
		  <td class="rampActive"><?php echo $active[$ramp->active]; ?></td>
		  <td><?php echo $ramp->plz; ?></td>
		  <td><?php echo $ramp->ort; ?></td>
		  <td class="actions">
			<?php echo
			  anchor('admin/ramstrg/ramp/edit/'.$ramp->id, lang('ramstrg:edit'), 'class="button"').' '.
			  anchor('admin/ramstrg/ramp/delete/'.$ramp->id, 	lang('ramstrg:delete'), array('class'=>'button rampDel')); ?>
		  </td>
		</tr>
		<?php endforeach; ?>
	  </tbody>
	</table>
	
	<div class="table_action_buttons">
	  <?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
	</div>
	
	<?php else: ?>
	<div class="no_data"><?php echo lang('ramstrg:no_items'); ?></div>
	<?php endif;?>
	
	<?php echo form_close(); ?>
  </div>
</section>
