// JavaScript Document

/**
 * Constructor for Lightbox Slide Galleries
 */
function LightboxSlideGallery(galleryWrapperId, options)
{
    this.wrapper = document.getElementById(galleryWrapperId);
    this.thumbnailsContainer = getElementsByClassName("lb_slide_gallery_images_thumbnails", null, this.wrapper)[0];
    this.galleryContent = getElementsByClassName("lb_slide_gallery_content", null, this.wrapper)[0];
    this.nextPicAnchor = getElementsByClassName("lb_slide_next_pic_anchor", null, this.wrapper)[0];
    this.prevPicAnchor = getElementsByClassName("lb_slide_prev_pic_anchor", null, this.wrapper)[0];
    this.loadingDiv = getElementsByClassName("lb_slide_loading_div", null, this.wrapper)[0];

    this.scroller = new DivScroller(this.thumbnailsContainer);
    this.scroller.intervalBetweenMovements = 40;
    this.scroller.deltaScroll = 8;

    if(options == null)
    {
        options = {};
    }
}

LightboxSlideGallery.prototype.Initilize = function()
{
    this.Setup();
}

LightboxSlideGallery.prototype.Setup = function()
{
    addEvent(this.nextPicAnchor, "mouseover", createObjectCallback(this, this.OnNextPicMouseOver));
    addEvent(this.prevPicAnchor, "mouseover", createObjectCallback(this, this.OnPrevPicMouseOver));
    addEvent(this.nextPicAnchor, "mouseout", createObjectCallback(this, this.OnScrollerMouseOut));
    addEvent(this.prevPicAnchor, "mouseout", createObjectCallback(this, this.OnScrollerMouseOut));

    // Set the width of the scrollable div so that it scrolls
    // For some reason one more pixel needs to be substracted

    var thumbnailsParent = this.thumbnailsContainer.parentNode; // The parent node is hidden, you can't get the width from it
    var oldWidth = thumbnailsParent.parentNode.offsetWidth;
    var nextWidth = this.nextPicAnchor.offsetWidth;
    var prevWidth = this.prevPicAnchor.offsetWidth;
    this.thumbnailsContainer.style.width = (oldWidth - 1 - nextWidth - prevWidth) + "px";
    
    // Preload the thumbnails
    var images = jQuery("img", this.thumbnailsContainer);
    var preloader = new ImagePreloader(images, createObjectCallback(this, this.OnImagesLoaded));
    preloader.StartLoading();
}

LightboxSlideGallery.prototype.OnImagesLoaded = function()
{
    jQuery(this.loadingDiv).fadeOut(300, createObjectCallback(this, this.OnLoadingDivFaded));
}

LightboxSlideGallery.prototype.OnLoadingDivFaded = function()
{
    jQuery(this.galleryContent).animate({opacity: 1}, 300);
}

LightboxSlideGallery.prototype.OnNextPicMouseOver = function()
{
    this.scroller.StartScrolling(ScrollTypes.Left);
}

LightboxSlideGallery.prototype.OnPrevPicMouseOver = function()
{
    this.scroller.StartScrolling(ScrollTypes.Right);
}

LightboxSlideGallery.prototype.OnScrollerMouseOut = function()
{
    this.scroller.StopScrolling();
}
