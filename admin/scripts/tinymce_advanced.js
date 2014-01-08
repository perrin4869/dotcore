var tinyMceAdvancedSettings = {

// General options
mode : "specific_textareas",
editor_selector : "advanced-rich-editor",
elements : "ajaxfilemanager",
theme : "advanced",
language : 'he',
directionality : "rtl",
plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,-dotcore_pages_linker,-dotcore_galleries_embedder",
// Theme options

// theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,ltr,rtl,|,styleselect,formatselect,fontselect,fontsizeselect",
// theme_advanced_buttons2 : "tablecontrols,|,cut,copy,paste,pastetext,pasteword,bullist,numlist,|,fullscreen,tinybrowser,|,undo,redo,|,link,unlink,image,cleanup,code,forecolor,backcolor",
// theme_advanced_buttons3 : "",
theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,docore_pages_linker_control,link,unlink,anchor,docore_galleries_embed_control,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,

content_css : "/templates/Default/styles/master.css",
document_base_url : "/",
body_class : "editor",
width: '800px',
height: '400px',
force_p_newlines : true,
theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",
convert_urls : false,
merge_styles_invalid_parents: null,
// Enable empty divs (by not putting the empty "-" in front of it)
extended_valid_elements:
  "div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|style|title]",

setup: function(editor)
{
    editor.onBeforeGetContent.add(tinyMceEvents.onBeforeGetContent);
    // editor.onGetContent.add(tinyMceEvents.onGetContent);
    // editor.onBeforeSetContent.add(tinyMceEvents.onBeforeSetContent);
    editor.onSetContent.add(tinyMceEvents.onSetContent);
},

file_browser_callback: "tinyupload", //Hookup tinyupload the the filebrowser call back.
// template_external_list_url : "js/template_list.js",
// external_link_list_url : "js/link_list.js",
// external_image_list_url : "js/image_list.js",
// media_external_list_url : "js/media_list.js",
relative_urls : false

};

tinyMCE.init(tinyMceAdvancedSettings);