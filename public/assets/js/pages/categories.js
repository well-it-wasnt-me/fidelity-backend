$(document).ready(function () {
    var myTable;
    var url_update = '/api/categories/update';
    var url_delete = '/api/categories/delete';
    var url_list = '/api/categories/list';
    var url_add = '/api/categories/add';

    var columnDefs = [
        {
            data: "cat_id",
            title: "ID",
            type: "readonly"
    },
        {
            data: "cat_name",
            title: "Category Name"
    },
        {
            data: "cat_descr",
            title: "Description"
    },
        {
            data: "cat_picture",
            title: "Picture",
            render: function (data) {
                return '<img width="64px" height="64px" src="'+data+'"/>';
            }
    },
        {
            data: "is_active",
            title: "Active",
            render: function(data){
                if(data === "1"){
                    return 'Active';
                } else {
                    return 'Inactive';
                }
            },
            type: "select",
            options: {1:"Active", 0:"In-Active"}
    }
    ];




    myTable = $('#tbl_categories').DataTable({
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
                text: 'Add',
                name: 'add'        // do not change name
        },
            {
                extend: 'selected', // Bind to Selected row
                text: 'Edit',
                name: 'edit'        // do not change name
        },
            {
                extend: 'selected', // Bind to Selected row
                text: 'Delete',
                name: 'delete'      // do not change name
        },
            {
                text: 'Refresh',
                name: 'refresh'      // do not change name
        }
        ],
        onAddRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be / with type='PUT'
                url: url_add,
                type: 'POST',
                data: rowdata,
                success: success,
                error: error
            });
        },
        onDeleteRow: function (datatable, rowdata, success, error) {
            $.ajax({
                // a tipycal url would be /{id} with type='DELETE'
                url: url_delete,
                type: 'POST',
                data: rowdata[0],
                success: success,
                error: error
            });
        },
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