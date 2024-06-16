
var $editMarker = $('.editMarker')

function markerEditor() {
    $editMarker.editable({
        emptyMessage: 'Please write something...',
        callback: function (data) {
            console.log('Stopped editing ' + data.$el[0].nodeName)


            editBox = data.$el[0].id;
            const editBoxArr = editBox.split("_");
            var metafield = editBoxArr[0];
            var videoId = editBoxArr[1];
            var markerId = editBoxArr[2];
            var displayVid = editBoxArr[3];

            markerAction = 'updateMarker'
            let value = "";

            if (data.content !== false) {
                 value = data.content.trim();

                if (value == "") {
                    markerAction = 'deleteMarker'
                }


                $.ajax({
                    type: 'post',
                    url: 'process.php',
                    data: jQuery.param({
                        submit: markerAction,
                        id: videoId,
                        markerText: value,
                        markerId: markerId,
                        video: displayVid,
                        edit: videoId,
                        }),
                        success: function (data) {
                            const videoCell = document.getElementById("videoMarkerList");
                           // if (videoCell != null) {
                                videoCell.innerHTML = data;
                           // }
                            // console.log(data);
                            // window.opener.location.reload(true);
                           // window.close();
                        }
                })
                console.log('   * The text was changed -> ' + data.content)
            }
            //}
        }
    })
}


$editMarker.on('edit', function () {
    writeLog('Started editing element ' + this.nodeName)
})

markerEditor()


