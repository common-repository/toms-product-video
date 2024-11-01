jQuery(function($){
    $("#toms_wvp_video_upload_button").on("click", function(){
        var video_url = wp.media({
            //title: "Choose a Video",
            frame: "select",
            multiple: false,
            library: {
                type: [ 'video' ]
            }
        })
        video_url.open().on("select", function(e){
            var uploadVideo = video_url.state().get("selection").first()
            var selectedVideo = uploadVideo.toJSON()
            $("#toms_wvp_video_url").val(selectedVideo.url)
        }) 
    });
});
jQuery(function($){
    $("#toms_wvp_video_poster_image_upload_button").on("click", function(){
        var video_poster_image_url = wp.media({
            //title: "Choose a Image",
            frame: "select",
            multiple: false,
            library: {
                type: [ 'image' ]
            }
        })
        video_poster_image_url.open().on("select", function(e){
            var uploadVideoPosterImg = video_poster_image_url.state().get("selection").first()
            var selectedVideoPosterImg = uploadVideoPosterImg.toJSON()
            $("#toms_wvp_video_poster_image_url").val(selectedVideoPosterImg.url)
        }) 
    });
});

function updatePadding(name, val) {
    document.getElementById(name).setAttribute('value',val);
}
function getPadding(name, get_val) {
    var get_value = document.getElementById(get_val).value
    document.getElementById(name).setAttribute('value',get_value);
}

var $selected_position_value = document.getElementById('toms_wvp_position').value;
if( $selected_position_value == 1 ){
    var tomsPaddingContainerInit = document.getElementById('toms-wvp-padding-container')
    tomsPaddingContainerInit.style.display = "none"
    tomsPaddingContainerInit.style.opacity = 0
}
function disabledUnsupportFunction( select ){
    var currentSelected = select.selectedIndex;
    var tomsPaddingContainer  = document.getElementById('toms-wvp-padding-container')

    switch ( currentSelected ) {
        case 1:
            tomsPaddingContainer.style.display = "none"
            tomsPaddingContainer.style.opacity = 0
        break;

        default:
            tomsPaddingContainer.style.display = "flex"
            tomsPaddingContainer.style.opacity = 1
            break;
    }
}

function updatePaddingMaxValue(input_range, input_value, select){
    var currentSelected = select.selectedIndex;
    var tomsInputRange  = document.getElementById(input_range);
    var tomsInputValue  = document.getElementById(input_value);

    var tomsInputValueVal = document.getElementById(input_value).value;

    switch ( currentSelected ) {
        case 0:
            tomsInputRange.setAttribute('max', 100)
            break;
        case 1:
            tomsInputRange.setAttribute('max', 25)
            if( tomsInputValueVal > 25 ){
                tomsInputValue.setAttribute('value', 25)
            }
        break;
        case 2:
            tomsInputRange.setAttribute('max', 25)
            if( tomsInputValueVal > 25 ){
                tomsInputValue.setAttribute('value', 25)
            }
        break;
        case 3:
            tomsInputRange.setAttribute('max', 35)
            if( tomsInputValueVal > 35 ){
                tomsInputValue.setAttribute('value', 35)
            }
        break;

        default:
            tomsInputRange.setAttribute('max', 100)
            break;
    }
}