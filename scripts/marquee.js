// Javascript marquee object by Julian Grinblat
// Converts any object into a marquee

var MarqueeDirection = {

    Top: 1,
    Bottom: 2,
    Left: 3,
    Right: 4

}

var MarqueeStartPosition = {

    Below: 'below',
    Filling: 'filling'

}

var Marquee = function(el, configuration)
{
    this.srcElement = el;
    // Holds the element relative to which the marquee content is placed
    this.wrapper = document.createElement("div");
    // The interval element that is used to scroll the marquee
    this.marqueeInterval = null;
    // The marquee content elements
    this.scrolling = [];
    // Holds the current position of the different scrolling items, gets initilized for each item when it is inserted
    this.currentPosition = [];
    // Holds the position from which the marquee begins moving, it is effectively parsed when the first item is inserted
    this.startPosition = 0;
    // Stores the configuration given to this object
    this.configuration = (configuration != null && configuration != undefined) ? configuration : {};

    var autoStart = true;
    var alwaysFull = false;

    var scrollElement = document.createElement("div");
    scrollElement.innerHTML = this.srcElement.innerHTML;

    // Offset width and height contains the paddings and the borders
    this.srcElement.style.overflow = "hidden"; // Make sure all the contents are taken into account when querying for the dimensions

    // Paddings
    var pTop, pBottom, pLeft, pRight;
    // Borders
    var bTop, bBottom, bLeft, bRight;

    pTop = parseInt(getStyle(this.srcElement, "padding-top")) || 0;
    pBottom = parseInt(getStyle(this.srcElement, "padding-bottom")) || 0;
    pLeft = parseInt(getStyle(this.srcElement, "padding-left")) || 0;
    pRight = parseInt(getStyle(this.srcElement, "padding-right")) || 0;

    bTop = parseInt(getStyle(this.srcElement, "border-top-width")) || 0; // Look out for NaN in some browsers
    bBottom = parseInt(getStyle(this.srcElement, "border-bottom-width")) || 0;
    bLeft = parseInt(getStyle(this.srcElement, "border-left-width")) || 0;
    bRight = parseInt(getStyle(this.srcElement, "border-right-width")) || 0;
    /*
    pTop = (this.srcElement.style.paddingTop != "") ? parseInt(this.srcElement.style.paddingTop) : 0;
    pBottom = (this.srcElement.style.paddingBottom != "") ? parseInt(this.srcElement.style.paddingBottom) : 0;
    pLeft = (this.srcElement.style.paddingLeft != "") ? parseInt(this.srcElement.style.paddingLeft) : 0;
    pRight = (this.srcElement.style.paddingRight != "") ? parseInt(this.srcElement.style.paddingRight) : 0;

    bTop = (this.srcElement.style.borderTopWidth != "") ? parseInt(this.srcElement.style.borderTopWidth) : 0;
    bBottom = (this.srcElement.style.borderBottomWidth != "") ? parseInt(this.srcElement.style.borderBottomWidth) : 0;
    bLeft = (this.srcElement.style.borderLeftWidth != "") ? parseInt(this.srcElement.style.borderLeftWidth) : 0;
    bRight = (this.srcElement.style.borderRightWidth != "") ? parseInt(this.srcElement.style.borderRightWidth) : 0;
    */

    var wrapperHeight = this.srcElement.offsetHeight - pTop - pBottom - bTop - bBottom;
    var wrapperWidth = this.srcElement.offsetWidth - pLeft - pRight - bLeft - bRight;
    this.wrapper.style.height = wrapperHeight + "px";
    this.wrapper.style.width = wrapperWidth + "px";

    // The dimensions are saved
    this.srcElement.innerHTML = "";

    this.wrapper.style.overflow = "hidden";
    this.wrapper.style.position = "relative";
    this.srcElement.appendChild(this.wrapper);

    // Parse the configuration given to this marquee

    if(this.configuration.speed != null && this.configuration.speed != undefined)
    {
        this.speed = this.configuration.speed;
    }
    else
    {
        this.speed = 1;
    }

    if(this.configuration.direction != null && this.configuration.direction != undefined)
    {
        this.direction = this.configuration.direction;
    }
    else
    {
        this.direction = MarqueeDirection.Top;
    }

    if(this.configuration.interval != null && this.configuration.interval != undefined)
    {
        this.interval = this.configuration.interval;
    }
    else
    {
        this.interval = 30;
    }

    if(this.configuration.addStoppingEventsOnMouseOver == undefined) {
        this.configuration.addStoppingEventsOnMouseOver = true;
    }

    if(this.configuration.alwaysFull != null && this.configuration.speed != alwaysFull)
    {
        alwaysFull = configuration.alwaysFull;
    }

    if(this.configuration.autoStart != null && this.configuration.speed != autoStart)
    {
        autoStart = configuration.autoStart;
    }

    // Add the first scrolling element
    this.AddScrollingElement(scrollElement); // Add the first scrolling element

    if(alwaysFull == true)
    {
        var countAdd;
        var i, contentLength, marqueeLength;
        if(this.IsVertical())
        {
            contentLength = this.scrolling[0].offsetHeight;
            marqueeLength = wrapperHeight;
        }
        else
        {
            contentLength = this.scrolling[0].offsetWidth;
            marqueeLength = wrapperWidth;
        }


        if(contentLength > 0) {
            countAdd = Math.floor(marqueeLength / contentLength);
            countAdd++;
        }
        else {
            countAdd = 0;
        }

        // We'll end up adding at least one more
        for(i = 0; i < countAdd; i++)
        {
            this.AddScrollingElement(scrollElement.cloneNode(true));
        }
    }

    // True by default, so if null it's valid
    if(this.configuration.addStoppingEventsOnMouseOver == true)
    addEvent(
         this.srcElement,
         "mouseover",
         createObjectCallback(this, this.OnMouseOver));

    addEvent(
        this.srcElement,
        "mouseout",
        createObjectCallback(this, this.OnMouseOut));

    if(autoStart)
    {
        this.StartScrolling();
    }
}

Marquee.prototype.AddScrollingElement = function(content)
{
    var tmp;
    if(typeof content == "string")
    {
        tmp = document.createElement("div");
        tmp.innerHTML = content;
    }
    else
    {
        tmp = content;
    }

    tmp.style.position = "absolute";

    if(this.IsHorizontal()) {
        tmp.style.whiteSpace = 'nowrap';
        tmp.style.width = "auto";
        tmp.style.height = this.wrapper.offsetHeight + "px";
    }
    else
    {
        tmp.style.height = "auto";
        tmp.style.width = this.wrapper.offsetWidth + "px";
    }

    this.scrolling.push(tmp);
    this.wrapper.appendChild(tmp);

    var currentScrollerPosition = this.scrolling.length - 1;
    // After inserting the first item we can effectively calculate the starting position
    if(this.scrolling.length == 1)
    {
        if(this.configuration.startPosition != null)
        {
            if(typeof(this.configuration.startPosition) == "string")
            {
                switch(this.configuration.startPosition)
                {
                    case MarqueeStartPosition.Below:
                        if(this.IsHorizontal()) {
                            this.startPosition = tmp.offsetWidth * -1;
                        }
                        else{
                            this.startPosition = tmp.offsetHeight * -1;
                        }
                        break;
                    case MarqueeStartPosition.Filling:
                        if(this.IsHorizontal()) {
                            this.startPosition = this.wrapper.offsetWidth - tmp.offsetWidth;
                        }
                        else{
                            this.startPosition = this.wrapper.offsetHeight - tmp.offsetHeight;
                        }
                        break;
                }
            }
            else
            {
                // Assume int
                this.startPosition = configuration.startPosition;
            }
        }
        else
        {
            // Default start position
            if(this.IsHorizontal()) {
                this.startPosition = tmp.offsetWidth * -1;
            }
            else{
                this.startPosition = tmp.offsetHeight * -1;
            }
        }

        this.SetMarqueePosition(currentScrollerPosition, this.startPosition);
    }
    else
    {
        var dimensions = (this.IsVertical()) ? tmp.offsetHeight : tmp.offsetWidth;
        // Set the next item under the last inserted item
        this.SetMarqueePosition(currentScrollerPosition, this.currentPosition[currentScrollerPosition - 1] - dimensions)
    }
}

// Accessors

Marquee.prototype.SetSpeed = function(speed)
{
    this.speed = speed;
}

Marquee.prototype.SetDirection = function(direction)
{
    this.direction = direction;
}

Marquee.prototype.SetInterval = function(interval)
{
    this.interval = interval;
}

// Methods

Marquee.prototype.IsScrolling = function()
{
    return this.marqueeInterval != null;
}

Marquee.prototype.StartScrolling = function()
{
    // Activate the interval
    if(this.marqueeInterval == null)
    {
        this.marqueeInterval = setInterval(createObjectCallback(this, this.MarqueeCallback), this.interval);
    }
}

Marquee.prototype.StopScrolling = function()
{
    // Deactivate the interval
    if(this.marqueeInterval != null)
    {
        clearInterval(this.marqueeInterval);
        this.marqueeInterval = null;
    }
}

Marquee.prototype.ResetPosition = function(scrollingIndex)
{
    if(this.scrolling.length == 1)
    {
        if(this.IsVertical())
        {
            this.SetMarqueePosition(scrollingIndex, this.scrolling[scrollingIndex].offsetHeight * -1);
        }
        else
        {
            this.SetMarqueePosition(scrollingIndex, this.scrolling[scrollingIndex].offsetWidth * -1);
        }
    }
    else
    {
        var underPosition;
        if(scrollingIndex == 0)
        {
            underPosition = this.scrolling.length - 1;
        }
        else
        {
            underPosition = scrollingIndex - 1;
        }

        var dimensions = (this.IsVertical()) ? this.scrolling[scrollingIndex].offsetHeight : this.scrolling[scrollingIndex].offsetWidth;
        // Set the next item under the last inserted item
        this.SetMarqueePosition(scrollingIndex, this.currentPosition[underPosition] - dimensions)
    }
}

Marquee.prototype.IsVertical = function()
{
    return (this.direction == MarqueeDirection.Bottom || this.direction == MarqueeDirection.Top);
}

Marquee.prototype.IsHorizontal = function()
{
    return !this.IsVertical();
}

Marquee.prototype.MarqueeCallback = function()
{
    var countScrollers = this.scrolling.length;
    var i;
    for(i = 0; i < countScrollers; i++)
    {
        this.currentPosition[i] += this.speed;
        if(this.IsVertical())
        {
            if(this.currentPosition[i] > this.wrapper.offsetHeight)
            {
                this.ResetPosition(i);
            }
            else
            {
                this.SetMarqueePosition(i, this.currentPosition[i]);
            }
        }
        else
        {
            if(this.currentPosition[i] > this.wrapper.offsetWidth)
            {
                this.ResetPosition(i);
            }
            else
            {
                this.SetMarqueePosition(i, this.currentPosition[i]);
            }
        }
    }
}

Marquee.prototype.SetMarqueePosition = function(scrollingPosition, value)
{
    this.currentPosition[scrollingPosition] = value;

    if(this.direction == MarqueeDirection.Top)
    {
        this.scrolling[scrollingPosition].style.bottom = value + "px";
    }
    else if(this.direction == MarqueeDirection.Bottom)
    {
        this.scrolling[scrollingPosition].style.top = value + "px";
    }
    else if(this.direction == MarqueeDirection.Left)
    {
        this.scrolling[scrollingPosition].style.right = value + "px";
    }
    else if(this.direction == MarqueeDirection.Right)
    {
        this.scrolling[scrollingPosition].style.left = value + "px";
    }
}

Marquee.prototype.OnMouseOver = function()
{
    this.StopScrolling();
}

Marquee.prototype.OnMouseOut = function(e)
{
    // Execute the mouse out ONLY if we're mousing out of the scrolEl
    // The way to check it is to read the toElement of the event, and check if it's a
    // child of scrollEl, by looping all the way up to the body
    // If it's not a child, great, if it is, then do not execute this event

    var target = e.relatedTarget;
    var currentTarget = e.currentTarget;
    while(target != null && target != currentTarget && target.nodeName != "BODY")
    {
        target = target.parentNode;
    }
    
    if(target == currentTarget) {
        return;
    }
    
    this.StartScrolling();
}