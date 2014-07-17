/******************************************************************************************/
// This function grabs all the form data and puts it into the actual submitted input
// Returns: nothing
/******************************************************************************************/


/******************************************************************************************/
// Rebinds all the updates 
/******************************************************************************************/

function updateinput(thisform) {
	var string = '';
	var newQueryString = thisform.closest(".multifileselectform").parent().serializeArray();
	
	for (var i = 0; i < (newQueryString.length - 1) ; i += 4) {
		
		var aFileDir =		newQueryString[(i+1)]['value'];
		var aFileName = 	newQueryString[(i)]['value'];
		var aFileTitle = 	newQueryString[(i+2)]['value'];
		var aFileAlt =  	newQueryString[(i+3)]['value'];
		
		string = string + aFileDir + "~" + aFileName + "~" + aFileTitle + "~" + aFileAlt + "|";
	}
	thisform.closest(".multifileselectform").next().val(string);
}


/******************************************************************************************/
// Rebinds all the updates 
/******************************************************************************************/
function bindinput() {
	$('.multifileselectform input').off();
	$('.multifileselectform input').on('input', function(){
		updateinput($(this));
	});
}

/******************************************************************************************/
// Rebinds the remove buttons
/******************************************************************************************/
function removerow() {
	$('.removerow').off();
	$('.removerow').on('click', function(){
		var newbind = $(this).parent().parent();
		$(this).parent().remove();
		updateinput(newbind);
	});
}

/******************************************************************************************/
// Adds row [need to fix addnewfile()] and calls all the rebinds
/******************************************************************************************/
function addrow() {
	$('.addrowmulti').off();
	$('.addrowmulti').on('click',function(){
		$(this).prev().prev().find('tr:last').after('<tr><td style="width:30%;text-align:center">' + '<p><a class="add_new_file"> add file</a></p>' + '</td><td style="width:30%"><fieldset class="holder"><input name="filename" style="width:98%"></fieldset></td><td style="width:30%"><fieldset class="holder"><input name="filealt" style="width:98%"></fieldset></td><td class="removerow" style="width:10%;text-align:center"><button>x</button></td></tr>');		          
		removerow();
		bindinput();
		
		var params = {
            content_type: 'img',
            directory: $(this).prev().prev().data('directory')
        };

		$.ee_filebrowser.add_trigger($('.add_new_file'), 'test', params, 
			function(file){
				var tdparent = $(this).closest('td');
				tdparent.empty();
				tdparent.html('<img src="'+ file['thumb'] + '"><br><p>' + file['file_name'] +'</p><input type="hidden" name="filename" value="'+ file['file_name'] + '"><input type="hidden" name="filedir" value="' + file['upload_location_id'] + '">');
				updateinput(tdparent);
			});
	});
}

/******************************************************************************************/
// Call all the binds and call the matrix binds
/******************************************************************************************/

$(document).ready(function() {
	
	addrow();
	removerow();
	bindinput();
	updateinput($('.multifileselectform .removerow:last'));
	$('.multifileselectform').each(function(){
		$(this).tableDnD({
			onDrop: function(table, row) {
			        updateinput($(row));
			    }
		});
	});
});


