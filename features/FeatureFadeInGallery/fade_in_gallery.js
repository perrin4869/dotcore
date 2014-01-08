// JavaScript Document

/**
 * Constructor for FadeInGalleries
 */
function FadeInGallery(galleryWrapperId, options)
{
    this.wrapper = document.getElementById(galleryWrapperId);
    this.loadingDiv = getElementsByClassName("fade-in-gallery-loading", null, this.wrapper)[0];
    this.galleryContent = getElementsByClassName("gallery-content", null, this.wrapper)[0];
    this.imagesThumbnails = getElementsByClassName("gallery-thumbnails-div", null, this.wrapper)[0];
    this.imagesMenu = getElementsByClassName("gallery-images-div", null, this.wrapper)[0];
    this.currentImageWrapper = getElementsByClassName("gallery-fullsize", null, this.wrapper)[0];
    this.loadedImages = null; // Stores all the fullsized images in a dictionary (the key being the source of the fullsized image)
    this.currentImage = null;
    this.imageToChangeTo = null; // Stores the path of the current image to change to

    if(options == null)
    {
        options = {};
    }

    this.fadeInterval = options.fadeInterval || 300;
    this.imagesMenuWidth = options.imagesMenuWidth || "20%";
}

FadeInGallery.prototype.Initilize = function()
{
    this.PreloadFadeInGalleryImages();
}

FadeInGallery.prototype.PreloadFadeInGalleryImages = function()
{
    var images = this.imagesThumbnails.getElementsByTagName("a");
    var fullsizedImages = new Array();
    var i;
    for(i = 0; i < images.length; i++)
    {
        fullsizedImages.push(images[i].getAttribute("href")); // Preload the fullsized image...
        addEvent(images[i], "mouseover", createObjectCallback(this, this.FadeInGalleryImageHover));
        addEvent(images[i], "click", createObjectCallback(this, this.FadeInGalleryImageClick));
    }

    var fullsizedPreloader = new ImagePreloader(
        fullsizedImages,
        createObjectCallback(this, this.OnFadeInGalleryImagesPreloaded),
        {saveLoadedImages: true}
    );
    fullsizedPreloader.StartLoading();
}

FadeInGallery.prototype.OnFadeInGalleryImagesPreloaded = function(preloader)
{
    var countTmpImages = preloader.images.length;
    var i;
    this.loadedImages = [];
    for(i = 0; i < countTmpImages; i++)
    {
        this.loadedImages[preloader.images[i].getAttribute("src")] = preloader.images[i];
    }

    this.currentImage = preloader.images[0];
    this.currentImageWrapper.appendChild(this.currentImage);
    
    jQuery(this.loadingDiv).fadeOut(150, createObjectCallback(this, this.GalleryLoadingFaded));
}

FadeInGallery.prototype.GalleryLoadingFaded = function(e)
{
    // Set the width of the gallery nav before showing it
    this.imagesMenu.style.width = this.imagesMenuWidth;

    jQuery(this.galleryContent).animate({opacity: 1}, 150);
}

FadeInGallery.prototype.FadeInGalleryImageHover = function(e)
{
    if(this.currentImage != null)
    {
        this.imageToChangeTo = this.loadedImages[e.currentTarget.getAttribute("href")];
        if(this.imageToChangeTo != this.currentImage)
        {
            jQuery(this.currentImage).stop().fadeTo(
                this.fadeInterval,
                0,
                createObjectCallback(
                    this,
                    this.FadeInGalleryImageFaded
                )
            );
        }
    }
}

FadeInGallery.prototype.FadeInGalleryImageClick = function(e)
{
    e.preventDefault();
}

FadeInGallery.prototype.FadeInGalleryImageFaded = function()
{
    jQuery(this.imageToChangeTo).css("opacity", 0);
    this.currentImage.parentNode.replaceChild(this.imageToChangeTo, this.currentImage);
    this.currentImage = this.imageToChangeTo;
    jQuery(this.imageToChangeTo).stop().animate({opacity: 1}, this.fadeInterval);
}
