function OnContactUsRecipientEditorLoad()
{
    // basic tabs 1, built from existing content
    var tabs = new Ext.TabPanel({
        renderTo: "contact_us_recipient_panel",
        activeTab: activeTab,
        frame:true,
        defaults:{autoHeight: true},
        items: tabItems
    });
}

addEvent(window, "load", OnContactUsRecipientEditorLoad);