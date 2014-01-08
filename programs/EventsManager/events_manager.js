
var EventsManager =
{
    OnEventsEditorLoad: function ()
    {
        // basic tabs 1, built from existing content
        var tabs = new Ext.TabPanel({
            renderTo: "events_panel",
            activeTab: activeTab,
            frame:true,
            defaults:{autoHeight: true},
            items: tabItems
        });
    },

    OnEventsLanguageTabActivate: function(panel)
    {
        // Create a cookie that'll last one year
        createCookie(languageCookieName, panel.initialConfig.data.lang_id, 365, "/admin/");
    }
}

addEvent(window, "load", EventsManager.OnEventsEditorLoad);