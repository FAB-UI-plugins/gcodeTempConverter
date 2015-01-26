<script type="text/javascript">
    

    var filTable;

    $.ajaxSetup ({
        // Disable caching of AJAX responses
        cache: false
    });
    
    $(function () {
        
        
        
        
        $("#submit").on('click', function() {
            
           
            $('#submit').addClass("disabled");
            $('#submit').html('<i class="fa fa-spin fa-spinner"></i> Converting');
            
            
            var note         = encodeURIComponent($.trim($("#note").val()));
            var name         = encodeURIComponent($.trim($("#name").val()));

            var tbl = get_data_from_table(filTable);
            var useRow = null;
            var radios = $("input[name='checkbox-inline']")
            
            for (row = 0 ; row < radios.length ; row++){

                if (radios[row].checked){

                    useRow = filTable.rows($(radios[row]).parents('tr')[0]).data()[0];
                    
                }
                  
            }
            
            if (!useRow) {
            	$.smallBox({
    				title : "Failed!",
    				content : "<i class='fa'></i> The processing failed.",
    				color : "#ff0000",
    				iconSmall : "fa fa-thumbs-down bounce animated",
                    timeout : 4000
                });
                $('#submit').removeClass("disabled");
                $('#submit').html('<i class="fa fa-save"></i> Convert File');
				return false;
			}
			
        
            
            $.ajax({
              type: "POST",
              url: "/fabui/application/plugins/gcodeTempConverter/assets/ajax/convert_file.php",
              data: {file_id: <?php echo $_file->id; ?>,  
                     file_path : '<?php echo $_file->full_path ?>', 
                     note: note, 
                     name: name, 
                     bed_temp_first : useRow[3],
                     bed_temp : useRow[4],
                     ext_temp_first : useRow[5],
                     ext_temp : useRow[6]
                     },
              dataType: 'html'
            }).done(function( response ) {
                console.log(response);
                
                $.smallBox({
    				title : "Success",
    				content : "<i class='fa fa-check'></i> The file was converted",
    				color : "#659265",
    				iconSmall : "fa fa-thumbs-up bounce animated",
                    timeout : 4000
                });
                
                $('#submit').removeClass("disabled");
                $('#submit').html('<i class="fa fa-save"></i> Convert File');
                load_file_content();
              
            });
                        
        });
             
        $("tbody .editable").click(function () { 
			
			var OriginalContent = $(this).text(); 
			$(this).addClass("cellEditing"); 
			$(this).html("<input type='text' id='tblEdit' value='" + OriginalContent + "' />"); 
			$(this).children().first().focus(); 
			$(this).children().first().keypress(function (e) { 
				if (e.which == 13) { 
					var newContent = $(this).val(); 
					$(this).parent().text(newContent); 
					$(this).parent().removeClass("cellEditing"); 
				} 
			}); 
			
			$(this).children().first().blur(function(){ 
				$(this).parent().text(OriginalContent); 
				$(this).parent().removeClass("cellEditing"); 
			}); 
			
			$(this).removeClass("cellEditing");
		});

        $("#save-fil").on('click', function(){
			$('#save-fil').addClass("disabled");
            $('#save-fil').html('<i class="fa fa-spin fa-spinner"></i> Saving');
            

			
			var tbl = get_data_from_table(filTable);
			

			
			$.ajax({
              type: "POST",
              url: "/fabui/application/plugins/gcodeTempConverter/assets/ajax/filament_file.php",
              data: {
                    method: 'write',
                    configFile: '/var/www/fabui/application/plugins/gcodeTempConverter/assets/config/config.json',
                    jsonString: JSON.stringify(tbl)
                	},
                dataType: "html"
            }).done(function(data) {
				$.smallBox({
    				title : "Success",
    				content : "<i class='fa fa-check'></i> The file was saved",
    				color : "#659265",
    				iconSmall : "fa fa-thumbs-up bounce animated",
                    timeout : 4000
                });

                $('#save-fil').removeClass("disabled");
				$('#save-fil').html('<i class="fa fa-save"></i> Save List');
            });


		});
        
        
        load_file_content();
       
        var selectedCheck;

		filTable = $("#filament_table").DataTable({
    		"processing": true,
        	"ajax" : {
        		"type" : "POST",
        		"url" : "/fabui/application/plugins/gcodeTempConverter/assets/ajax/filament_file.php",
        		"data" : {"method" : "read"},
        		
        	},
        	"columns": [
		            { "data" : null,
			          "render": function ( data, type, full, meta ) {

		            	      return '<label class="radio">	<input type="radio" name="checkbox-inline" id=fil-' + data[0] + '><i></i></label>';
		            	      }
          	      	},
		            { "data" : 2 },
		            { "data" : 3 },
		            { "data" : 4 },
		            { "data" : 5 },
		            { "data" : 6 },
		            { "data" : null,
			          "render" : function ( data, type, full, meta ) {
				            return '<a class="edit" href="">Edit</a><br><a class="delete" href="">Delete</a>';
				            }
		            }
		            

		          ],
			"initComplete": function () {
				try {
					$("input[name='checkbox-inline']")[0].click();
				} catch (e) {
				}
				

			},
			"drawCallback": function () {


		        
				$("input[name='checkbox-inline']").off('change');
            	$("input[name='checkbox-inline']").on('change', '', function () {
            		selectedCheck = this.id;

            		var nRow = $(this).parents('td');

            		console.log(filTable.cell(nRow).data());

                });
          	

				$('#' + selectedCheck).click();
	
        	}
      		
        });

        $("#new-row").on('click', function(){
        	var tmpArray = filTable.rows().data();
        	var lastId = 0;

         	for (var i = 0; i < tmpArray.length; i++) {
             	if (tmpArray[i][0] > lastId){
                 	lastId = tmpArray[i][0];
             	}
         	
         	}
            filTable.row.add( [
                               lastId + 1,
                               false,
                               '[Name]',
                               0,
                               0,
                               0,
                               0
                               ]).draw();

        } );

            

        $('#filament_table').on('click', 'a.delete', function (e) {
            e.preventDefault();
            var nRow = $(this).parents('tr')[0];
            filTable.row(nRow).remove();
            filTable.draw();

            

        } );
                

        var nEditing = null;
        
        $('#filament_table').on('click', 'a.edit', function (e) {
            e.preventDefault();
     
            /* Get the row as a parent of the link that was clicked on */
            var nRow = $(this).parents('tr')[0];
     
            if ( nEditing !== null && nEditing != nRow ) {
                /* A different row is being edited - the edit should be cancelled and this row edited */
                restoreRow( filTable, nEditing );
                editRow( filTable, nRow );
                nEditing = nRow;
            }
            else if ( nEditing == nRow && this.innerHTML == "Save" ) {
                /* This row is being edited and should be saved */
                saveRow( filTable, nEditing );
                nEditing = null;
            }
            else {
                /* No row currently being edited */
                editRow( filTable, nRow );
                nEditing = nRow;
            }
        } );

   
    });

    function restoreRow ( oTable, nRow )
    {
    	var aData = oTable.row(nRow).data();
    	var jqTds = $('>td', nRow);
    	
    	
    	for( var i=1, iLen=jqTds.length ; i<iLen ; i++ ) {
    		oTable.cell(nRow, i).data(aData[i+1]);
    		
    	}
    	
    	oTable.draw();
    }



    function editRow ( oTable, nRow )
    {
        var aData = oTable.row(nRow).data();
        var jqTds = $('>td', nRow);
        jqTds[1].innerHTML = '<input type="text" value="'+aData[2]+'">';
        jqTds[2].innerHTML = '<input type="text" value="'+aData[3]+'">';
        jqTds[3].innerHTML = '<input type="text" value="'+aData[4]+'">';
        jqTds[4].innerHTML = '<input type="text" value="'+aData[5]+'">';
        jqTds[5].innerHTML = '<input type="text" value="'+aData[6]+'">';
        jqTds[6].querySelector('.edit').innerHTML = 'Save';
        
    }

           
            
            

    function saveRow ( oTable, nRow ) {
        var jqInputs = $('input', nRow);

        for (i = 2 ; i <= 5 ; i++) {
            if (isNaN(jqInputs[i].value)) {
            	restoreRow ( oTable, nRow );
            	return;
            }
        }
        
       	
        oTable.cell(nRow, 1).data(jqInputs[1].value);
		oTable.cell(nRow, 2).data(parseInt(jqInputs[2].value));
        oTable.cell(nRow, 3).data(parseInt(jqInputs[3].value));
        oTable.cell(nRow, 4).data(parseInt(jqInputs[4].value));
        oTable.cell(nRow, 5).data(parseInt(jqInputs[5].value));
// 		oTable.cell(nRow, 6).data('Edit');
        oTable.draw();

        saveFilamentTable()
	
		
        
    }

    function saveFilamentTable(){
		$('#save-fil').addClass("disabled");
        $('#save-fil').html('<i class="fa fa-spin fa-spinner"></i> Saving');
        
		var tbl = get_data_from_table(filTable);

		$.ajax({
          type: "POST",
          url: "/fabui/application/plugins/gcodeTempConverter/assets/ajax/filament_file.php",
          data: {
                method: 'write',
                configFile: '/var/www/fabui/application/plugins/gcodeTempConverter/assets/config/config.json',
                jsonString: JSON.stringify(tbl)
            	},
            dataType: "html"
        }).done(function(data) {
			$.smallBox({
				title : "Success",
				content : "<i class='fa fa-check'></i> The file was saved",
				color : "#659265",
				iconSmall : "fa fa-thumbs-up bounce animated",
                timeout : 4000
            });

            $('#save-fil').removeClass("disabled");
			$('#save-fil').html('<i class="fa fa-save"></i> Save List');
        });
    }

    

	
    function get_data_from_table(table){

		var tbl = [];
        var tmpArray = table.rows().data();

        for (var i = 0; i < tmpArray.length; i++) {
            tbl.push(tmpArray[i]); 
        	
        }

		
		return tbl;
	}

    
    function load_file_content(){
         
         $.get( "<?php echo 'http://'.$_SERVER['HTTP_HOST'].str_replace('/var/www/', '/', $_file->full_path)."?t=".time() ?>", function( data ) {
		
			 
            $("#editor").html(data);
            $("#file-content-title").html('Content');
            $("#editor").show();
             
             
         });
        
        
    }
    
    
    
    


    
    
</script>
