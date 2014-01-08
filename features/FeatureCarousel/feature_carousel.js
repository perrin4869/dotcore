// JavaScript Document

/**
 * Constructor for FadeInGalleries
 */
function FeatureCarousel(wrapperID)
{
    this.wrapper = document.getElementById(wrapperID);
    this.loading = getElementsByClassName("feature-carousel-loading", null, this.wrapper)[0];
    this.carousel = getElementsByClassName("feature-carousel-images", null, this.wrapper)[0];

    this.marquee = null;
}

FeatureCarousel.prototype.Initilize = function()
{
    // Preload the images
    var images = this.carousel.getElementsByTagName("img");
    var paths = [];
    var i, count = images.length;
    for(i = 0; i < count; i++)
    {
        paths.push(images[i].getAttribute("src"));
    }

    var preloader = new ImagePreloader(paths, createObjectCallback(this, this.OnImagesLoaded));
    preloader.StartLoading();
}

FeatureCarousel.prototype.OnImagesLoaded = function()
{
    jQuery(this.loading).fadeOut(300, createObjectCallback(this, this.OnLoadingFadeOut));
}

FeatureCarousel.prototype.OnLoadingFadeOut = function()
{
    jQuery(this.carousel).animate({opacity: 1}, 300, createObjectCallback(this, this.OnCarouselFadeIn));
}

FeatureCarousel.prototype.OnCarouselFadeIn = function()
{
    this.marquee = new Marquee(this.carousel, {
        startPosition: MarqueeStartPosition.Filling,
        alwaysFull: true,
        direction: MarqueeDirection.Right,
        interval: 15});
}