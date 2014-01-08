var PagesManager = {
    OnPagesManagerLoad: function()
    {
        // Hook an event to the appears in nav checkboxes
        var appearsInNavCheckboxes = document.getElementById("PagesTabs").getElementsByTagName("input");
        var i;
        for(i = 0; i < appearsInNavCheckboxes.length; i++)
        {
            if(appearsInNavCheckboxes[i].type == "checkbox")
            {
                addEvent(appearsInNavCheckboxes[i], "change", PagesManager.AppearsInNavClicked);
            }
            else if(appearsInNavCheckboxes[i].type == "radio")
            {
                addEvent(appearsInNavCheckboxes[i], "change", PagesManager.OnDefaultPageChanged);
            }
        }

        // basic tabs 1, built from existing content
        var tabs = new Ext.TabPanel({
            renderTo: "PagesTabs",
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
            addEvent(upArrows[i], "click", PagesManager.OnUpArrowClicked);
        }

        for(i = 0; i < downArrows.length; i++)
        {
            addEvent(downArrows[i], "click", PagesManager.OnDownArrowClicked);
        }
    },

    AppearsInNavClicked : function()
    {
        var extElem = new Ext.Element(this);
        var pageID = extElem.getAttributeNS("dotcore", "page_id");
        Ext.Ajax.request({

            url: "/programs/PagesManager/toggle_appears_in_nav.php",
            params : {
                page: pageID,
                appears_in_nav: (this.checked == true) ? "1" : "0"
            },
            success: PagesManager.OnAppearsInNavChangeSuccess,
            failure: PagesManager.OnAppearsInNavChangeFailure

        });
    },

    OnAppearsInNavChangeSuccess: function(objServerResponse)
    {
        // Nothing to do now
    },

    OnAppearsInNavChangeFailure: function(objServerResponse)
    {
        Ext.MessageBox.alert("Failure", objServerResponse.responseText);
    },

    OnDefaultPageChanged: function()
    {
        Ext.Ajax.request({
            url: "/programs/PagesManager/change_default.php",
            params : {
                page: this.value
            },
            success: PagesManager.OnDefaultPageChangedSuccessfully,
            failure: PagesManager.OnDefaultPageChangeFailure
        });
    },

    OnDefaultPageChangedSuccessfully: function(objServerResponse)
    {
        // Do nothing for now
    },

    OnDefaultPageChangeFailure: function(objServerResponse)
    {
        Ext.MessageBox.alert("Failure", objServerResponse.responseText);
    },

    OnUpArrowClicked: function()
    {
        var tr = PagesManager.GetPageRow(this);
        if(tr)
        {
            var extTr = new Ext.Element(tr);
            var pageGeneration = extTr.getAttributeNS("dotcore", "page_generation");

            // Find the previous pages with the same generation, if any
            var currentTr = extTr;
            while(currentTr = currentTr.prev())
            {
                var currentGen = currentTr.getAttributeNS("dotcore", "page_generation");
                if(currentGen == pageGeneration)
                {
                    // Exchange them
                    PagesManager.SwapPages(extTr, currentTr, currentGen);
                    break;
                }
            }

            // Run animation
            PagesManager.StartSwapEffect(extTr);
        }
    },

    OnDownArrowClicked: function()
    {
        var tr = PagesManager.GetPageRow(this);
        if(tr)
        {
            var extTr = new Ext.Element(tr);
            var pageGeneration = extTr.getAttributeNS("dotcore", "page_generation");

            // Find the previous pages with the same generation, if any
            var currentTr = extTr;
            while(currentTr = currentTr.next())
            {
                var currentGen = currentTr.getAttributeNS("dotcore", "page_generation");
                if(currentGen == pageGeneration)
                {
                    // Exchange them
                    PagesManager.SwapPages(extTr, currentTr, currentGen);
                    break;
                }

            }

            // Run animation
            PagesManager.StartSwapEffect(extTr);
        }
    },

    StartSwapEffect: function(extTr) {
        extTr.stopFx().highlight("ABDD70");
    },

    GetPageRow: function(td)
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

    SwapPages: function(srcTr, targetTr, generation)
    {
        var pageID1 = srcTr.getAttributeNS("dotcore", "page_id");
        var pageID2 = targetTr.getAttributeNS("dotcore", "page_id");
        // Function is async
        PagesManager.SwapOrder(pageID1, pageID2);

        //  We need a strategy to move the tr along with their tr "childs"
        // Ideally we'd have some kind of row grouping, that could be nested, but we don't
        // What we have is the page_generation property that I've added

        // We'll loop both trs, and make a list of tr's that need to be exchanged with each other

        var srcChilds = PagesManager.GetChildTrs(srcTr, generation);
        var targetChilds = PagesManager.GetChildTrs(targetTr, generation);

        // They are inserted in reverse order
        srcChilds.reverse();
        targetChilds.reverse();

        swapNode(srcTr.dom, targetTr.dom);
        srcTr.insertSibling(srcChilds, "after");
        targetTr.insertSibling(targetChilds, "after");

        var i = 0;
        var firstTr = srcTr.parent().first();
        do
        {
            if(i % 2 == 0)
            {
                firstTr.removeClass("alternating");
            }
            else
            {
                firstTr.addClass("alternating");
            }
            i++;
        }while(firstTr = firstTr.next())
    },

    GetChildTrs: function(rootTr, rootGeneration)
    {
        var result = new Array();
        var i = 0;
        var generation = null;
        while(rootTr = rootTr.next())
        {
            generation = rootTr.getAttributeNS("dotcore", "page_generation");
            if(generation > rootGeneration)
            {
                result[i] = rootTr;
                i++;
            }
            else{
                break;
            }
        }
        return result;
    },

    SwapOrder: function(pageID1, pageID2)
    {
        Ext.Ajax.request({
            url: "/programs/PagesManager/swap_order.php",
            params : {
                page1: pageID1,
                page2: pageID2
            },
            success: PagesManager.OnSwapSuccess,
            failure: PagesManager.OnSwapFailure
        });
    },

    OnSwapSuccess: function()
    {

    },

    OnSwapFailure: function()
    {
        alert("Swap failed");
    },

    OnPagesLanguageTabActivate: function(panel)
    {
        // Create a cookie that'll last one year
        createCookie(languageCookieName, panel.initialConfig.data.lang_id, 365, "/admin/");
    }
}

addEvent(window, "load", PagesManager.OnPagesManagerLoad);