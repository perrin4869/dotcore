/* 
 * Class used to embed background music into websites
 */

function BackgroundMusic(musicUrl, options) {
    this.musicUrl = musicUrl;
    this.options = options;
    this.baseUrl = (options.baseUrl != undefined) ? options.baseUrl : "";

    this.offMusicImg = this.baseUrl + "/music_off.gif";
    this.onMusicImg = this.baseUrl + "/music_on.gif";

    var preloader = new ImagePreloader(
        [
            this.offMusicImg,
            this.onMusicImg
        ]);
    preloader.StartLoading();

    if(!soundManager.loaded) {
        soundManager.onload = createObjectCallback(this, this.OnSoundManagerLoad);
    }
    else {
        this.OnSoundManagerLoad();
    }

    var imgSource = this.offMusicImg;
    if(readCookie("back_music") != "0")
    {
        imgSource = this.onMusicImg;
    }

    var imgController = document.createElement("img");
    imgController.src = imgSource;
    document.getElementById("bg-music-controller-container").appendChild(imgController);
    addEvent(imgController, "click", createObjectCallback(this, this.OnBackgroundMusicToggle));
}

BackgroundMusic.prototype.OnBackgroundMusicToggle = function() {

    var imgSource;

    if(readCookie("back_music") != "0")
    {
        createCookie("back_music", "0", 365);
        this.backgroundMusic.pause();
        imgSource = this.offMusicImg;
    }
    else
    {
        createCookie("back_music", "1", 365);
        this.backgroundMusic.play();
        imgSource = this.onMusicImg;
    }

    document
        .getElementById("bg-music-controller-container")
        .getElementsByTagName("img")[0]
        .setAttribute("src", imgSource);

}

BackgroundMusic.prototype.OnSoundManagerLoad = function() {
    this.backgroundMusic = soundManager.createSound({
        id: 'background',
        url: this.musicUrl
    });

    if(readCookie("back_music") != "0")
    {
        this.backgroundMusic.play();
    }
}
