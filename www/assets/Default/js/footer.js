    $(document).ready(function () {
        $(".jobAction").on("click", function () {
            var action = this.getAttribute('data-job-action')
            var id = this.getAttribute('data-job-id')
            console.log(action,id);
            // $("#logBody").load(url);


            $.ajax({
                type: "post",
                url: "process.php",
                data: {
                    submit: action,
                    job_id: id,
                },
                cache: false,
                success: function (data) {
                    console.log(data);
                    return false;
        },
            });


        });
    });


var $editMetadata = $(".editMetadata");

function metaEditor() {
    $editMetadata.editable({
        emptyMessage: "Please write something...",
        callback: function (data) {
            editBox = data.$el[0].id;
            const editBoxArr = editBox.split("_");
            var metafield = editBoxArr[0];
            var videoId = editBoxArr[1];

            if (data.content !== false) {
                let value = data.content.trim();

                if (value == "") {
                    value = "NULL";
                }
                $.ajax({
                    type: "post",
                    url: "process.php",
                    data: jQuery.param({
                        submit: "updateVideoCard",
                        field: metafield,
                        value: value,
                        video_id: videoId,
                    }),
                    success: function (data) {
                        let close = document.getElementById("reload");
                        console.log(" window reload -> " + close);

                        if (close != null) {
                            window.opener.location.reload(true);
                            window.location.reload(true);
                        }
                    },
                });
                console.log("   * The text was changed -> " + value);
            }
        },
    });
}

$editMetadata.on("edit", function () {
    console.log("Started editing element " + this.nodeName);
});

metaEditor();
