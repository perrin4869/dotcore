// Div Scrolling Javascript helper object by Julian Grinblat
// Provides functions to start scrolling, stop scrolling, and scroll by a few pixels

var ScrollTypes = {

	Top: 1,
	Bottom: 2,
	Left: 3,
	Right: 4

}

var DivScroller = function(div)
{
	this.div = div;
	this.deltaScroll = 1;
	this.intervalBetweenMovements = 35;
	this.scrolling = false;
	this.scrollingType = null;

	this.interval = null;
	
	this.IsScrolling = function()
	{
		return this.interval != null;
	}
	
	this.StartScrolling = function(type)
	{
		if(type != null) {
			this.scrollingType = type;
		}

		if(this.interval == null) {
			this.interval = setInterval(createObjectCallback(this, this.ScrollCallback), this.intervalBetweenMovements);
		}
	}
	
	this.StopScrolling = function()
	{
		if(this.interval != null)
		{
			clearInterval(this.interval);
			this.interval = null;
		}
	}
	
	this.ScrollCallback = function(e)
	{
		// Some browsers pass an event object, other browsers do not.
		// If type is not defined, that means the event object was not passed, and the first argument is the type
		this.ScrollBy(this.deltaScroll);
	}
	
	this.ScrollBy = function(delta)
	{
		if(this.scrollingType == ScrollTypes.Left)
		{
			if(this.div.scrollLeft + delta < this.div.scrollWidth)
			{
				this.div.scrollLeft = this.div.scrollLeft + delta;
			}
			else
			{
				this.div.scrollLeft = this.div.scrollWidth;
			}
		}
		
		if(this.scrollingType == ScrollTypes.Right)
		{
			if(this.div.scrollLeft - delta >= 0)
			{
				this.div.scrollLeft = this.div.scrollLeft - delta;
			}
			else
			{
				this.div.scrollLeft = 0;
			}
		}
		
		if(this.scrollingType == ScrollTypes.Top)
		{
			if(this.div.scrollTop - delta >= 0)
			{
				this.div.scrollTop -= delta;
			}
			else
			{
				this.div.scrollTop = 0;
			}
		}
		
		if(this.scrollingType == ScrollTypes.Bottom)
		{
			if(this.div.scrollTop + delta < this.div.scrollHeight)
			{
				this.div.scrollTop += delta;
			}
			else
			{
				this.div.scrollTop = this.div.scrollHeight;
			}
		}
	}
}