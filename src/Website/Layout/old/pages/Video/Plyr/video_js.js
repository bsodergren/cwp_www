
var curVolume = getCookie("playerVolCookie");
if (curVolume == null) {
    curVolume = 0.5;
} else {
    curVolume = curVolume / 100;
}
console.log("current volume " + curVolume);


window.addEventListener("resize", resize, false);

// video.height = 100; /* to get an initial width to work with*/
function togglePopup(x_pos, y_pos, videoId, timeCode) {
    y_pos = y_pos - 50;
    const overlay = document.getElementById("popupOverlay");
    overlay.classList.toggle("show");
    overlay.style.position = "absolute";
    overlay.style.left = x_pos + "px";
    overlay.style.top = y_pos + "px";
    document.getElementById("markerText").value = '';
    document.getElementById("markerText").focus();

    videoIdInput = document.getElementById("markerVideoid");
    videoIdInput.value = videoId;

    IdInput = document.getElementById("markerId");
    IdInput.value = videoId;

    timeCodeInput = document.getElementById("markerTimeCode");
    timeCodeInput.value = timeCode;

    console.log(x_pos, y_pos, videoId, timeCode);
}

function resize() {
    var videoTag = document.getElementsByTagName("video");
    const video = videoTag[0];

    //     videoRatio = video.height / video.width;
    // windowRatio = window.innerHeight / window.innerWidth; /* browser size */

    //     if (windowRatio < videoRatio) {
    //         if (window.innerHeight > 50) { /* smallest video height */
    //         video.height = window.innerHeight;

    //         } else {
    //             video.height = 50;
    //     }
    //     } else {
    //         video.width = window.innerWidth;
    //     }
    //     text= "Resising Window to W:"+video.width + " H:" + video.height;
    //     console.log(text)
}
function updateOptions(id) {
    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            submit: "jquery",
            id: id,
        },
        success: function (data) {
            var outletOptions = document.querySelector(".videoPlaylistButton");
            Array.from(outletOptions).forEach((option) => {
                outletOptions.removeChild(option);
            });

            var myArray = JSON.parse(data);
            var opt = document.createElement("option");

            opt.appendChild(document.createTextNode("Select from List"));
            opt.classList.add("filter-option");
            opt.disabled = true;
            outletOptions.appendChild(opt);

            myArray.map((optionData) => {
                var opt = document.createElement("option");

                opt.appendChild(document.createTextNode(optionData[1]));
                opt.classList.add("filter-option");
                opt.value = optionData[0];
                if (optionData[2] == true) {
                    opt.classList.add("selected");
                }
                if (optionData[3] == true) {
                    opt.classList.add("disabled");
                }

                opt.selected = optionData[2];
                opt.disabled = optionData[3];
                outletOptions.appendChild(opt);
            });
            return false;
        },
    });
}

function setInfoText(className, item) {
    // console.log("setInfoText" , className, item.getAttribute("data-title"))
    var TitleId = "." + className + "_title";
    const textTitle = document.querySelector(TitleId);
    if (textTitle != null) {
        textTitle.textContent = item.getAttribute("data-title");
    }

    var artistId = "." + className + "_artist";
    const textartist = document.querySelector(artistId);
    if (textartist != null) {
        if (item.getAttribute("data-artist") == "") {
            textartist.classList.add("hidden"); // Add 'hidden' class
            textartist.textContent = "";
        } else {
            textartist.textContent = item.getAttribute("data-artist");
        }
    }

    var genreId = "." + className + "_genre";
    const textgenre = document.querySelector(genreId);
    if (textgenre != null) {
        textgenre.textContent = item.getAttribute("data-genre");
    }

    var studioId = "." + className + "_studio";
    const textstudio = document.querySelector(studioId);
    if (textstudio != null) {
        textstudio.textContent = item.getAttribute("data-studio");
    }

    const textvideoid = document.querySelector("#videoPlaylistVideoId");

    const playerTextInfo = document.querySelector(".player_" + className);
    if (playerTextInfo != null) {
        playerTextInfo.setAttribute("href", item.getAttribute("data-pUrl"));

        textvideoid.value = item.getAttribute("data-videoid");
        playerTextInfo.setAttribute(
            "data-videoid",
            item.getAttribute("data-videoid")
        );
        playerTextInfo.setAttribute(
            "onclick",
            "videoCard(" + textvideoid.value + ")"
        );
    }
}

function seektoTime(timeCode) {
    PlayerApp.player.currentTime = timeCode;
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function formatTime(timeInSeconds) {
    const result = new Date(timeInSeconds * 1000).toISOString().substr(11, 8);

    return {
        minutes: result.substr(3, 2),
        seconds: result.substr(6, 2),
    };
}

function addMarker() {
    var action = document.getElementById("markerAction").value;

    var timeCode = document.getElementById("markerTimeCode");
    var videoid = document.getElementById("markerVideoid").value;
    var playlistid = document.getElementById("markerPlaylistId").value;
    var markerText = document.getElementById("markerText");

    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            action: action,
            videoId: videoid,
            id: videoid,
            timeCode: timeCode.value,
            playlist_id: playlistid,
            markerText: markerText.value,
            exit: true,
        },
        cache: false,
        success: function (data) {
            const videoCell = document.getElementById("videoMarkerList");
            videoCell.innerHTML = data;
            markerText.value = "";
            timeCode.value = "";

            const overlay = document.getElementById("popupOverlay");
            overlay.classList.toggle("show");

            return false;
        },
    });
}


function updateFavVideo(videoid) {
    // const playervideoid = document.querySelector('.player_text')
    // videoid = playervideoid.getAttribute('data-videoid')

    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            action: "isFavorite",
            videoId: videoid,
        },
        cache: false,
        success: function (data) {
            const videoCell = document.getElementById("FavoriteButton");
            if (videoCell != null) {
                videoCell.innerHTML = data;
            }
        },
    });
}

function updateVideoMarkers(videoid) {
    // const playervideoid = document.querySelector('.player_text')
    // videoid = playervideoid.getAttribute('data-videoid')

    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            action: "getMarker",
            id: videoid,
        },
        cache: false,
        success: function (data) {
            const videoCell = document.getElementById("videoMarkerList");
           // if (videoCell != null) {
                videoCell.innerHTML = data;
           // }
        },
    });
}

function calculateAspectRatio(width, height) {
    function gcd(a, b) {
        return b == 0 ? a : gcd(b, a % b);
    }

    var divisor = gcd(width, height);

    return width / divisor + ":" + height / divisor;
}

function calculateApectHeight(width, ratio) {
    var t_arr = ratio.split(":");
    var div = width / t_arr[0];
    return div * t_arr[1];
}

function resizeWindow(large, width = 1280, height = 720) {
    if (large == null) {
        large = false;
    }

    if (typeof large == "object") {
        large = true;
    }

    videoWidth = width;
    videoHeight = height;

    text = "Origintal Video W:" + videoWidth + " H:" + videoHeight;
    // console.log(text)

    ratio = calculateAspectRatio(videoWidth, videoHeight);

    if (videoWidth > 1921) {
        videoWidth = 1280;
    }

    windowWidth = videoWidth * 0.75;
    videoHeight = calculateApectHeight(videoWidth, ratio);

    text = "Video to W:" + videoWidth + " H:" + videoHeight + " Ratio:" + ratio;
    console.log(text)
    heightMulti = 0.95;
    if (large == true) {
        // heightMulti = heightMulti + .13
    }
    windowHeight = videoHeight * heightMulti;

    text =
        "Resising Window to W:" +
        windowWidth +
        " H:" +
        windowHeight +
        ", x" +
        heightMulti +
        " Playlist:" +
        large;
     console.log(text)

    window.resizeTo(windowWidth, windowHeight);
}

resizeWindow(document.querySelector(".playlist-icon"), { $width }, { $height });
