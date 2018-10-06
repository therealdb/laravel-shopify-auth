function loadScript(url, callback){

    var script = document.createElement("script")
    script.type = "text/javascript";

    if (script.readyState){  //IE
        script.onreadystatechange = function(){
            if (script.readyState == "loaded" ||
                    script.readyState == "complete"){
                script.onreadystatechange = null;
                callback();
            }
        };
    } else {  //Others
        script.onload = function(){
            callback();
        };
    }

    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}

loadScript("https://cdn.shopify.com/s/assets/external/app.js", function(){
    if (typeof window.shopifyIsEmbedded != "undefined" && window.shopifyIsEmbedded) {
        ShopifyApp.init({
            apiKey: window.shopifyApiKey,
            shopOrigin: window.shopifyDomain
        });
    }
});