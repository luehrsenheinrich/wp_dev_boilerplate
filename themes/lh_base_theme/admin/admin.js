jQuery(document).ready(function($){
	var content_tmpl_ctrl = new LHContentTemplateCtrl();
	jQuery(".ui-sortable").sortable();
	
	$(".lh_color_picker").wpColorPicker({
		palettes:  ['#F00', '#0F0', '#00F', '#aaa', '#ccc', '#eee', '#fff', '#000']
	});
});

var LHContentTemplateCtrl = function(){
	var $ = jQuery;

	this.init = function(){
		$("#content_template_select select.ct-select").change(function(){

			if($(this).val() != ""){

				var params = {
					action: "ct_info",
					ct: $(this).val(),
				}

				$.get(ajaxurl, params, function(res){
					console.log(res);
					if(res.success){
						$("#content_template_select .description .thumbnail").remove();

						if(res.data.thumbnail){
							var $img = $("<img>").attr("src", res.data.thumbnail);
							var $container = $("<div>").addClass("thumbnail").prepend($img);
							$("#content_template_select .description").prepend($container);
						}

						$("#content_template_select .desc-text").text(res.data.description);

					} else {
						$("#content_template_select .desc-text").text();
					}


				}, "json");

			} else {
				$("#content_template_select .description .thumbnail").remove();
				$("#content_template_select .desc-text").text("")
			}
		});
	}

	this.init();
}
