function youtubegallery_pluginAppObj_3_17() {
    
    var pluginAppObj_3_17YouTubeGallery,
    divContainer = $("#youtubegallery_pluginAppObj_3_17"),
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
            
            $("#youtubegallery_pluginAppObj_3_17 div.swiper-slide-active").append(img);
            var widthImg = img.width();
            var heightImg = img.height();
            $("#youtubegallery_pluginAppObj_3_17 div.swiper-slide-active ." + tmpClass).remove();
            
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
                url: 'pluginAppObj/pluginAppObj_3_17/lib.php?action=getThumb',
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
        $('#youtubegallery_pluginAppObj_3_17 div.swiper-slide[data-video="'+id+'"]').css('background-image', 'url(' + _url + ')');
        $('#youtubegallery_pluginAppObj_3_17 div.swiper-slide[data-video="'+id+'"] > .imLoadAnim').remove();  
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
        
    		var pluginAppObj_3_17_resizeTo = null,
		pluginAppObj_3_17_firstTime = true,
		pluginAppObj_3_17_width = 0;
		x5engine.utils.onElementResize(document.getElementById('pluginAppObj_3_17'), function (rect, target) {
			if (pluginAppObj_3_17_width == rect.width) {
				return;
			}
			pluginAppObj_3_17_width = rect.width;
			if (!!pluginAppObj_3_17_resizeTo) {
				clearTimeout(pluginAppObj_3_17_resizeTo);
			}
			pluginAppObj_3_17_resizeTo = setTimeout(function() {
			if (!pluginAppObj_3_17_firstTime) {
				$('#pluginAppObj_3_17').css('overflow-x', 'hidden');
				load();
			}
		pluginAppObj_3_17_firstTime = false;
			}, 50);
		});
   
        
    function load() {
        resizeYouTubeGallery_pluginAppObj_3_17();
        pluginAppObj_3_17YouTubeGallery.update();
        pluginAppObj_3_17YouTubeGallery.slideTo(0, 1, null);
    }  
    
    function loadHtml() {
        if(!preview){      
            animation = x5engine.settings.imLoadingAnimation;

            lengthList = 25;
            var showValue = "mouse_over";

            var links = "https://youtube.com/playlist?list=OLAK5uy_lpaf8WCV4v2ozaRNiqQou6Ib_SW7mIQpc&si=rjdThN1uadLtR24A||https://youtube.com/playlist?list=OLAK5uy_kxjVzy_MDYxalflzpoI41b4mMq5AIZzWQ&si=kIqJ6Ft9G2-2oart||https://youtube.com/playlist?list=OLAK5uy_k1M4WVf9F2BxDwp_h3Cdo0xrLkf8aPQNY&si=yAvCLe1NHj8yF1_7||https://youtube.com/playlist?list=OLAK5uy_nUfNpSmOOJUkwUW-AES0RlUS27oVfU8o4&si=IryqqTW3BWK3JvTj||https://youtube.com/playlist?list=OLAK5uy_mABRbX_D2uofNRsEO8ACyVAIbYi3Aej0k&si=MvUXFtXSrl3t-LxV||https://youtube.com/playlist?list=OLAK5uy_nZ8llykn7wqPh9y3N7lm2Baa16ckoocP4&si=Ka4tRBX6lcUlLGmO||https://youtube.com/playlist?list=OLAK5uy_nsNT8Ntx3qqu4NZ3A50Yh70xiAuXzZS_A&si=j64edxl7HPNlBkNL||https://youtube.com/playlist?list=OLAK5uy_nK6PJ-WxUNlSuCgsyhJgwgaLDwl7JIX9k&si=TwAdzZhVTgOR6_Q_||https://youtube.com/playlist?list=OLAK5uy_ki-s6TKCDkAZjdjF2cpDcduro2_PyFBE4&si=eYTg1b22lrvM_L3m||https://youtube.com/playlist?list=OLAK5uy_k8_EFwSbSS8g4nrj6eostQ3BLVXLUT2PQ&si=vjRl3RTiMjPQ9nZV||https://youtube.com/playlist?list=OLAK5uy_kkiLbTWVZlx1flNmSVwds8Do7Tyetr2qI&si=pksT2Z3MlIbqP_tN||https://youtube.com/playlist?list=OLAK5uy_lQ1uK3W1lHcAB31nbBPimcl80RrTst9G4&si=Y2BRXu5-Ck1mupfN||https://youtube.com/playlist?list=OLAK5uy_mB_qSEIeQw6occyc1sAyVVN47Btiu9b_E&si=f3F-yYk821HUYX87||https://youtube.com/playlist?list=OLAK5uy_niUVGG4hB1karBrFlV-q4ByvovIZ84oLo&si=ZQ6-RUC05Yk_lFJ8||https://youtube.com/playlist?list=OLAK5uy_mTeVBpO-_gJIFwTg8S00fuKbAgJ_dpGOk&si=xC3f0Pn_gmlQmIMk||https://www.youtube.com/playlist?list=OLAK5uy_nSO67kPKG45JBJxiQLd9e7Rm2hzGy5hnM||https://youtube.com/playlist?list=OLAK5uy_mQA8Cenw5STbejL2yoQqdf3lWwkzou8q4&si=YkKiVzGSrLCFll-p||https://youtube.com/playlist?list=OLAK5uy_k67uimmL6U1bLC0AufOvkK_DuDqZ5BSUo&si=Mc09BD_mhQq-HdXz||https://youtube.com/playlist?list=OLAK5uy_kViCQ1Gtm_SnGXcgKSmIGvVwsa6pg6ZEU&si=cTpw8cODDoOXvdOQ||https://youtube.com/playlist?list=OLAK5uy_nYshWtYkqdw9ItyJ-tmsEsBk7Z1FqfJ1U&si=8-z-xFp7rfB_wCth||https://youtube.com/playlist?list=OLAK5uy_kW8yCj7Dyvpmc8XhhQEJq42FF27xJJX_4&si=7GHsbKGbjUUzZpqf||https://youtube.com/playlist?list=OLAK5uy_nOX-8Zg9wNk1mPCL9w_u2llntX_pBwacA&si=TJkHEERtDbO1F1YU||https://youtube.com/playlist?list=OLAK5uy_m8v7TGNt6d2g5Q9qDRt-oIytPvWUH5tng&si=ib8xAQ6ig_3PbTaa||https://youtube.com/playlist?list=OLAK5uy_nTB1F8U1BM2rLzNSUtJsEceTYBZJFS79w&si=0Ryx5UFGVSwL-39P||||";
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
            $("#youtubegallery_pluginAppObj_3_17 div.swiper-slide").append(animation);
        
            $('#youtubegallery_pluginAppObj_3_17 .swiper-slide').on("click", function () {
        
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
                    }, 0, $('#youtubegallery_pluginAppObj_3_17'));
                }    
            });
        }  
    }  
        
    loadHtml();   
    resizeYouTubeGallery_pluginAppObj_3_17();
    loadYouTubeGallery();

});

    function resizeYouTubeGallery_pluginAppObj_3_17(){  
        var container_width = $("#pluginAppObj_3_17").width();
        var heightUI = 360;
        var widthUI  = 1000;
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
            $("#youtubegallery_pluginAppObj_3_17").css({"width": max_width,"height": height + pagination_padding});
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
            $("#youtubegallery_pluginAppObj_3_17").css({"width": width,"height": height + pagination_padding});
        }
            
        $("#pluginAppObj_3_17 .swiper-container.main").css({"width": width,"height": height});
        $("#pluginAppObj_3_17 .swiper-button-next, #pluginAppObj_3_17 .swiper-button-prev").css({"top": height/2});    
            
    
        $("#pluginAppObj_3_17 .swiper-container.thumbs").css({"width": width});

        $("#youtubegallery_pluginAppObj_3_17").css({"width": max_width,"height": height + pagination_padding});
            
        if(1 > 1){
            var customHeight =  (height - 0)/1; 
            $("#youtubegallery_pluginAppObj_3_17 .swiper-container.main .swiper-slide").css("height", customHeight);
        }
        else{
            $("#youtubegallery_pluginAppObj_3_17 .swiper-container.main .slide-content").css("height", height);
        }
    }

    function loadYouTubeGallery() {
        pluginAppObj_3_17YouTubeGallery = new Swiper('#pluginAppObj_3_17 .swiper-container.main', {

            slidesPerView:    1,
            slidesPerColumn:  1,
            spaceBetween:     20,
            freeMode:         false,
            speed:            1000,
            roundLengths: true,
                    autoplay: 3000,
        autoplayDisableOnInteraction: false,
        nextButton: '#pluginAppObj_3_17 .swiper-button-next',
        prevButton: '#pluginAppObj_3_17 .swiper-button-prev',
        paginationClickable: true,
        pagination: '#pluginAppObj_3_17 .swiper-pagination',
        paginationType: 'bullets',
            controlBy:        'container'
        });
    }
}