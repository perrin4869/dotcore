/**
 * Class used to run the navigation of the site
 * @param UnorderedList list
 * @return void
 */
var Navigation = function(list)
{
    /**
     * Unordered list, holds the root of the nav
     */
    this.list = list;
    this.timeout = 700;
    this.closetimer	= []; // Stores the timers for closing navs
    this.status = []; // Stores the status of the items in the navigation
    this.widths = [];
    this.heights = [];
    this.pixelsPerMilliSecond = 0.35;

    /**
     * Holds an array describing all the opened lists
     */
    this.currentSubNavPath = [];

    var currItem, itemID;
    var i;
    for(i = 0; i < this.list.childNodes.length; i++)
    {
        // Run all the li, and their first child (a)
        currItem = this.list.childNodes[i];
        if(currItem.tagName == "LI")
        {
            addEvent(
                currItem,
                "mouseover",
                createObjectCallback(this, this.RootItemMouseOver, [currItem]));
            addEvent(
                currItem,
                "mouseout",
                createObjectCallback(this, this.RootItemMouseOut, [currItem]));

            // Add a unique id for the items
            itemID = i.toString();
            currItem.setAttribute("nav_id", itemID);

            this.AddEventsToItemsRecursively(currItem, itemID);
        }
    }
};

Navigation.ITEM_STATUS_CLOSED = 1;
Navigation.ITEM_STATUS_OPENING = 2;
Navigation.ITEM_STATUS_OPENED = 3;
Navigation.ITEM_STATUS_CLOSING = 4;

//
// Navigation Methods
//

Navigation.prototype.AddEventsToItemsRecursively = function(item, itemID)
{
    var subNav = this.GetSubNav(item);
    if(subNav)
    {
        // No need to attach the event on the nav - it is propagated into the li element under it
        // addEvent(subNav, "mouseover", createObjectCallback(this, this.CancelClosingTimer, [item]));
        // addEvent(subNav, "mouseout", createObjectCallback(this, this.StartClosingTimer, [item]));

        subNav.style.visibility = "hidden";
        subNav.style.display = "block";
        subNav.style.height = "auto"; // Make sure all the subnav is taken into account

        this.widths[itemID] = subNav.offsetWidth;
        this.heights[itemID] = subNav.offsetHeight;

        subNav.style.visibility = "visible";
        subNav.style.display = "none";
        subNav.style.height = "0px";

        var i;
        var currItem;
	for(i = 0; i < subNav.childNodes.length; i++)
	{
            // Run all the li, and their first child (a)
            currItem = subNav.childNodes[i];
            if(currItem.tagName == "LI")
            {
                addEvent(currItem, "mouseover", createObjectCallback(this, this.SubNavItemMouseOver, [currItem]));
                addEvent(currItem, "mouseout", createObjectCallback(this, this.SubNavItemMouseOut, [currItem]));

                this.AddEventsToItemsRecursively(currItem);
            }
	}

    }
}

Navigation.prototype.GetStatus = function(itemID)
{
    if(this.status[itemID] == null)
    {
        this.status[itemID] = Navigation.ITEM_STATUS_CLOSED;
    }
    return this.status[itemID];
}

//open hidden layer
Navigation.prototype.OpenSubNav = function(item)
{
    var subNav, itemID, status;
    subNav = this.GetSubNav(item);
    itemID = item.getAttribute("nav_id");
    status = this.GetStatus(itemID);
    if(
        subNav && (
            status == Navigation.ITEM_STATUS_CLOSING ||
            status == Navigation.ITEM_STATUS_CLOSED))
    {
        this.status[itemID] = Navigation.ITEM_STATUS_OPENING;

        // Find the number of pixels left to animate
        // The time equals the pixels per millisecond (0.35 by default)

        subNav.style.display = "block";

        jQuery(subNav) // .slideDown(500);
            .stop()
            .animate(
                {height: this.heights[itemID] + "px"},
                Math.ceil((this.heights[itemID] - parseInt(subNav.style.height)) / this.pixelsPerMilliSecond),
                "linear",
                createObjectCallback(this, this.SubMenuOpened, [item])
            );

    }
};

Navigation.prototype.SubMenuOpened = function(item)
{
    if(item)
    {
        var itemID = item.getAttribute("nav_id");
        this.status[itemID] = Navigation.ITEM_STATUS_OPENED;
    }
}

// close showed layer
Navigation.prototype.CloseSubMenu = function(item)
{
    var subNav = this.GetSubNav(item);
    // Animate the closing only if it's open
    if(subNav && subNav.style.display == "block")
    {
        var itemID = item.getAttribute("nav_id");
        this.status[itemID] = Navigation.ITEM_STATUS_CLOSING;

        // Find the number of pixels left to animate
        // The time equals the pixels per millisecond (0.35 by default)
        jQuery(subNav) // .slideUp(500);
            .stop()
            .animate(
                {height: "0px"},
                Math.ceil(parseInt(subNav.style.height) / this.pixelsPerMilliSecond),
                "linear",
                createObjectCallback(this, this.SubMenuClosed, [item])
            );
    }
};

Navigation.prototype.SubMenuClosed = function(item)
{
    var subNav = this.GetSubNav(item);
    if(subNav)
    {
        this.UnmakeCurrentItem(item);
        subNav.style.display = "none";
        this.status[item.getAttribute("nav_id")] = Navigation.ITEM_STATUS_CLOSED;
    }
}

// go close timer
Navigation.prototype.StartClosingTimer = function(item)
{
    this.CancelClosingTimer(item); // Don't initiate 2 timers for the same item
    var itemID = item.getAttribute("nav_id");
    this.closetimer[itemID] = window.setTimeout(createObjectCallback(this, this.ClosingTimerTick, [item]), this.timeout);
};

// cancel close timer
Navigation.prototype.CancelClosingTimer = function(item)
{
    var itemID = item.getAttribute("nav_id");
    if(this.closetimer[itemID] != null)
    {
        window.clearTimeout(this.closetimer[itemID]);
        this.closetimer[itemID] = null;
    }
};

Navigation.prototype.ClosingTimerTick = function(e, item)
{
    // Some browsers send an event, some don't
    // If item is set, we know the event was sent, so send item, else send event
    if(item == null)
    {
        item = e;
    }
    this.CloseSubMenu(item);
}

Navigation.prototype.GetSubNav = function(item)
{
    return item.getElementsByTagName("ul")[0];
}

Navigation.prototype.GetCurrentItem = function()
{
    return this.currentSubNavPath[0];
}

Navigation.prototype.MakeCurrentItem = function(item)
{
    this.currentSubNavPath[0] = item;
    jQuery(item).addClass("active");
}

Navigation.prototype.UnmakeCurrentItem = function(item)
{
    // Make sure we're popping the current item ONLY if item IS the current item (it could have stopped being because of another reason)
    if(this.GetCurrentItem() == item)
    {
        this.currentSubNavPath.pop();
    }

    jQuery(item).removeClass("active");
}


//
// Events Handlers
//

Navigation.prototype.RootItemMouseOver = function(e, item)
{
    this.CancelClosingTimer(item); // If there was a timer set to close this item's subnav, cancel it

    var currentListItem = this.GetCurrentItem();
    if(item != currentListItem)
    {
        this.MakeCurrentItem(item);
    }

    this.OpenSubNav(item);
}

Navigation.prototype.RootItemMouseOut = function(e, item)
{
    // Check that we're really leaving item
    var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
    if(reltg)
    {
        while (reltg != item && reltg.nodeName != 'BODY')
        {
            reltg = reltg.parentNode;
        }
        if (reltg == item) return;

        // Make sure we're no using a stray pointer, even though theoretically there's no reason for it to be stray here
        var subNav = this.GetSubNav(item);
        if(subNav)
        {
            this.StartClosingTimer(item);
        }
        else
        {
            this.UnmakeCurrentItem(item);
        }
    }
}

Navigation.prototype.SubNavItemMouseOver = function(e, item)
{
    // e.stopPropagation();
    jQuery(item).addClass("active");
}

Navigation.prototype.SubNavItemMouseOut = function(e, item)
{
    // e.stopPropagation();
    jQuery(item).removeClass("active");
}