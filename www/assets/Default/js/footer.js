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
