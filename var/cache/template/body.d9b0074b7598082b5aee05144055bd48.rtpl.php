<?php if(!class_exists('Rain\Tpl')){exit;}?><?php require $this->checkTemplate('index/'.$headerTemplate);?>

<table class="table table-dark">
    <thead>
        <tr>
            <th class="fs-3">Media</th>
        </tr>
    </thead>
    <tbody>
        
        <?php $counter1=-1;  if( isset($jobArray) && ( is_array($jobArray) || $jobArray instanceof Traversable ) && sizeof($jobArray) ) foreach( $jobArray as $key1 => $value1 ){ $counter1++; ?>

        <tr class="fs-5">
            <td>
                <?php echo $value1["FORM_HTML_START"]; ?>


                <div class="row" id="hide_<?php echo $value1["JOB_ID"]; ?>"
                    onclick="return clickButton(this.id);">
                    <div class="col text-nowrap">Job Number: <span
                            id="job_number"
                            name="jobNumber_<?php echo $value1["JOB_ID"]; ?>"><?php echo $value1["TEXT_JOB"]; ?></span>
                        <p id="p6"></p>
                    </div>
                    <div class="col text-nowrap text-md">
                        <span class="text-white rounded-pill bg-success px-4"
                            <?php echo $value1["TEXT_CLOSE_URL"]; ?>><?php echo $value1["TEXT_CLOSE"]; ?></span>
                    </div>
                    <div class="col text-end"><?php echo $value1["NUM_OF_FORMS"]; ?></div>
                </div>
                <div class="<?php echo $value1["HIDDEN_CLASS"]; ?>" id="model_<?php echo $value1["JOB_ID"]; ?>">
                    <div class="row mb-2">

                        <div class="btn-group mb-2" role="group"
                            aria-label="Default button group">
                            <?php echo $value1["FORM_BUTTONS_HTML"]; ?>

                        </div>
                        <div class="btn-group mb-2" role="group"
                            aria-label="Default button group">
                            <?php echo $value1["FORM_EXTRA_HTML"]; ?>

                        </div>

                        <div class="btn-group" role="group"
                            aria-label="Delete button Group">
                            <?php echo $value1["FORM_DELETE_HTML"]; ?>

                        </div>
                    </div>
                </div>
                <?php echo $value1["FORM_CLOSE"]; ?>

            </td>
        </tr>
        <?php } ?>

        

    </tbody>
</table>
<script type="text/javascript">

    function OpenNewWindow(MyPath) {
        window.open(MyPath, "GoogleDrive", "location=center,height=600,width=1280");
    }
    function createButton(id) {

        var divArr = id.split("_");
        var action = divArr[0] + '_' + divArr[1];
        var job_id = divArr[2];

        console.log(action, job_id);
        $.ajax({
            type: "post",
            url: "<?php echo __PROCESS_FORM__; ?>",
            data:
            {
                'submit': { 'create_xlsx': 'create xlsx' },
                'job_id': job_id
            },
            cache: false,
            success: function () {

            }
        });

    }


    function clickButton(id) {

        console.log(id);
        var divArr = id.split("_");

        let row_id = "model_" + divArr[1];

        var divClass = document.getElementById(row_id).className;
        console.log(divClass);


        if (divClass == "collapse") {
            document.getElementById(row_id).className = "collapse.show";

        } else {
            document.getElementById(row_id).className = "collapse";
        }

        $.ajax({
            type: "post",
            url: "<?php echo __PROCESS_FORM__; ?>",
            data:
            {
                'divClass': divClass,
                'row_id': row_id
            },
            cache: false,
            success: function () {

            }
        });


        return false;
    }
</script>
<?php require $this->checkTemplate('index/'.$footerTemplate);?>