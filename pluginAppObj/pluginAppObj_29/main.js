function spotify_pluginAppObj_29(param) {
    var srcValue = "";
    var frameStart = "<iframe frameborder=\"0\" allowtransparency=\"true\" ";
    var frameEnd = "</iframe>";
    var linkInfo = "https://www.websitex5.com/obj_spotify";
    
    x5engine.boot.push(function(){
        $("#pluginAppObj_29_spotify").html("");
        if(param.type == "play-button"){
            initPlayButton();
        }
        else{
            initFollowButton();
        }    
    });

    function initPlayButton(){
        buildSrcPlayButton();
        if(srcValue == ""){
            return;
        }
        var tagIframe = frameStart;
        tagIframe += "allow=\"encrypted-media\" ";
        tagIframe += "width=\"" + param.width + "\" ";
        tagIframe += "height=\"" + param.height + "\" ";
        tagIframe += "src=\"" + srcValue + "\"";
        tagIframe += ">";
        tagIframe += frameEnd;
        
        $("#pluginAppObj_29_spotify").append(tagIframe);
    }

    function buildSrcPlayButton(){
        srcValue = "";
        if(!param.uri.indexOf("spotify:") == 0 && !param.uri.indexOf("https://open.spotify.com/") == 0) {
            addErr();
            return;
        }

        if(param.uri.indexOf("spotify:") == 0){
            try{
                //uri
                var uri = param.uri.split(":");
                srcValue = "https://open.spotify.com/embed/" + uri[1] + "/" + uri[2];
            }
            catch(err){
                addErr();
                return;
            }
        }
        else{
            try{
                //link                
                var ti = param.uri.indexOf("?");
                if(ti > - 1){
                    param.uri = param.uri.substring(0, ti);
                }
                srcValue = param.uri.replace("https://open.spotify.com/", "https://open.spotify.com/embed/"); 
            }
            catch(err){
                addErr();
            }
        }
    }

    function initFollowButton(){
        buildSrcFollowButton();
        if(srcValue == ""){
            addErr();
            return;
        }
        var tagIframe = frameStart;
        tagIframe += "scrolling=\"no\" style=\"border:none; overflow:hidden;\" ";
        tagIframe += "width=\"" + param.width + "\" ";
        tagIframe += "height=\"" + param.height + "\" ";
        tagIframe += "src=\"" + srcValue + "\"";
        tagIframe += ">";
        tagIframe += frameEnd;  
        $("#pluginAppObj_29_spotify").append(tagIframe);
    }

    function buildSrcFollowButton(){
        srcValue = "";
        var typeArtist = "";
        if(param.uri.indexOf("user") !== -1){
            typeArtist = "user";
        }
        else if(param.uri.indexOf("artist") !== -1){
            typeArtist = "artist";
        }
        else{
            //ko
            addErr();
            return;
        }
        
        if(!param.uri.indexOf("spotify:"+typeArtist+":") == 0 && !param.uri.indexOf("https://open.spotify.com/"+typeArtist+"/") == 0) {
            //ko
            addErr();
            return;
        }
        var sUri = param.uri;
        if(param.uri.indexOf("spotify:"+typeArtist+":") == 0){
            //uri
            var uri = param.uri.split(":");
            if(uri.length == 3){
                sUri = param.uri;
            }
            else{
                //ko
                addErr();
                return;
            }
        }
        else{
            //link
            try{
                var ti = param.uri.indexOf("?");
                if(ti > - 1){
                    param.uri = param.uri.substring(0, ti);
                }
                
                sUri = "spotify:"+typeArtist+":" + param.uri.replace("https://open.spotify.com/"+typeArtist+"/","");
            }
            catch(e){
                //ko
                addErr();
                return;  
            }
                
        }
        srcValue = "https://open.spotify.com/follow/1/?uri=" + sUri;
        srcValue += "&size=" + param.styleButton;
        srcValue += "&theme=" + param.field_style_follow_background;
        if(param.followerCount == false){
            srcValue += "&show-count=0";
        }
    }

    function addErr(){
        srcValue = "";
        $("#pluginAppObj_29_spotify").empty();
        if(!param.preview){
            return;
        }
 
        var img = "<img src='data:image/gif;base64,R0lGODlhEAAQAPcAAAAAAFBQUFVUUVpYU5qIZqOQaKiTaraebryjcL6kcM2wdevGfe3Ifu3If76+vu3Kg+3Lhu7Ni+3Nje7Rl+7Unu7Wpe/Xqe/bte/euu/hxO/ixu/kzfDo2O7r5+3t7fDs5PHv6/Hv7PDv7fHx8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAP8ALAAAAAAQABAAAAh9AP8JHEiwoMF/IDJ0OFjQAwUGFBgWZEBxg8R/IiRQZCDhIgYGBQIUYICB4YcHIEUyeMDQAkUDAwxQtFDQAYcGFEOOZNCAQ8EJGxMEULBxwoiBGjYqVapBYIgISg8IQKA0Qoh/F5YSUKn0wj+USxcsXfmvAoSzaNOirXDxYEAAOw=='/>"; 
        var message = "Add the link or the URI making sure you comply with all Spotify requirements.";
        var moreInfo = "More information about"; 
        $("#pluginAppObj_29_spotify").append("<div class=\"error_post_container\"><div class=\"error_post_thumb\">"+img+"</div><div class=\"p_error_ui\">"+message+"<br><br><p>"+moreInfo+" <a href='"+linkInfo+"' target='_blank'>https://www.websitex5.com/obj_spotify</a>.</p></div></div>");
    }
}