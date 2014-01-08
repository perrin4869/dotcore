var SWFObjectLoader =
{
    OnLoad: function() {
        var flash_elements = getElementsByClassName("flash");
        var i;
        var count_flash_elements = flash_elements.length;
        var id;
        var generatedID = 0;
        for(i = 0; i < count_flash_elements; i++)
        {
            id = flash_elements[i].getAttribute('id');
            if(id == "")
            {
                generatedID++;
                id = 'flash' + generatedID;
                flash_elements[i].setAttribute('id', id);
            }
            swfobject.registerObject(id, "9.0.0", "expressInstall.swf");
        }
    }
}

addEvent(window, "load", SWFObjectLoader.OnLoad);