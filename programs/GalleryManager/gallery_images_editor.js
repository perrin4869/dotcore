
var GalleryImagesEditor = {
    OnGalleryImagesEditorLoad: function()
    {
        // Get all the reordering arrows, and add the click event to them
        var i;
        var upArrows = document.getElementsByName("up_arrow");
        var downArrows = document.getElementsByName("down_arrow");
        for(i = 0; i < upArrows.length; i++)
        {
            addEvent(upArrows[i], "click", GalleryImagesEditor.OnUpArrowClicked);
        }

        for(i = 0; i < downArrows.length; i++)
        {
            addEvent(downArrows[i], "click", GalleryImagesEditor.OnDownArrowClicked);
        }
    },

    OnUpArrowClicked: function()
    {
        var tr = GalleryImagesEditor.GetLinkRow(this);
        if(tr)
        {
            var extTr = new Ext.Element(tr);
            var swapTr = extTr.prev();
            if(swapTr)
            {
                GalleryImagesEditor.SwapImages(extTr, swapTr);
            }
        }
    },

    OnDownArrowClicked: function()
    {
        var tr = GalleryImagesEditor.GetLinkRow(this);
        if(tr)
        {
            var extTr = new Ext.Element(tr);
            var swapTr = extTr.next();
            if(swapTr)
            {
                GalleryImagesEditor.SwapImages(extTr, swapTr);
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

    SwapImages: function(srcTr, targetTr)
    {
        var galID1 = srcTr.getAttributeNS("dotcore", "img_id");
        var galID2 = targetTr.getAttributeNS("dotcore", "img_id");
        // Function is async
        GalleryImagesEditor.SwapOrder(galID1, galID2);
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

    SwapOrder: function(imgID1, imgID2)
    {
        Ext.Ajax.request({
            url: "/programs/GalleryManager/swap_order.php",
            params : {
                img1: imgID1,
                img2: imgID2
            },
            success: GalleryImagesEditor.OnSwapSuccess,
            failure: GalleryImagesEditor.OnSwapFailure
        });
    },

    OnSwapSuccess: function()
    {

    },

    OnSwapFailure: function()
    {
        Ext.alert("Swap failed");
    }
}

addEvent(window, "load", GalleryImagesEditor.OnGalleryImagesEditorLoad);