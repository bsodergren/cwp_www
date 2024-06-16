var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

function writeLog(msg) {
    let date = new Date();
    // console.log (writeLog.caller.callee)
    console.trace(msg);
    // window.console.log(date.toISOString() + " " + msg);
}

function editPlaceholder(id) {
    var x = document.getElementById(id).placeholder;

    if (x !== "") {
        document.getElementById(id).value = x;
        document.getElementById(id).style = "background:white";
    }
}

function hideSubmit(id, text) {
    document.getElementById("hiddenSubmit_" + id).value = text;
}

function doSubmitValue(id) {
    document.getElementById(id).value = id;
}

function editRadioValue(id) {
    document.getElementById(id).value = "1";
}

function checkValue(id) {
    var ph = document.getElementById(id).placeholder;
    var n = document.getElementById(id).value;

    const t_arr = id.split("_");
    if (t_arr[1] == "studio") {
        var ph = "___";
    }

    if (t_arr[1] == "substudio") {
        var ph = "___";
    }

    if (ph == n) {
        document.getElementById(id).value = "";
    } else {
        document.getElementById(id).style = "background:white";
    }
}

function setNull(id) {
    var f_id = id + "_id";
    document.getElementById(f_id).value = "NULL";
    document.getElementById(f_id).style = "background:black";
}

function openOnce(url, target) {
    // open a blank "target" window
    // or get the reference to the existing "target" window
    var winref = window.open("", target, "");

    // if the "target" window was just opened, change its url
    if (winref.location.href === "about:blank") {
        winref.location.href = url;
    }
    return winref;
}

function videoCard(id) {
    url = "http://" + window.location.hostname + window.location.pathname;
    url = url.replace("video.php", "");
    popup(url + "videoinfo.php?id=" + id, "videoinfo_popup");
}

function popup(mylink, windowname, width = 1028, height = 800) {
    //console.log(width + ' ' + height);

    var href;
    if (typeof mylink == "string") href = mylink;
    else href = mylink.href;

    var winref = window.open(
        href,
        windowname,
        "scrollbars=yes,width=" + width + ",height=" + height + ""
    );

    if (winref.location.href != "about:blank") {
        winref.location.href = href + "&r=true";
    }

    return winref;
}

function previewHover(e, thumb, prev) {
    if (prev == "") {
        return false;
    } else {
        var element = document.getElementById(e.id);

        element.addEventListener("mouseenter", (event) => {
            element.src = prev;
        });

        element.addEventListener("mouseleave", (event) => {
            element.src = thumb;
        });
    }
}

function FavoriteVideo(action, videoid = null) {
    var favButton = "FavoriteButton";
    if (videoid == null) {
        const playervideoid = document.querySelector(".player_text");
        videoid = playervideoid.getAttribute("data-videoid");
    } else {
        favButton = favButton + "_" + videoid;
    }
    // console.log(action + ' ' + videoid)
    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            action: action,
            videoId: videoid,
            exit: true,
        },
        cache: false,
        success: function (data) {
            const videoCell = document.getElementById(favButton);
            videoCell.innerHTML = data;
        },
    });
}
