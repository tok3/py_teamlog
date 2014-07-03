<?php echo $fields['open'];?>
  <fieldset>
	<legend>Anlieferung</legend>

	<div class="row"> 

					  <?php echo $form_errors;?>
	  <div class="large-3 columns">
		<label>Datum</label> 
		<?php echo $fields['del_date'];?>
	  </div>

	  <div class="large-3 columns left">
		<label>Uhrzeit</label>
				<?php echo $fields['del_time'];?>
	  </div>

	</div> <!-- ende row -->


	<div class="row"> 
	  <div class="large-3 columns">
		<label>Lieferant</label> 
		<?php echo $fields['del_supplier'];?>
	  </div>

	  <div class="large-3 columns left">
		<label>LKW - Kennzeichen</label>
				<?php echo $fields['del_lic_number'];?>
	  </div>

	</div> <!-- ende row -->


	<div class="row"> 
	  <div class="large-6 columns"> 
		<label>Art der Ladung</label> 
			<?php echo $fields['del_art_teil'];?><label for="teil">Teilladung</label> 
			<?php echo $fields['del_art_kompl'];?><label for="komplet">Komplettladung</label> 
	  </div> 
	</div> <!-- ende row -->



	<div class="row"> 
	  <div class="large-5 columns"> <div class="small-7 columns">
		  <label for="right-label" class="inline">Anzahl Peletten</label>
        </div>
        <div class="small-5 columns">
			<?php echo $fields['del_mnt_pal'];?>
        </div>
	  </div>
  </div> 

	<div class="row"> 
	  <div class="large-5 columns"> <div class="small-7 columns">
		  <label for="right-label" class="inline">Gewicht (KG)</label>
        </div>
        <div class="small-5 columns">
			<?php echo $fields['del_kg'];?>
        </div>
	  </div>
  </div> 

							 </fieledset>

<?php echo $fields['submit'];?>
<?php echo $fields['close'];?>
