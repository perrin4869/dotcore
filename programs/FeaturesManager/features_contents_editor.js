
var FeaturesContentsEditor = {
    OnLoad: function()
    {
        // Load the tabs
        // basic tabs 1, built from existing content
        var tabs = new Ext.TabPanel({
            renderTo: "messages-editor-wrapper",
            activeTab: 0,
            frame: true,
            defaults: {autoHeight: true},
            items: tabItems
        });
    }

}

addEvent(window, "load", FeaturesContentsEditor.OnLoad);