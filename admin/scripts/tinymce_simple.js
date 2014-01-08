var tinyMceSettings = {

// General options
mode : "specific_textareas",
editor_selector : "rich-editor",
elements : "ajaxfilemanager",
theme : "advanced",
language : 'he',
directionality : "rtl",
plugins : "-dotcore_pages_linker",

theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
theme_advanced_buttons2 : "bullist,numlist,|,undo,redo,|,docore_pages_linker_control,link,unlink,anchor,|,image,cleanup,help,code,|,forecolor,backcolor",
theme_advanced_buttons3 : "",

// Theme options
content_css : "/templates/Default/styles/master.css",
document_base_url : "/",
body_class : "editor",
width: '800px',
height: '200px',
force_p_newlines : true,
theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",
convert_urls : false,
merge_styles_invalid_parents: null,

setup: function(editor)
{
    editor.onBeforeGetContent.add(tinyMceEvents.onBeforeGetContent);
    editor.onSetContent.add(tinyMceEvents.onSetContent);
},

file_browser_callback: "tinyupload", //Hookup tinyupload the the filebrowser call back.
relative_urls : false

};

tinyMCE.init(tinyMceSettings);