// Image Preloader script by Julian Grinblat
// Gets a list of image paths, and loads them.
// When it finishes, it callbacks to the client

var ImagePreloader = function(paths, callback, options)
{
    this.callback = callback;
    this.paths = paths;
    this.count = 0;
    this.validPaths = []; // Will store the valid paths in this array
    this.images = [];

    if(options == null)
    {
        options = {};
    }

    this.saveLoadedImages = options.saveLoadedImages || false;
    this.CountIncreased = options.countIncreased || null;
    
    this.StartLoading = function()
    {
        var currImg;
        var i;
        var totalImgs = this.paths.length;
        if(totalImgs > 0)
        {
            for(i = 0; i < totalImgs; i++)
            {
                if(typeof this.paths[i] == "string")
                {
                    currImg = document.createElement("img");
                    addEvent(currImg, "load", createObjectCallback(currImg, this.OnImageLoaded, [this]));
                    addEvent(currImg, "error", createObjectCallback(currImg, this.OnImageError, [this]));
                    currImg.setAttribute("src", this.paths[i]);
                    if(this.saveLoadedImages)
                    {
                        this.images.push(currImg);
                    }
                }
                else
                {
                    if(this.paths[i].complete)
                    {
                        this.count++;
                        this.DoCheck();
                    }
                    else
                    {
                        addEvent(this.paths[i], "load", createObjectCallback(this.paths[i], this.OnImageLoaded, [this]));
                        addEvent(this.paths[i], "error", createObjectCallback(this.paths[i], this.OnImageError, [this]));
                    }

                    if(this.saveLoadedImages)
                    {
                        this.images.push(this.paths[i]);
                    }
                }
            }
        }
        else
        {
            this.DoCheck();
        }
    }
    
    this.OnImageLoaded = function(e,thisObj)
    {
        thisObj.count++;
        thisObj.validPaths.push(this.src); // Add this to the valid paths
        thisObj.DoCheck();
    }
    
    this.OnImageError = function(e,thisObj)
    {
    	thisObj.count++;
    	thisObj.DoCheck();
    }
    
    this.DoCheck = function()
    {
    	var totalImgs = this.paths.length;
        if(this.count == totalImgs)
        {
            if(this.callback != null)
            {
                // Finished loading, call the callback
                this.callback(this);
            }
        }
        else
        {
            if(this.CountIncreased)
            {
                this.CountIncreased(this);
            }
        }
    }
}