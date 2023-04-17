$(document).ready(function () {
    var myTable;
    var url_update = '/api/claims/update';
    var url_list = '/api/claims/list';

    var columnDefs = [
        {
            data: "claim_id",
            title: "ID",
            type: "readonly"
    },
        {
            data: "user_full_name",
            title: "Person",
            type: "readonly"
    },
        {
            data: "prize_name",
            title: "Product",
            type:"readonly"
    },
        {
            data: "claim_date",
            title: "Claim Date"
    },
        {
            data: "is_delivered",
            title: "Delivered",
            type: "select",
            render: function(data){
                if(data === "1"){
                    return 'Yes';
                } else {
                    return 'No';
                }
            },
            options: {1:"Yes", 0:"No"}
    }
    ];




    myTable = $('#tbl_claims').DataTable({
        "sPaginationType": "full_numbers",
        ajax: {
            url : url_list,
            cache: true,
            // our data is an array of objects, in the root node instead of /data node, so we need 'dataSrc' parameter
            dataSrc : ''
        },
        columns: columnDefs,
        dom: 'Bfrtip',        // Needs button container
        select: 'single',
        responsive: true,
        altEditor: true,     // Enable altEditor
        buttons: [
            {
                extend: 'selected', // Bind to Selected row
                text: 'Edit',
                name: 'edit'        // do not change name
        },
            {
                text: 'Refresh',
                name: 'refresh'      // do not change name
        }
        ],
        onEditRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='POST'
                url: url_update,
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        }
    });


});