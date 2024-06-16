function rightClickSeekTooltip (event) {
    const skipTo = Math.round(
        // (event.offsetX / event.target.clientWidth) *
            parseInt(event.target.getAttribute('aria-valuenow'), 10)
    )

    addChapter(skipTo)
    const t = formatTime(skipTo)

    time = `${t.minutes}:${t.seconds}`
}

function formatTime (timeInSeconds) {
    const result = new Date(timeInSeconds * 1000).toISOString().substr(11, 8)

    return {
        minutes: result.substr(3, 2),
        seconds: result.substr(6, 2)
    }
}

// const player = new Plyr('video', {
//     captions: { active: true },
//     keyboard: { focused: true, global: true },
//     hideControls: false,
//     disableContextMenu: true,
//     seekTime: 30,

//     markers: { enabled: true, points: { $ChapterIndex } }
// })
// window.player = player


function setPlayerTime(time){
    player.currentTime = time;
}


function resizeWindow(large)
{
    if(large == null)
    {
        large = false
    }

    if(typeof large == "object"){
        large = true
    }

    videoWidth = { $width }
    videoHeight = { $height }

    text= "Video to W:"+videoWidth + " H:" + videoHeight;
    console.log(text)
    if (videoHeight > 1280) {
        videoHeight = 1280
    }
    if (videoWidth > 1920) {
        videoWidth = 1920
    }
    windowWidth = videoWidth * 0.75
    heightMulti = .75
    // if(large == false) {
        // heightMulti = 1.25
    // }
        windowHeight = videoHeight * heightMulti

    text= "Resising Window to W:"+windowWidth + " H:" + windowHeight + ", x"+heightMulti+" Playlist:"+large;
    console.log(text)

    window.resizeTo(windowWidth, windowHeight)
}

resizeWindow(document.querySelector('.playlist-icon'))


function addChapter(event, timeCode) {
    let x = event.clientX;
    let y = event.clientY;

    txtField = document.getElementById("info");
    txtField.style.left = x + "px";
    txtField.style.top = y + "px";
    txtField.style.display = "block";

    var hiddenAction = document.getElementById("timeCodeInput");
    hiddenAction.value = timeCode;

    // form.submit();
}

$("#addChapter").submit(function (e) {
    var postData = $(this).serializeArray();
    $.ajax({
        url: "process.php",
        type: "POST",
        data: postData,
        success: function (data) {
        //    window.location.href = data;
        var form = $('#addChapter')
        return false;
        setPlayerTime(data)
        },
    });
});
