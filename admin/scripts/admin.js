
var AdminEvents = {
    OnAdminLoad: function()
    {
        var calendars = getElementsByClassName("date-time-input");

        var i;
        var popupButton, popupButtonID;
        var calendar, calendarID;
        for(i = 0; i < calendars.length; i++)
        {
            calendar = Ext.getDom(calendars[i]);
            calendarID = calendar.getAttribute("id");

            popupButton = document.createElement("button");
            popupButtonID = calendarID + "-button";
            popupButton.setAttribute("id", popupButtonID);
            popupButton.innerHTML = "בחר תאריך";

            calendar.parentNode.insertBefore(popupButton, calendar.nextSibling);

            Calendar.setup({
                inputField     :    calendarID,           //*
                ifFormat       :    "%Y-%m-%d %H:%M:%S",
                showsTime      :    true,
                button         :    popupButtonID,        //*
                step           :    1,
                date           :    new Date(calendar.value)
            });
        }
    }
}

addEvent(window, "load", AdminEvents.OnAdminLoad);
