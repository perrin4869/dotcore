
var LinksEditor = {
    OnLinksEditorLoad: function()
    {
        // basic tabs 1, built from existing content
        var tabs = new Ext.TabPanel({
            renderTo: "links_panel",
            activeTab: activeTab,
            frame:true,
            defaults:{autoHeight: true},
            items: tabItems
        });

        // Get all the reordering arrows, and add the click event to them
        var upArrows = document.getElementsByName("up_arrow");
        var downArrows = document.getElementsByName("down_arrow");
        for(i = 0; i < upArrows.length; i++)
        {
            addEvent(upArrows[i], "click", LinksEditor.OnUpArrowClicked);
        }

        for(i = 0; i < downArrows.length; i++)
        {
            addEvent(downArrows[i], "click", LinksEditor.OnDownArrowClicked);
        }
    },

    OnUpArrowClicked: function()
    {
        var tr = LinksEditor.GetLinkRow(this);
        if(tr)
        {
            var extTr = new Ext.Element(tr);
            var swapTr = extTr.prev();
            if(swapTr)
            {
                LinksEditor.SwapLinks(extTr, swapTr);
            }
        }
    },

    OnDownArrowClicked: function()
    {
        var tr = LinksEditor.GetLinkRow(this);
        if(tr)
        {
            var extTr = new Ext.Element(tr);
            var swapTr = extTr.next();
            if(swapTr)
            {
                LinksEditor.SwapLinks(extTr, swapTr);
            }
        }
    },

    GetLinkRow: function(td)
    {
        var tdParent = td;
        while(tdParent.offsetParent)
        {
            tdParent = tdParent.parentNode;
            if(tdParent.tagName == "TR")
            {
                return tdParent;
            }
        }

        return null;
    },

    SwapLinks: function(srcTr, targetTr)
    {
        var linkID1 = srcTr.getAttributeNS("dotcore", "link_id");
        var linkID2 = targetTr.getAttributeNS("dotcore", "link_id");
        // Function is async
        LinksEditor.SwapOrder(linkID1, linkID2);
        swapNode(srcTr.dom, targetTr.dom);

        if(srcTr.hasClass("alternating"))
        {
            srcTr.removeClass("alternating");
            targetTr.addClass("alternating");
        }
        else
        {
            srcTr.addClass("alternating");
            targetTr.removeClass("alternating");
        }
    },

    SwapOrder: function(linkID1, linkID2)
    {
        Ext.Ajax.request({
            url: "/programs/LinksEditor/swap_order.php",
            params : {
                link1: linkID1,
                link2: linkID2
            },
            success: LinksEditor.OnSwapSuccess,
            failure: LinksEditor.OnSwapFailure
        });
    },

    OnSwapSuccess: function()
    {

    },

    OnSwapFailure: function()
    {
        Ext.alert("Swap failed");
    },

    OnLinkLanguageTabActivate: function(panel)
    {
        // Create a cookie that'll last one year
        createCookie(languageCookieName, panel.initialConfig.data.lang_id, 365, "/admin/");
    }
}

addEvent(window, "load", LinksEditor.OnLinksEditorLoad);