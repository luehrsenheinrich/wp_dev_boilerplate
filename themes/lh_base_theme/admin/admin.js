jQuery(document).ready(function($){
	var default_event_image_picker = new LHSettingsImagePicker();
	default_event_image_picker.initUploader();
	
	$(".lh_color_picker").wpColorPicker({
		palettes:  ['#1670B1', '#288DC8', '#d4dbE9', '#ff9e1b', '#474747', '#A5A5A5', '#CDCDCD', '#000', '#fff']
	});
});


var LHSettingsImagePicker = function(){

	this.initUploader = function(){
			jQuery('.appearance_page_lh_theme_settings .settings_image_picker').each(function(i){

				var $scope = jQuery(this);

				//uploading files variable
				var custom_file_frame;
				jQuery('.add_settings_image', $scope).click(function(event) {
					event.preventDefault();
					var frame_title = lh_var.choose_image;
					var frame_action = lh_var.select_image;

					//If the frame already exists, reopen it
					if (typeof(custom_file_frame)!=="undefined") {
						custom_file_frame.close();
					}

					//Create WP media frame.
					custom_file_frame = wp.media.frames.customHeader = wp.media({
					//Title of media manager frame
					title: frame_title,
					library: {
						type: 'image'
					},
					button: {
						//Button text
						text: frame_action
					},
					//Do not allow multiple files, if you want multiple, set true
					multiple: true
					});

					//callback for selected image
					custom_file_frame.on('select', function() {
						var selection = custom_file_frame.state().get('selection');
						selection.map(function(v){
							var image_url = v.attributes.sizes.thumbnail.url;
							var image_id = v.id;
							var img_code = '<img src="'+image_url+'" />'
							jQuery(".settings_image_id", $scope).val(v.id);
							jQuery(".settings_image_stage", $scope).html(img_code);
						});

					});

				//Open modal
				custom_file_frame.open();
			});

			jQuery(".remove_settings_image", $scope).click(function(){
				jQuery(".settings_image_id", $scope).val("");
				jQuery(".settings_image_stage", $scope).html("");
			});

		});
	}
};