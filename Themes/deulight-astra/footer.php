<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php astra_content_bottom(); ?>
	</div> <!-- ast-container -->
	</div><!-- #content -->
<?php 
	astra_content_after();
		
	astra_footer_before();
		
	astra_footer();
		
	astra_footer_after(); 
?>
	</div><!-- #page -->
<?php 
	astra_body_bottom();    
	wp_footer();
?>
 <script>
jQuery(document).on('change', '#getUnittypes', function() {
  jQuery.ajax({
    type: 'GET',
    url: '/getUnittypes.php',
    data: {
      t: Math.random(),
      ManufacturerID: jQuery('#getUnittypes').val()
    },
    dataType: 'json',
    success: function(result) {
      var unitTypes = result.UnitTypes.Data;
      var models = result.Models.Data;

      // Populate UnitTypeID select field
      var unitTypeSelect = jQuery('#UnitTypeID');
      unitTypeSelect.empty().append('<option value="">---</option>');
      jQuery.each(unitTypes, function(index, unitType) {
        unitTypeSelect.append(jQuery('<option></option>').val(unitType.UnitTypeID).text(unitType.UnitTypeName));
      });

      // Populate ModelNumber select field
      var modelNumberSelect = jQuery('#getModelnumber');
      modelNumberSelect.empty().append('<option value="">---</option>');
      jQuery.each(models, function(index, model) {
        modelNumberSelect.append(jQuery('<option></option>').val(model.ModelID).text(model.ModelNumber));
      });
    },
    error: function() {
      // Handle error scenario
    }
  });
});
  
  jQuery(document).on('change', '#getModelnumber',function(){
     jQuery('#UnitTypeID').val(jQuery('#getModelnumber option:selected').attr('UnitTypeID'));
	 jQuery('#unitTypeDetails').val(jQuery('#UnitTypeID option:selected').text());
	 jQuery('#manufacturerDetails').val(jQuery('#getUnittypes option:selected').text());
	 jQuery('#modelDetails').val(jQuery('#getModelnumber option:selected').text());
  });
  
  </script>
	</body>
</html>
