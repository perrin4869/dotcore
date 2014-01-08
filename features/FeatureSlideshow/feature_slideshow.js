// JavaScript Document

/**
 * Constructor for FadeInGalleries
 */
function FeatureSlideshow(wrapperID, options)
{
    this.wrapper = document.getElementById(wrapperID);
    this.imagesWrapper = getElementsByClassName("feature-slideshow-images", null, this.wrapper)[0];
    this.loadingWrapper = getElementsByClassName("feature-slideshow-loading", null, this.wrapper)[0];
    this.images = this.imagesWrapper.getElementsByTagName("img");

    if(options == null)
    {
        options = {};
    }

    this.width = (options.width != null) ? options.width : ((this.images[0] != null ? this.images[0].offsetWidth + "px" : "100%"));
    this.height = (options.height != null) ? options.height : ((this.images[0] != null ? this.images[0].offsetHeight + "px" : "100%"));
    this.wrapper.style.width = this.images[0].offsetWidth + "px";
    this.wrapper.style.height = this.images[0].offsetHeight + "px";
    this.fx = options.fx != null ? options.fx : null;
}

FeatureSlideshow.prototype.Initilize = function()
{
    // Preload the images
    var preloader = new ImagePreloader(this.images, createObjectCallback(this, this.OnImagesLoaded));
    preloader.StartLoading();

    var imgWidth = this.width;
    var imgHeight = this.height;
    var fx;

    switch(this.fx)
    {
        case 'fade':
            fx = {
                fx: 'fade'
            }
            break;
        default:
            fx = {
                fx: 'custom',
                cssBefore: {
                    top:  0,
                    left: 0,
                    width: 0,
                    height: 0,
                    display: "block"
                },
                animIn:  {
                    width: imgWidth,
                    height: imgHeight
                },
                animOut: {
                    top:  imgHeight,
                    left: imgWidth,
                    width: 0,
                    height: 0
                },
                cssAfter: {
                    display: "none"
                },
                delay: -1000
            }
            break;

    }

    jQuery(this.imagesWrapper)
        .cycle(fx)
        .cycle('pause');
}

FeatureSlideshow.prototype.OnImagesLoaded = function(preloader)
{
    jQuery(this.loadingWrapper).fadeOut(500, createObjectCallback(this, this.OnLoadingFaded));
}

FeatureSlideshow.prototype.OnLoadingFaded = function()
{
    jQuery(this.imagesWrapper).animate({opacity: 1}, 500, createObjectCallback(this, this.PlaySlideshow));
}

FeatureSlideshow.prototype.PlaySlideshow = function()
{
    jQuery(this.imagesWrapper)
        .cycle('resume');
}