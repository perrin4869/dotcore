function OnNewsEditorLoad()
{
    // basic tabs 1, built from existing content
    var tabs = new Ext.TabPanel({
        renderTo: "news_panel",
        activeTab: activeTab,
        frame:true,
        defaults:{autoHeight: true},
        items: tabItems
    });
}

addEvent(window, "load", OnNewsEditorLoad);