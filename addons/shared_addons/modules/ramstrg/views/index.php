<div class="ramstrg-container">

  {{ if items_exist == false }}
  <p>Keine Eintr&auml;ge vorhanden</p>
  {{ else }}

  {{items}}
<div class="large-12 columns left">
        <div class="panel radius">
          <div class="row">
            <div class="large-5 small-5 columns">
              <h4>{{name}}</h4>
              <hr>

              <h5 class="subheader">
		<span class="street-address">{{str}} {{nr}}</span>
<br>
		<span class="locaspanty">{{plz}} {{ort}}</span>

</h5>

<a href="<?php echo site_url('ramstrg/showCal/'.date('Y/m/d',time()));?>/{{ id }}"  class="small radius button">Diesen Standort Beliefern</a>
            </div>

            <div class="large-7 small-7 columns">
{{ map }}
            </div>
          </div>
        </div>
      </div>
<!--
  <div class="row panel radius">
	<div class="large-3 columns vcard">
		<span class="fn">{{name}}</span>
		<span class="street-address">{{str}} {{nr}}</span>
		<span class="locaspanty">{{plz}} {{ort}}</span>
		<span><span class="state">Caprica</span>, <span class="zip">12345</span>

	</div>
		  <div class="large-9 columns left">{{ map }}</div>
  </div>
<p>&nbsp;</p>
-->
  {{/items}}
  
  {{ endif }}
  
</div>
