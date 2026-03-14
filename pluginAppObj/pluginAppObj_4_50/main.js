function youtubegallery_pluginAppObj_4_50() {
    
    var pluginAppObj_4_50YouTubeGallery,
    divContainer = $("#youtubegallery_pluginAppObj_4_50"),
    preview,
    lengthList = 0,
    animation = "",
    fullWidth = false,
    resizeTimer, // Set resizeTimer to empty so it resets on page load
    res = ["/maxresdefault.jpg", "/hqdefault.jpg", "/mqdefault.jpg", "/sddefault.jpg"]; 
    
    function getImagePreview(url, id, i) {
        callLinkResolution(url, id, res[i], i);
    }
    
    function callLinkResolution(url, id, resI, i) {
        var _url = url + id + resI; 
        var tmpClass = id + "_" + i + "_tmp";
        var img = $("<img class='" + tmpClass + "' style='visibility: hidden; opacity: 0'/>");
        
        img.one("load", function () {
            
            $("#youtubegallery_pluginAppObj_4_50 div.swiper-slide-active").append(img);
            var widthImg = img.width();
            var heightImg = img.height();
            $("#youtubegallery_pluginAppObj_4_50 div.swiper-slide-active ." + tmpClass).remove();
            
            if( widthImg == 120 && heightImg == 90){
                if(i < 3){
                    getImagePreview(url, id, i + 1);
                }
                else{
                    setBackgroundImage("https://img.youtube.com/vi/", id); 
                }
            }
            else{
                setBackgroundImage(_url,id);
            }
            
        });

        img.attr("src", _url);   
    }
    
    function getImageOnLine(id){
        
            $.ajax({
                url: 'pluginAppObj/pluginAppObj_4_50/lib.php?action=getThumb',
                type: 'POST',
                data: { 
                    'id': id,  
                },
                dataType: "json"
            })
            .fail(function () {
                setBackgroundImage("https://img.youtube.com/vi/", id); 
            })
            .done(function (data) {
                if (data == "") {
                    setBackgroundImage("https://img.youtube.com/vi/", id); 
                } else {
                    setBackgroundImage(data,id); 
                }
            });
    }
    
    function setBackgroundImage(_url,id){   
        $('#youtubegallery_pluginAppObj_4_50 div.swiper-slide[data-video="'+id+'"]').css('background-image', 'url(' + _url + ')');
        $('#youtubegallery_pluginAppObj_4_50 div.swiper-slide[data-video="'+id+'"] > .imLoadAnim').remove();  
    }
    
    function youTubeParser(url) {
        var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
        var match = url.match(regExp);
        return (match && match[7].length == 11) ? match[7] : false;
    }

    function getThumbUrlYouTube(url) {

        var thumb = "https://img.youtube.com/vi/";
        var id = youTubeParser(url);
        
        if("online" == "online" ){
           return getImageOnLine(id);
        }
        else{
            return getImagePreview(thumb, id,  0); 
        }
    }
    
    x5engine.boot.push(function(){ preview = false;
        
    		var pluginAppObj_4_50_resizeTo = null,
		pluginAppObj_4_50_firstTime = true,
		pluginAppObj_4_50_width = 0;
		x5engine.utils.onElementResize(document.getElementById('pluginAppObj_4_50'), function (rect, target) {
			if (pluginAppObj_4_50_width == rect.width) {
				return;
			}
			pluginAppObj_4_50_width = rect.width;
			if (!!pluginAppObj_4_50_resizeTo) {
				clearTimeout(pluginAppObj_4_50_resizeTo);
			}
			pluginAppObj_4_50_resizeTo = setTimeout(function() {
			if (!pluginAppObj_4_50_firstTime) {
				$('#pluginAppObj_4_50').css('overflow-x', 'hidden');
				load();
			}
		pluginAppObj_4_50_firstTime = false;
			}, 50);
		});
   
        
    function load() {
        resizeYouTubeGallery_pluginAppObj_4_50();
        pluginAppObj_4_50YouTubeGallery.update();
        pluginAppObj_4_50YouTubeGallery.slideTo(0, 1, null);
    }  
    
    function loadHtml() {
        if(!preview){      
            animation = x5engine.settings.imLoadingAnimation;

            lengthList = 1;
            var showValue = "mouse_over";

            var links = "https://www.youtube.com/watch?v=2P3nq8NPKeI||";
            var arrLinks = links.substring(0,links.length - 2).split("||");

            html = "<div class='swiper-container main'>";

            html += "<div class='swiper-wrapper'>";
            for (var i = 0; i < lengthList; i++){

                getThumbUrlYouTube(arrLinks[i]);

                var icon_play = "";
                if(showValue != "never"){
                    icon_play = "<yt-icon id=\"play\" icon=\"play_all\" class=\"fade-in\"><svg viewBox=\"0 0 24 24\" preserveAspectRatio=\"xMidYMid meet\" focusable=\"false\" class=\"yt-icon\"><g class=\"yt-icon\"><path d=\"M8 5v14l11-7z\" class=\"yt-icon\"></path></g></svg></yt-icon>";
                }

                var slide = "<div class='swiper-slide' data-video='" + youTubeParser(arrLinks[i]) + "'>\n";
                slide += icon_play+"\n";
                slide += "</div>\n";

                html += slide;
            }
            html += "</div>"; //Closing swiper wrapper

            if("inside" == "inside" ){
                html += "<div class='swiper-button-prev'></div>";
                html += "<div class='swiper-button-next'></div>";

                if("bullets" != "none" ){
                    html += "<div class='swiper-pagination'></div>";
                }
            }

            if(false){      
                html += "<div class='swiper-scrollbar'></div>";
            }

            html += "</div>"; //Closing swiper container

            if("inside" == "outside" ){
                html += "<div class='swiper-button-prev'></div>";
                html += "<div class='swiper-button-next'></div>";

                if("bullets" != "none" ){
                    html += "<div class='swiper-pagination'></div>";
                }
            }

            $(divContainer).append(html);
            $("#youtubegallery_pluginAppObj_4_50 div.swiper-slide").append(animation);
        
            $('#youtubegallery_pluginAppObj_4_50 .swiper-slide').on("click", function () {
        
                var url = "https://www.youtube.com/watch?v=" + $(this).attr('data-video');

                if(false) {
                    var win = window.open(url, '_blank');
                }
                else {    
                    x5engine.imShowBox({
                        media: [
                            {
                                url: url,
                                width: 1280,
                                height: 720,
                                type: "youtube"
                            }
                        ]
                    }, 0, $('#youtubegallery_pluginAppObj_4_50'));
                }    
            });
        }  
    }  
        
    loadHtml();   
    resizeYouTubeGallery_pluginAppObj_4_50();
    loadYouTubeGallery();

});

    function resizeYouTubeGallery_pluginAppObj_4_50(){  
        var container_width = $("#pluginAppObj_4_50").width();
        var heightUI = 720;
        var widthUI  = 1280;
        var height = heightUI;
        var width = widthUI;
        var max_width = container_width;
        var controls_padding = 0
        var pagination_padding = 0 
            
        if (!fullWidth || false) {
            //obj in the bp ceil
            max_width = (container_width < width ? container_width : width);
            height = ((max_width - controls_padding) / width) * height;
            
            width = max_width - controls_padding;
            $("#youtubegallery_pluginAppObj_4_50").css({"width": max_width,"height": height + pagination_padding});
        }
        else {
            //obj fullwidth
            if (max_width > widthUI) {
                height = heightUI;
            }
            else {
                height = ((max_width - controls_padding) / widthUI) * height;
            }
            
            width = container_width - controls_padding;
            $("#youtubegallery_pluginAppObj_4_50").css({"width": width,"height": height + pagination_padding});
        }
            
        $("#pluginAppObj_4_50 .swiper-container.main").css({"width": width,"height": height});
        $("#pluginAppObj_4_50 .swiper-button-next, #pluginAppObj_4_50 .swiper-button-prev").css({"top": height/2});    
            
    
        $("#pluginAppObj_4_50 .swiper-container.thumbs").css({"width": width});

        $("#youtubegallery_pluginAppObj_4_50").css({"width": max_width,"height": height + pagination_padding});
            
        if(1 > 1){
            var customHeight =  (height - 0)/1; 
            $("#youtubegallery_pluginAppObj_4_50 .swiper-container.main .swiper-slide").css("height", customHeight);
        }
        else{
            $("#youtubegallery_pluginAppObj_4_50 .swiper-container.main .slide-content").css("height", height);
        }
    }

    function loadYouTubeGallery() {
        pluginAppObj_4_50YouTubeGallery = new Swiper('#pluginAppObj_4_50 .swiper-container.main', {

            slidesPerView:    1,
            slidesPerColumn:  1,
            spaceBetween:     5,
            freeMode:         false,
            speed:            1000,
            roundLengths: true,
                    autoplay: 3000,
        autoplayDisableOnInteraction: false,
        nextButton: '#pluginAppObj_4_50 .swiper-button-next',
        prevButton: '#pluginAppObj_4_50 .swiper-button-prev',
        paginationClickable: true,
        pagination: '#pluginAppObj_4_50 .swiper-pagination',
        paginationType: 'bullets',
            controlBy:        'container'
        });
    }
}