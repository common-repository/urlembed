(function() {
    tinymce.create("tinymce.plugins.urlembed_bb", {
        init : function(ed, url) {
            ed.addButton("urlembed_bb", {
                title : "UrlEmbed",
                cmd : "urlembed_bb",
                image : "https://urlembed.com/static/img/favicon.ico"
            });
            ed.addCommand("urlembed_bb", function() {
                var selected_text = ed.selection.getContent();
                if(selected_text.indexOf("[urlembed]") != 0){
                    selected_text = "[urlembed]" + selected_text + "[/urlembed]";
                }else{
                    selected_text = selected_text.replace("[urlembed]","");
                    selected_text = selected_text.replace("[/urlembed]","");
                }
                ed.execCommand("mceInsertContent", 0, selected_text);
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "UrlEmbed",
                author : "UrlEmbed",
                version : "1"
            };
        }
    });
    tinymce.PluginManager.add("urlembed_bb", tinymce.plugins.urlembed_bb);
})();