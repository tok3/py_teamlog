<?php echo $fields['open'];?>
  <fieldset>
	<legend>LKW Details</legend>
					  

					  <?php echo $form_errors;?>
	<div class="row"> 
	  <div class="large-6 columns left">
	<label><a href="<?php echo $backlink;?>"><i class="fi-arrow-left"></i>&nbsp;Zur&uuml;ck</a>
<hr>
</label>
	  </div>

	</div> <!-- ende row -->

	<div class="row"> 
	  <div class="large-3 columns">
	 <label>Name</label> 
		<?php echo $fields['name'];?>
	  </div>

	  <div class="large-3 columns left">
		<label>Kennzeichen</label>
				<?php echo $fields['lic_number'];?>
	  </div>

	  <div class="large-3 columns left">
		<label>test</label>
				<?php echo $fields['test'];?>
	  </div>

	</div> <!-- ende row -->

							 </fieledset>
	  <?php echo $fields['delete'];?>
<?php echo $fields['submit'];?>
<?php echo $fields['close'];?>
