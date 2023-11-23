const FORM_URL = 'dataObject.php';

function load_search_history(e) {
    var search_box = e.name;
    var search_result = e.name + '_result';
    var search_query = document.getElementsByName(search_box)[0].value;
    if (search_query == '') {
        fetch(FORM_URL, {
            method: "POST",
            body: JSON.stringify({
                action: 'fetch',
                table: search_box
            }),
            headers: {
                'Content-type': 'application/json; charset=UTF-8'
            }
        }).then(function (response) {
            return response.json();
        }).then(function (responseData) {
            if (responseData.length > 0) {
                var html = '<ul class="list-group">';
               // html += '<li class="list-group-item d-flex justify-content-between align-items-center"><b class="text-primary"><i>Your Recent Searches</i></b></li>';
                for (var count = 0; count < responseData.length; count++) {
                    html += '<li class="list-group-item text-muted" style="cursor:pointer"><i class="fas fa-history mr-3"></i><span onclick="get_text(this,\'' + e.name + '\')">' + responseData[count].search_query + '</span> <i class="far fa-trash-alt float-right mt-1" onclick="delete_search_history(' + responseData[count].id + ')"></i></li>';
                }
                html += '</ul>';
                document.getElementById(search_result).innerHTML = html;
                if(document.getElementById(search_result).hidden == true){
                    document.getElementById(search_result).hidden = false
                }
            }

        });

    }


}

function get_text(event,name) {

    var string = event.textContent;
    var search_result = name + '_result';
    var search_box = name;


    //fetch api

    fetch(FORM_URL, {

        method: "POST",

        body: JSON.stringify({
            search_query: string,
            table: search_box
        }),

        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    }).then(function (response) {

        return response.json();

    }).then(function (responseData) {

        document.getElementsByName(search_box)[0].value = string;

        document.getElementById(search_result).innerHTML = '';

    });



}

function hide_data(name)
{
    var search_result = name + '_result';


    if(document.getElementById(search_result).hidden == false){
    document.getElementById(search_result).hidden = true;
    }

}

function load_data(e) {
    var query = e.value;
    var table = e.name;
    var search_result = e.name + '_result';

    if (query.length > 2) {
        var form_data = new FormData();

        form_data.append('query', query);
        form_data.append('table', table);
        var ajax_request = new XMLHttpRequest();

        ajax_request.open('POST', FORM_URL);

        ajax_request.send(form_data);

        ajax_request.onreadystatechange = function () {
            if (ajax_request.readyState == 4 && ajax_request.status == 200) {
                var response = JSON.parse(ajax_request.responseText);

                var html = '<div class="list-group">';

                if (response.length > 0) {
                    for (var count = 0; count < response.length; count++) {
                        html += `<a href="#" class="list-group-item list-group-item-action" onclick = "get_text(this,\'${table}\')" > ${response[count].post_title}</a > `;
                    }
                }
                else {
                    html += '<a href="#" class="list-group-item list-group-item-action disabled">No Data Found</a>';
                }

                html += '</div>';

                document.getElementById(search_result).innerHTML = html;
                if(document.getElementById(search_result).hidden == true){
                    document.getElementById(search_result).hidden = false
                }
            }
        }
    }
    else {
        document.getElementById(search_result).innerHTML = '';
    }
}