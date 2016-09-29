$(document).on('click', '.ajax-post', function() {
    var data = $('form').serialize();
    var url = $('form').attr('action');
    $.ajax({
        'url' : url,
        'type' : 'post',
        'dataType' : 'json',
        'data' : data,
        success : function(data) {
            console.log(data);
            if (data.status == 1) {
                $('#aw-ajax-box .modal-body p').html(data.info+'，页面即将自动跳转...');
                $('#aw-ajax-box .aw-tips-box,.modal-backdrop').show();
                setTimeout(function() {
                    window.location.href = data.url;
                }, 1500);
            } else {
                $('#aw-ajax-box .modal-body p').html(data.info);
                $('#aw-ajax-box .aw-tips-box,.modal-backdrop').show();
                $('#aw-ajax-box .close,#aw-ajax-box .aw-tips-box').click(function() {
                    $('#aw-ajax-box .aw-tips-box,.modal-backdrop').hide();
                });
            }
        },
        error : function() {
            console.log('error');
        }
    });
    return false;
}).on('click', 'a[href*=delete]', function() {
    if (!confirm('是否确认删除')) {
        return false;
    } else {
        return true;
    }
});
