<fieldset id="filters">
	<legend><?php echo lang('global:filters') ?></legend>

	<?php form_open('', '', array('f_module' => $module_details['slug'])) ?>
		<ul>
			<li class="">
        		<label for="f_active"><?php echo lang('ramstrg:ramp') ?></label>
        		<?php echo form_dropdown('f_active', array('' => lang('global:select-all'), 0=>'Inaktiv', 1=>'Aktiv')) ?>
    		</li>


			<li class="">
				<?php echo anchor(current_url() . '#', lang('buttons:cancel'), 'class="button red"') ?>
			</li>
		</ul>
	<?php echo form_close() ?>
</fieldset>
