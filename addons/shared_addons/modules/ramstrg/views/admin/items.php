<section class="title">
	<h4><?php echo lang('ramstrg:item_list'); ?></h4>
</section>

<section class="item">
	  <div class="content">
	<?php echo form_open('admin/ramstrg/delete');?>
	
	<?php if (!empty($items)): ?>
	
		<table>
			<thead>
				<tr>
					<th><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
					<th><?php echo lang('ramstrg:name'); ?></th>
					<th><?php echo lang('ramstrg:plz'); ?></th>
						<th><?php echo lang('ramstrg:ort'); ?></th>
			
					<th></th>
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
				<?php foreach( $items as $item ): ?>
				<tr>
					<td><?php echo form_checkbox('action_to[]', $item->id); ?></td>
					<td><?php echo $item->name; ?></td>
				<td><?php echo $item->plz; ?></td>
			<td><?php echo $item->ort; ?></td>
							<td class="actions">
						<?php echo
						anchor('admin/ramstrg/edit/'.$item->id, lang('ramstrg:edit'), 'class="button"').' '.
						anchor('admin/ramstrg/delete/'.$item->id, 	lang('ramstrg:delete'), array('class'=>'button standortDel')); ?>
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
