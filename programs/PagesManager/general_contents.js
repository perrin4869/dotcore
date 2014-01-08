
var GeneralContentsEditor = {
    OnLoad: function()
    {
        // Load the tabs
        // basic tabs 1, built from existing content
        var tabs = new Ext.TabPanel({
            renderTo: "shared_contents_panel",
            activeTab: activeTab,
            frame: true,
            defaults: {autoHeight: true},
            items: tabItems
        });

        /*
        var sharedContentsForm = document.getElementsByName("shared_contents_form");
        var i, j;
        for(i = 0; i < sharedContentsForm.length; i++)
        {
            var textareas = sharedContentsForm[i].getElementsByTagName("textarea");
            for(j = 0; j < textareas.length; j++)
            {
                textareas[j].parentNode.style.display = "none";
            }

            var labels = sharedContentsForm[i].getElementsByTagName("label");
            for(j = 0; j < labels.length; j++)
            {
                addEvent(labels[j], "click", OnSharedContentLabelClicked);
            }
        }
        */
    },

    OnGeneralContentLabelClicked: function()
    {
        var inputDiv = this.parentNode;
        do{
            inputDiv = inputDiv.nextSibling;
        }while(inputDiv != null && inputDiv.tagName != "DIV");

        if(inputDiv != null)
        {
            if(inputDiv.style.display == "block")
            {
                inputDiv.style.display = "none";
            }
            else
            {
                inputDiv.style.display = "block";
            }
        }
    },

    GeneralContentLanguageTabActivate: function(panel)
    {
        // Create a cookie that'll last one year
        createCookie(languageCookieName, panel.initialConfig.data.lang_id, 365, "/admin/");

        tinyMCE.settings = GeneralContentsEditor.tinyMceGeneralContentsSettings;

        // OK, for some reason, panels that are not in focus when the page loads cannot load tinyMCE
        // So, load all the tinyMCEs now, that it is in focus
        var layoutTarget = panel.getLayoutTarget();
        var textareas = layoutTarget.query("textarea");
        var i;
        for (i = 0; i < textareas.length; i++)
        {
            if(Ext.get(textareas[i]).hasClass('rich-general-content') && !tinyMCE.get(textareas[i].id)) {
                tinyMCE.execCommand('mceAddControl', true, textareas[i].id);
            }
        }
    },

    tinyMceGeneralContentsSettings: tinyMceSettings
    // tinyMceGeneralContentsSettings.force_br_newlines = true;
    // tinyMceGeneralContentsSettings.forced_root_block = ''; // Needed for 3.x

}

addEvent(window, "load", GeneralContentsEditor.OnLoad);