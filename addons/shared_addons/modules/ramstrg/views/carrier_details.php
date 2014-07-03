<?php echo $fields['open'];?>
  <fieldset>
	<legend>Spedituer Details</legend>
					  <?php echo $err_enter_details;?>

					  <?php echo $form_errors;?>

	<div class="row"> 
	  <div class="large-6 columns"> 
		<label>Name</label> 
			<?php echo $fields['name'];?> 
			<?php echo $fields['name_2'];?> 
	  </div> 
	</div> <!-- ende row -->

	<div class="row"> 
	  <div class="large-4 columns">
	 <label>Strasse</label> 
		<?php echo $fields['str'];?>
	  </div>

	  <div class="large-2 columns left">
		<label>Nr.</label>
				<?php echo $fields['nr'];?>
	  </div>

	</div> <!-- ende row -->

	<div class="row"> 
	  <div class="large-2 columns">
	 <label>PLZ</label> 
		<?php echo $fields['plz'];?>
	  </div>

	  <div class="large-4 columns left">
		<label>Ort</label>
				<?php echo $fields['ort'];?>
	  </div>

	</div> <!-- ende row -->

	<div class="row"> 
	  <div class="large-3 columns">
	 <label>Telefon</label> 
		<?php echo $fields['tel'];?>
	  </div>

	  <div class="large-3 columns left">
		<label>email</label>
				<?php echo $fields['email'];?>
	  </div>

	</div> <!-- ende row -->

							 </fieledset>

<?php echo $fields['submit'];?>
<?php echo $fields['close'];?>
