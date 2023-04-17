$(document).ready(function () {
    $.get('/api/stat/today/money', function (resp) {
        if (resp.total_money !== null) {
            $("#todaysmoney").text(resp.total_money / 100);
        }
    });
    $.get('/api/stat/today/users', function (resp) {
        $("#todaysusers").text(resp.total_user);
    })
    $.get('/api/stat/total-users', function (resp) {
        $("#total_users").text(resp.total_user)
    })
    $.get('/api/stat/total-sales', function (resp) {
        $("#total_sales").text(resp.total_sale)
    })
    $.get('/api/stat/product/latest', function (resp) {
        $('#tbl_latest_products').append('<tr>\n' +
            '                                    <td>\n' +
            '                                        <div class="d-flex px-2 py-1">\n' +
            '                                            <div>\n' +
            '                                                <img src="'+resp.prod_picture+'" class="avatar avatar-sm me-3" alt="xd">\n' +
            '                                            </div>\n' +
            '                                            <div class="d-flex flex-column justify-content-center">\n' +
            '                                                <h6 class="mb-0 text-sm" onclick="goToProduct('+resp.prod_id+')">'+ resp.prod_name +'</h6>\n' +
            '                                            </div>\n' +
            '                                        </div>\n' +
            '                                    </td>\n' +
            '                                    <td>\n' +
            '                                        <div class="avatar-group mt-2">\n' +
            '                                            '+ resp.cat_name +
            '                                        </div>\n' +
            '                                    </td>\n' +
            '                                    <td class="align-middle text-center text-sm">\n' +
            '                                        <span class="text-xs font-weight-bold">€ '+ resp.prod_price / 100 +' </span>\n' +
            '                                    </td>\n' +
            '                                    <td>\n' +
            '                                     ' + resp.total_sales +
            '                                    </td>\n' +
            '                                </tr>');
    })

    $.get('/api/stat/claims/latest', function (resp) {
        $("#timeline").append('<div class="timeline-block mb-3">\n' +
            '                  <span class="timeline-step">\n' +
            '                    <i class="ni ni-bell-55 text-success text-gradient"></i>\n' +
            '                  </span>\n' +
            '                                <div class="timeline-content" onclick="goToClaim('+resp.claim_id+')">\n' +
            '                                    <h6 class="text-dark text-sm font-weight-bold mb-0">'+resp.f_name +' '+ resp.l_name +'</h6>\n' +
            '                                    <h6 class="text-dark text-sm font-weight-bold mb-0">'+resp.prize_name+'</h6>\n' +
            '                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">'+resp.claim_date+'</p>\n' +
            '                                </div>\n' +
            '                            </div>');
    })
});

function goToClaim(id)
{
    window.location = '/pages/claims/detail/'+id;
}
function goToProduct(id)
{
    window.location = '/pages/product/detail/'+id;
}