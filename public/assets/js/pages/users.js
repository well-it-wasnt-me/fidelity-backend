$(document).ready(function () {
    var myTable;
    var url_update  = '/api/users/update';
    var url_delete  = '/api/users/delete';
    var url_list    = '/api/users/list';
    var url_add     = '/api/users/add';

    var columnDefs = [
        {
            data: "user_id",
            title: "ID",
            type: "readonly"
    },
        {
            data: "f_name",
            title: "Name"
    },
        {
            data: "l_name",
            title: "Surname"
    },
        {
            data: "email",
            title: "E-Mail"
    },
        {
            data: "password",
            title: "Password",
            type:"password",
            render:function(data){
                return "";
            }
    },
        {
            data: "account_status",
            title: "Account Status",
            type:"select",
            options: {1:"Active", 0:"In-Active"},
            render: function(data){
                if(data === "1"){
                    return "Active";
                } else {
                    return "In-Active";
                }
            }
    },
        {
            data: "account_role",
            title: "Role",
            type:"select",
            options: {1:"User", 2:"Admin"},
            render: function(data){
                if(data === "1"){
                    return "User";
                } else {
                    return "Admin";
                }
            }
    },
        {
            data: "creation_date",
            title: "Created On",
            type: "readonly",
    },
        {
            data: "locale",
            title: "Locale",
            type: "select",
            options: {"en_EN":"English", "it_IT":"Italian"}
    },
        {
            data: "full_addr",
            title: "Address",
    },
        {
            data: "phone_number",
            title: "Phone Number",
    },
    ];




    myTable = $('#tbl_users').DataTable({
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