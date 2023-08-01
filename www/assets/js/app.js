function popup(mylink, windowname, width = 800, height = 400) {
    if (!window.focus) return true;
    var href;
    if (typeof (mylink) == 'string') href = mylink; else href = mylink.href;
    window.open(href, windowname, 'width=' + width + ',height=' + height + ',scrollbars=yes');
    return false;
}

function editPlaceholder(id) {
    var x = document.getElementById(id).placeholder;

    if (x !== "") {
        document.getElementById(id).value = x;
        document.getElementById(id).style = "background:white";

    }
}

function editRadioValue(id) {
    const t_arr = id.split('_');
    console.log(t_arr);
    let former = t_arr[0];
    let letter = t_arr[1];
    let f_id = t_arr[2];

    if (former == "Front") {
        let back_id = "Back_" + letter + "_" + f_id;
        document.getElementById(back_id).value = "";
    } else {
        let front_id = "Front_" + letter + "_" + f_id;
        document.getElementById(front_id).value = "";
    }

    document.getElementById(id).value = former;

}

function hideSubmit(id, text) {


    console.log(id);

    document.getElementById('hiddenSubmit_' + id).value = text;
}

function doSubmitValue(id) {
    document.getElementById(id).value = id;
}


function checkValue(id) {
    var ph = document.getElementById(id).placeholder;
    var n = document.getElementById(id).value;


    if (ph == n) {
        document.getElementById(id).value = "";
    } else {
        document.getElementById(id).style = "background:white";
    }
}

function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}