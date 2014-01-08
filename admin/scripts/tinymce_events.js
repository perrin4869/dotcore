var tinyMceEvents = {

    // The before functions can be used to edit DOM only!!!!!!!!!!
    // The after functions can be used to edit CONTENT ONLY!
    // TOOK ME 3 FREAKING HOURS TO FIGURE OUT

    // TURNS OUT DOM CAN BE MANIPULATED IN THE AFTER VERSION OF SET. WHAT THE HELL, TINYMCE!???????????

    // Can't access content because it wasn't serialized from DOM yet'
    onBeforeGetContent: function(editor, o)
    {
        var hyperlinks = editor.dom.getRoot().getElementsByTagName('a');
        var i;
        var count_hyperlinks = hyperlinks.length;
        var hyperlink;
        for(i = 0; i < count_hyperlinks; i++)
        {
           hyperlink = hyperlinks[i];
           if(hyperlink.target == '_blank') {
              hyperlink.rel='external'; //-> set rel attribute with value external
           }

            hyperlink.target = '';  //-> remove attribute target
        }
    },

    // Used to change content after serializing, but that means the DOM can't be changed (can't change 2 things at once)
    onGetContent: function(editor, o)
    {
        o.content = o.content
            .replace(/<u>/g, '<span class="underline">')
            .replace(/<\/u>/g, '</span>')
            .replace(/<\s*(\w[\w\d]*)\b([^>]*)>((?:[^<]|<\s*(\w[\w\d]*)\b[^>]*>[\s\S]*?<\s*\/\s*\4\s*>|<[^>]+\/\s*>)*\{(?:feature|תוספת)\:[\d\w\u0590-\u05FF0_-]*\s*(?:\s*[\d\w\u0590-\u05FF0_-]+\=("|')[^"]+\5\s*)*\}(?:[^<]|<\s*(\w[\w\d]*)\b[^>]*>[\s\S]*?<\s*\/\s*\6\s*>|<[^>]+\/\s*>)*)<\s*\/\s*\1\s*>/g, "<div$2>$3</div>");
    },

    onBeforeSetContent: function(editor, o)
    {
        o.content = o.content.replace(/<span class="underline">(.*?)<\/span>/g, '<u>$1</u>');
    },

    onSetContent: function(editor, o)
    {
        var hyperlinks = editor.dom.getRoot().getElementsByTagName('a');
        var i;
        var count_hyperlinks = hyperlinks.length;
        var hyperlink;
        for(i = 0; i < count_hyperlinks; i++)
        {
            hyperlink = hyperlinks[i];
            if(hyperlink.rel=='external') {
                tinyMCE.activeEditor.dom.setAttrib(hyperlink, 'target', "_blank");
                tinyMCE.activeEditor.dom.setAttrib(hyperlink, 'rel', "");
            }
        }
    }
}