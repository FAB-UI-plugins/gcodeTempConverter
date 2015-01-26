<script type="text/javascript">

    var oTable;
    
	$(document).ready(function() {


        
        $('#objects_table').dataTable({
            "aaSorting": [],
            "bProcessing": true,
            "sAjaxSource": '/fabui/application/plugins/gcodeTempConverter/assets/ajax/all_objects_for_table.php'
            
        });
        
        $("[rel=tooltip]").tooltip();

	});

    
    
    
    
    
</script>
