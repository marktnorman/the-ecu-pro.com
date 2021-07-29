function resetApiKeyForm(){
	jQuery("#generate-api-key-form #api-key-description").val("").focus(); //NO I18N
	jQuery("#generate-api-key").attr("disabled", true); //NO I18N
	jQuery("#api-key-span").text(""); //NO I18N
	jQuery("#generate-api-key-form").show(); //NO I18N
	jQuery("#api-key-div").hide(); //NO I18N
	jQuery("#generate-api-key-error").remove(); //NO I18N
}


//TODO - Copy is not working
function copyApiKey(){
	var apiKey = jQuery("#api-key-span"); //NO I18N
	var range = document.createRange();
	range.selectNode(apiKey[0]);
	window.getSelection().addRange(range);	
	try {
		var successful = document.execCommand('copy'); //NO I18N
	} catch(err) {
		alert(i18n.unable_to_copy_api_key);
	}
	window.getSelection().removeAllRanges();	
}

function generateApiKey(frm){

	var description = frm.find("[name=description]").val().trim();
	if(!description){
		frm.find("[name=description]").focus();
	}
	var btn = frm.find("#generate-api-key");
	btn.val(i18n.generating);
	var data = jQuery("#generate-api-key-form").serialize(); //NO I18N
	jQuery.post(ajaxurl, data, function(response){
		div = jQuery("#api-key-div"); //NO I18N
		div.children("#api-key-span").text(response); //NO I18N
		div.show();
		jQuery("#generate-api-key-form").hide(); //NO I18N
		btn.val(i18n.generate);
	}).fail(function(response){
		span = jQuery("<span id='generate-api-key-error' style='color:red;vertical-align:middle;margin-left:10px;'></span>");
		btn.after(span);
		span.text(response.responseJSON.data);
		btn.val(i18n.generate);
	});
}

jQuery(document).ready(function(){

	jQuery("#copy-api-key").click(function(e){ //NO I18N
		copyApiKey();
	});

	jQuery("#open-api-key-generation-popup").click(function(e){ //NO I18N
		resetApiKeyForm();
		jQuery("#generate-api-key-form #api-key-description").keyup(function(e){ //NO I18N
			jQuery("#generate-api-key-error").remove(); //NO I18N
			var descInput = jQuery(e.srcElement);
			var btn = jQuery("#generate-api-key"); //NO I18N
			if(descInput.val() != ""){
				btn.attr("disabled", false); //NO I18N
			}
			else{
				btn.attr("disabled", true); //NO I18N
			}
		});		
	});

	jQuery("#ok-api-key-popup").click(function(e){ //NO I18N
		tb_remove();
	});

	jQuery("#generate-api-key").click(function(e){ //NO I18N
		e.preventDefault(); 
		frm = jQuery("#generate-api-key-form"); //NO I18N
		generateApiKey(frm);
	});

	jQuery("#generate-api-key-form").submit(function(e){ //NO I18N
		e.preventDefault(); 
		frm = jQuery(e.srcElement);
		generateApiKey(frm);		
	});


	jQuery('.delete-api-key').click(function(e){ //NO I18N
		e.preventDefault(); 
		var cfrm = confirm(i18n.remove_api_key_confirmation);
		if(!cfrm){
			return;
		}
		var data = jQuery("#remove-api-key-form").serialize(); //NO I18N
		var icon = this;
		var id = icon.id;
		if(id.startsWith("api-key")){
			api_key_id = id.replace('api-key-', '');
			data = data + "&api_key_id=" + api_key_id; //NO I18N
			jQuery.post(ajaxurl, data, function(response){
				jQuery(icon).parents("tr").remove(); //NO I18N
				jQuery("#notice").attr("class", "notice").addClass("notice-success").text(response).show(); //NO I18N
				setTimeout(function(){
					jQuery("#notice").fadeOut("slow").attr("class", "notice").text(""); //NO I18N
				}, 5000);
			}).fail(function(response){
				jQuery("#notice").attr("class", "notice").addClass("notice-error").text(response.responseJSON.data).show(); //NO I18N
				setTimeout(function(){
					jQuery("#notice").fadeOut("slow").attr("class", "notice").text(""); //NO I18N
				}, 5000);				
			});
		}
	});

	jQuery( 'a.zoho-flow-rating-link' ).click( function() { //NO I18N
		jQuery.post( ajaxurl, { action: 'zoho_flow_rated' } ); //NO I18N
		jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) ); //NO I18N
	});	

});