// Javascript loading ellipsis object by Julian Grinblat
// Used to animate an ellipsis on loading messages

var LoadingEllipsis = function(object, config)
{
    if(config == null)
    {
        config = {};
    }
    this.currentPoints = config.currentPoints || 0;
    this.maxPoints = config.maxPoints || 3;
    this.object = object;
    this.intervalBetweenAdditions = config.intervalBetweenAdditions || 350;
    
    this.interval = null;
    this.Start();
}

LoadingEllipsis.prototype.AddDot = function()
{
    if(this.currentPoints < this.maxPoints)
    {
        this.object.innerHTML += ".";
        this.currentPoints++;
    }
    else
    {
        this.object.innerHTML = this.object.innerHTML.substr(0, this.object.innerHTML.length - this.maxPoints);
        this.currentPoints = 0;
    }
}

LoadingEllipsis.prototype.Start = function()
{
    if(this.interval == null)
    {
        this.interval = setInterval(createObjectCallback(this, this.AddDot), this.intervalBetweenAdditions);
    }
}

LoadingEllipsis.prototype.Stop = function()
{
    if(this.interval != null)
    {
        clearInterval(this.interval);
        this.interval = null;
    }
}