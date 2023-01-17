function importValidation() 
{
  // Make quick references to our fields.
  var jobNumber = document.getElementById("jobNumber");
  var pdffile = document.getElementById("fileToUpload");

  if (validateFileType(pdffile, "* Please select a pdf file *")) {
    if (textNumeric(jobNumber, "* Please enter a valid job Number *")) {
      if (lengthDefine(jobNumber, 6, 6)) {
        return true;
      }
    }
  }
  return false;
}

// Function that checks whether input text is numeric or not.
function textNumeric(inputtext, alertMsg) {
  var numericExpression = /^[0-9]+$/;

  if (inputtext.value.match(numericExpression)) {
    return true;
  } else {
    document.getElementById("p6").innerText = alertMsg; // This segment displays the validation rule for zip.
    inputtext.focus();
    return false;
  }
}

function validateFileType(inputtext, alertMsg) {

    var files = inputtext.files;
    if(files.length==0){
        document.getElementById("p4").innerText = alertMsg; // This segment displays the validation rule for zip.
        return false;
    }else{
        var filename = files[0].name;

        /* getting file extenstion eg- .jpg,.png, etc */
        var extension = filename.substr(filename.lastIndexOf("."));

        /* define allowed file types */
        var allowedExtensionsRegx = /(\.pdf)$/i;

        /* testing extension with regular expression */
        var isAllowed = allowedExtensionsRegx.test(extension);

        if(!isAllowed){
            document.getElementById("p4").innerText = alertMsg; 
            return false;
        }
        return true;

    }
}

// Function that checks whether the input characters are restricted according to defined by user.
function lengthDefine(inputtext, min, max) {
  var uInput = inputtext.value;
  if (uInput.length >= min && uInput.length <= max) {
    return true;
  } else {
    document.getElementById("p6").innerText =      "* Please enter between " + min + " and " + max + " characters *"; // This segment displays the validation rule for username
    inputtext.focus();
    return false;
  }
}


function popup(mylink, windowname,width=800,height=400)
{
   if (! window.focus)return true;
   var href; 
   if (typeof(mylink) == 'string') href=mylink; else href=mylink.href;
   window.open(href, windowname, 'width='+width+',height='+height+',scrollbars=yes'); 
   return false; 
} 

function editPlaceholder(id) {
  var x = document.getElementById(id).placeholder;

   if (x !== "") {
    document.getElementById(id).value = x;
    document.getElementById(id).style = "background:white";

  }
}

function editRadioValue(id)
{
  const t_arr = id.split('_');
    console.log(t_arr);
    let former = t_arr[0];
    let letter = t_arr[1];
    let f_id = t_arr[2];

    if ( former == "Front"){
      let back_id = "Back_"  + letter + "_" + f_id;
      document.getElementById(back_id).value = "";
    } else {
      let front_id = "Front_"  + letter + "_" + f_id;
      document.getElementById(front_id).value = "";
    }

    document.getElementById(id).value = former;
    
}

function hideSubmit(id,text)
{

  
  console.log(id);

  document.getElementById('hiddenSubmit_'+id).value = text; 
}

function doSubmitValue(id)
{
  document.getElementById(id).value = id;
}


function checkValue(id) {
    var ph = document.getElementById(id).placeholder;
    var n =  document.getElementById(id).value;


    if (ph == n) {
        document.getElementById(id).value = "";
    } else {
      document.getElementById(id).style = "background:white";
    }
  }
  
