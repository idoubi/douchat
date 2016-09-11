var hash = 2;
var menu = {
    button : {

    }
};

$(function() {
    $.ajax({
        url : get_menu_url,
        type : 'post',
        dataType : 'json',
        data : {

        },
        success : function(result) {
            if (result.errcode == 0) {
                show_menu(result.data);
            }
        },
        error : function() {
            console.log('error');
        }
    });
    $( ".nav-group" ).sortable({
        items : 'li.js-sortable',
        axis : 'x',
        cancel: '.js-not-sortable'
    });
    $('.designer-y').sortable({
        items: 'dd',
        axis: 'y',
        cancel: '.js-not-sortable'
    });

    $('#menu_info').hide();

    $('#btn-submit').on('click', function() {
        $.ajax({
            url : create_menu_url,
            type : 'post',
            dataType : 'json',
            data : {
                menu : menu
            },
            success : function(result) {
                if (result.errcode == 0) {
                    alert('发布自定义菜单成功');
                } else {
                    alert(result.errmsg);
                    var err_btn = $('*[data-id="'+result.data_id+'"]').find('a[onclick^="selectMenu"]');
                    selectMenu(err_btn);
                    return false;
                }
            },
            error : function() {
                alert('提交数据错误');
            }
        });
    });
    
    $('.add-button').on('click', function() {
        addMenu($(this));
    });

    $('#menu_info input[name=menu_name]').on({
        keydown : function() {
            name_change($(this));
        },
        keyup : function() {
            name_change($(this));
        },
        keypress : function() {
            name_change($(this));
        },
        blur : function() {
            name_change($(this));
        }
    });

    $('.menu_act input').on({
        keydown : function() {
            act_change($(this));
        },
        keyup : function() {
            act_change($(this));
        },
        keypress : function() {
            act_change($(this));
        },
        blur : function() {
            act_change($(this));
        }
    });

    // 重新编辑菜单
    $('.edit_menu').on('click', function() {
        empty_menu();           // 清空菜单
        return false;
    });

    // 拉取菜单
    $('.pull_menu').on('click', function() {
        $.ajax({
            url : get_menu_url,
            type : 'post',
            dataType : 'json',
            data : {

            },
            success : function(result) {
                if (result.errcode == 0) {
                    empty_menu();           // 清空菜单
                    show_menu(result.data);
                }
            },
            error : function() {
                console.log('error');
            }
        });
        return false;
    });

    // 删除菜单
    $('.delete_menu').on('click', function() {
        if (confirm('此操作将会删除微信端已经生效的自定义菜单，是否确认要删除？')) {
            $.ajax({
                url : delete_menu_url,
                type : 'post',
                dataType : 'json',
                data : {

                },
                success : function(result) {
                    if (result.errcode == 0) {
                        alert(result.errmsg);
                        empty_menu();           // 清空菜单
                    } else {
                        alert(result.errmsg);
                    }
                },
                error : function() {
                    console.log('error');
                }
            });
        } 
        return false;
    });

    $('#deleteMenu').on('click', function() {
        var data_id = $('#menu_info').attr('data-id');
        var data_type = $('#menu_info').attr('data-type');
        if (data_type == 'button') {
            if (confirm('确定要删除此菜单及其子菜单？')) {
                var button = $('.nav-group-item[data-id="'+data_id+'"]');
                menu['button'][data_id] = null;
                button.remove();
                if ($('.nav-group-item').length == 1) {
                    $('.nav-menu-wx').attr('class', 'js-quickmenu nav-menu-wx clearfix has-nav-'+$('.nav-group-item').length);
                    $('#menu_info').attr('data-id', '');
                    $('#menu_info').attr('data-type', '');
                    $('#menu_info').find('input[name=menu_name]').val('');
                    $('#menu_info').hide();
                    return;
                }
                var button_item = $('.nav-group-item');
                $('#menu_info').attr('data-id', button_item.first().attr('data-id'));
                $('#menu_info').attr('data-type', button_item.first().attr('data-type'));
                $('#menu_info').find('input[name=menu_name]').val(button_item.first().attr('data-name'));
                button_item.first().addClass('active');
                if (button_item.length < 3 && $('.add-button').length == 0) {
                    var add_item = '<li class="nav-group-item js-not-sortable ng-scope add-button" onclick="addMenu(this)">'+
                                        '<a href="javascript:void(0);" title="拖动排序" class="ng-binding">'+
                                            '<i class="fa fa-plus-circle"></i>'+
                                            '添加菜单'+
                                        '</a>'+
                                    '</li>';
                    button_item.last().after(add_item);
                }
                $('.nav-menu-wx').attr('class', 'js-quickmenu nav-menu-wx clearfix has-nav-'+$('.nav-group-item').length);
            }
        } else {
            var sub_button = $('.sub-button[data-id="'+data_id+'"]');
            button = sub_button.parent().parent();
            menu['button'][button.attr('data-id')]['sub_button'][data_id] = null;
            sub_button.remove();
            $('#menu_info').attr('data-id', button.attr('data-id'));
            $('#menu_info').attr('data-type', button.attr('data-type'));
            button.addClass('active');
            if (button.find('.sub-button').length < 5 && button.find('.add-sub-button').length == 0) {
                var add_item = '<dd class="js-not-sortable ng-scope add-sub-button" onclick="addSubMenu(this)"><a href="javascript:void(0)"><i class="fa fa-plus"></i></a></dd>';
                button.find('.sub-button').last().after(add_item);
            }
            $('#menu_info').find('input[name=menu_name]').val(button.attr('data-name'));
        }
    });

    $('.radio-inline').on('click', function() {
        var _this = $(this);
        var data_id = $('#menu_info').attr('data-id');
        var data_type = $('#menu_info').attr('data-type');
        var type = _this.find('input[name=menu_type]').val();
        if (data_type == 'sub_button') {
            var sub_button = $('.sub-button[data-id="'+data_id+'"]');
            button = sub_button.parent().parent();
            var parent_id = button.attr('data-id');
            menu['button'][parent_id]['sub_button'][data_id]['type'] = type;
            var menu_key = menu['button'][parent_id]['sub_button'][data_id]['key'];
            var menu_url = menu['button'][parent_id]['sub_button'][data_id]['url'];
        } else {
            menu['button'][data_id]['type'] = type; 
            var menu_key = menu['button'][data_id]['key'];
            var menu_url = menu['button'][data_id]['url'];
        }
        $('.menu_act input').val('');
        if (type == 'view') {
            $('.menu_act input').attr('placeholder', '请输入链接地址').val(menu_url);
            $('.menu_act .help-block').html('格式例如：http://baidu.com/');
        } else if (type == 'click') {
            $('.menu_act input').attr('placeholder', '请填写关键词').val(menu_key);
            $('.menu_act .help-block').html('');
        }
        $('.menu_act').show();

    });

    $('.iCheck-helper').on('click', function() {
        var _this = $(this);
        var data_id = $('#menu_info').attr('data-id');
        var data_type = $('#menu_info').attr('data-type');
        var type = _this.siblings('input[name=menu_type]').val();
        if (data_type == 'sub_button') {
            var sub_button = $('.sub-button[data-id="'+data_id+'"]');
            button = sub_button.parent().parent();
            var parent_id = button.attr('data-id');
            menu['button'][parent_id]['sub_button'][data_id]['type'] = type;
            var menu_key = menu['button'][parent_id]['sub_button'][data_id]['key'];
            var menu_url = menu['button'][parent_id]['sub_button'][data_id]['url'];
        } else {
            menu['button'][data_id]['type'] = type; 
            var menu_key = menu['button'][data_id]['key'];
            var menu_url = menu['button'][data_id]['url'];
        }
        $('.menu_act input').val('');
        if (type == 'view') {
            $('.menu_act input').attr('placeholder', '请输入链接地址').val(menu_url);
            $('.menu_act .help-block').html('格式例如：http://baidu.com/');
        } else if (type == 'click') {
            $('.menu_act input').attr('placeholder', '请填写关键词').val(menu_key);
            $('.menu_act .help-block').html('');
        }
        $('.menu_act').show();

    });
});

// 清空菜单
function empty_menu() {
    var btn_html = '<ul class="nav-group designer-x ui-sortable"><li class="nav-group-item js-not-sortable ng-scope add-button" onclick="addMenu(this)"><a href="javascript:void(0);" title="拖动排序" class="ng-binding"><i class="fa fa-plus-circle"></i>添加菜单</a></li></ul>';
    $('.nav-menu-wx').html(btn_html).attr('class', 'js-quickmenu nav-menu-wx clearfix has-nav-1');
    menu = {
        button : {

        }
    };
}

// 显示菜单
function show_menu(data) {
    if (!data) {
        return false;
    }
    var btn = data['button'];                   // 一级菜单数组
    if (btn) {
        var btn_len = btn.length;                   // 一级菜单个数   
    } else {
        var btn_len = 0;                   // 一级菜单个数   
    }
    
    if (btn_len >= 3) {
        btn_len = 3;                            // 限制最多三个一级菜单
        nav_count = 3;
    } else {
        nav_count = btn_len+1;                  // 按钮个数
    }
    var add_btn = $('.add-button');
    $('.nav-menu-wx').attr('class', 'js-quickmenu nav-menu-wx clearfix has-nav-'+nav_count);
    for (var i=0; i<btn_len; i++) {
        var sub_btn = btn[i]['sub_button'];
        if (sub_btn) {
            var sub_btn_len = sub_btn.length;
        } else {
            var sub_btn_len = 0;
        }
        btn_index = hash;
        menu['button'][btn_index] = {
            id : '',
            name : '',
            type : '',
            key : '',
            url : '',
            sub_button : {}
        };
        if (sub_btn_len == 0) {     // 没有二级菜单的一级菜单
            var btn_item = '<li class="nav-group-item js-sortable ng-scope ui-sortable-handle" id="btn_'+btn_index+'" data-id="'+btn_index+'" data-name="'+btn[i]['name']+'" data-type="button"><a href="javascript:void(0);" title="拖动排序" onclick="selectMenu(this)" class="ng-binding">'+btn[i]['name']+'</a><dl class="designer-y ui-sortable"><dd class="js-not-sortable ng-scope add-sub-button" onclick="addSubMenu(this)"><a href="javascript:void(0)"><i class="fa fa-plus"></i></a></dd></dl></li>';    
            add_btn.before($(btn_item));
            menu['button'][btn_index]['name'] = btn[i]['name'];
            menu['button'][btn_index]['type'] = btn[i]['type'];
            if (btn[i]['type'] == 'view') {
                menu['button'][btn_index]['url'] = btn[i]['url'];
            } else if (btn[i]['type'] == 'click') {
                menu['button'][btn_index]['key'] = btn[i]['key'];
            }
            hash += 2;
        } else {                    // 有二级菜单的一级菜单
            var btn_item = '<li class="nav-group-item js-sortable ng-scope ui-sortable-handle" id="btn_'+btn_index+'" data-id="'+btn_index+'" data-name="'+btn[i]['name']+'" data-type="button"><a href="javascript:void(0);" title="拖动排序" onclick="selectMenu(this)" class="ng-binding"><i class="fa fa-minus-circle"></i>'+btn[i]['name']+'</a><dl class="designer-y ui-sortable"><dd class="js-not-sortable ng-scope add-sub-button" onclick="addSubMenu(this)"><a href="javascript:void(0)"><i class="fa fa-plus"></i></a></dd></dl></li>';    
            add_btn.before($(btn_item));
            menu['button'][btn_index]['name'] = btn[i]['name'];
            menu['button'][btn_index]['type'] = btn[i]['type'];
            if (btn[i]['type'] == 'view') {
                menu['button'][btn_index]['url'] = btn[i]['url'];
            } else if (btn[i]['type'] == 'click') {
                menu['button'][btn_index]['key'] = btn[i]['key'];
            }
            hash += 2;
            if (sub_btn_len >= 5) {
                sub_btn_len = 5;
            }
            var add_sub_btn = $('.nav-group-item[data-id="'+btn_index+'"]').find('.add-sub-button');
            for (var j=0; j<sub_btn_len; j++) {
                var sub_btn_index = hash;
                menu['button'][btn_index]['sub_button'][sub_btn_index] = {
                    id : '',
                    name : '',
                    type : '',
                    key : '',
                    url : '',
                    sub_button : {}
                };
                var sub_btn_item = '<dd class="sub-button js-sortable ng-scope" id="sub_btn_'+sub_btn_index+'" data-id="'+sub_btn_index+'" data-name="'+sub_btn[j]['name']+'" data-type="sub_button"><a href="javascript:void(0)" onclick="selectMenu(this)" class="ng-binding">'+sub_btn[j]['name']+'</a></dd>';
                add_sub_btn.before(sub_btn_item);
                menu['button'][btn_index]['sub_button'][sub_btn_index]['name'] = sub_btn[j]['name'];
                menu['button'][btn_index]['sub_button'][sub_btn_index]['type'] = sub_btn[j]['type'];
                if (sub_btn[j]['type'] == 'view') {
                    menu['button'][btn_index]['sub_button'][sub_btn_index]['url'] = sub_btn[j]['url'];
                } else if (sub_btn[j]['type'] == 'click') {
                    menu['button'][btn_index]['sub_button'][sub_btn_index]['key'] = sub_btn[j]['key'];
                }
                hash += 2;
            }
            if (sub_btn_len >= 5) {
                add_sub_btn.remove();
            }
        }
    }
    if (btn_len >= 3) {
        add_btn.remove();
    }
}

function name_change(ele) {
    var _this = $(ele);
    var menu_name = _this.val();
    var data_id = $('#menu_info').attr('data-id');
    var data_type = $('#menu_info').attr('data-type');
    if (data_type == 'button') {
        var btn = $('.nav-group-item[data-id="'+data_id+'"]').find('a').first();
        if (btn.find('i').length == 0) {
            btn.text(menu_name);
        } else {
            btn.html('<i class="fa fa-minus-circle"></i>'+menu_name);
        }
        menu['button'][data_id]['name'] = menu_name;
    } else if(data_type == 'sub_button') {
        $('.sub-button[data-id="'+data_id+'"]').find('a').html(menu_name);
        var parent_data_id = $('.sub-button[data-id="'+data_id+'"]').parent().parent().attr('data-id');
        menu['button'][parent_data_id]['sub_button'][data_id]['name'] = menu_name;
    }
}

function act_change(ele) {
    var _this = $(ele);
    var menu_type = $('.radio-inline .checked input[name=menu_type]').val();
    var data_id = $('#menu_info').attr('data-id');
    var data_type = $('#menu_info').attr('data-type');
    if (data_type == 'button') {
        if (menu_type == 'view') {
            menu['button'][data_id]['url'] = _this.val();
        } else if (menu_type == 'click') {
            menu['button'][data_id]['key'] = _this.val();
        }            
    } else if(data_type == 'sub_button') {
        var parent_data_id = $('.sub-button[data-id="'+data_id+'"]').parent().parent().attr('data-id');
        if (menu_type == 'view') {
            menu['button'][parent_data_id]['sub_button'][data_id]['url'] = _this.val();
        } else {
            menu['button'][parent_data_id]['sub_button'][data_id]['key'] = _this.val();
        }  
    } 
}

// 添加一级按钮
function addMenu(ele) {
    $('input[name=menu_type]').parent().removeClass('checked');
    var _this = $(ele);
    var item_num = _this.siblings().length;
    // var data_id = item_num+1;
    var data_id = hash;
    hash += 2;
    var item = '<li class="nav-group-item js-sortable ng-scope active ui-sortable-handle" id="btn_'+data_id+'" data-id="'+data_id+'" data-name="菜单" data-type="button">'+
                    '<a href="javascript:void(0);" title="拖动排序" onclick="selectMenu(this)" class="ng-binding">'+
                        '菜单'+
                    '</a>'+
                    '<dl class="designer-y ui-sortable">'+
                        '<dd class="js-not-sortable ng-scope add-sub-button" onclick="addSubMenu(this)">'+
                            '<a href="javascript:void(0)"><i class="fa fa-plus"></i></a>'+
                        '</dd>'+
                    '</dl>'+
                '</li>';
    $('.nav-group-item').removeClass('active');
    $('.designer-y dd').removeClass('active');
    _this.before(item);
    $('#menu_info').attr('data-id', data_id).attr('data-type', 'button').find('input[name=menu_name]').val('菜单');
    menu['button'][data_id] = {
        id : 'btn_'+data_id,
        name : '菜单',
        type : '',
        key : '',
        url : '',
        sub_button : {}
    };
    $('.menu_act input').attr('placeholder', '').val('');
    $('.menu_act').hide();
    var item_num = _this.siblings().length;
    if (item_num >= 3) {
        _this.remove();
        return;
    }
    $('.nav-menu-wx').attr('class', 'js-quickmenu nav-menu-wx clearfix has-nav-'+(item_num+1));
    $('#menu_info').show();
}

// 添加子按钮
function addSubMenu(ele) {
    $('input[name=menu_type]').parent().removeClass('checked');
    var _this = $(ele);
    var parent_btn = _this.parent().parent();
    var parent_btn_id = parent_btn.attr('data-id');
    var item_num = _this.siblings().length;
    // var data_id = parent_btn_id+'_'+(item_num+1);
    var data_id = hash;
    hash += 2;
    var item = '<dd class="sub-button js-sortable ng-scope active" id="sub_btn_'+data_id+'" data-id="'+data_id+'" data-name="子菜单" data-type="sub_button">'+
                    '<a href="javascript:void(0)" onclick="selectMenu(this)" class="ng-binding">子菜单</a>'+
                '</dd>';
    $('.nav-group-item').removeClass('active');
    var parent_a = parent_btn.find('dl').siblings('a');
    if (parent_a.find('i').length == 0) {
        var parent_a_html = parent_a.html();
        var parent_a_html_new = '<i class="fa fa-minus-circle"></i>'+parent_a_html;
        parent_a.html(parent_a_html_new);
    } 
    $('.sub-button').removeClass('active');
    _this.before(item);
    $('#menu_info').attr('data-id', data_id).attr('data-type', 'sub_button').find('input[name=menu_name]').val('子菜单');
    menu['button'][parent_btn_id]['sub_button'][data_id] = {
        id : 'sub_btn_'+data_id,
        name : '子菜单',
        type : '',
        key : '',
        url : '',
        sub_button : {}
    };
    $('.menu_act input').attr('placeholder', '').val('');
    $('.menu_act').hide();
    $('.extra').show();
    var item_num = _this.siblings().length;
    if (item_num >= 5) {
        _this.remove();
        return;
    }
}

function selectMenu(ele) {
    var _this = $(ele);                                                     // 当前选择的按钮
    var data_id = _this.parent().attr('data-id');                           // 获取当前选择按钮的唯一标识ID
    var data_type = _this.parent().attr('data-type');                       // 当前选择的按钮类型，一级菜单：button，二级菜单：sub_button
    $('.radio-inline .iradio_square-blue').removeClass('checked');          // 移除所有的菜单动作样式
    $('#menu_info').show();                                                 // 显示菜单动作编辑框
    if (_this.siblings().find('dd[data-type=sub_button]').length > 0) {     // 如果当前菜单是一级菜单，且包含二级菜单，则隐藏菜单动作编辑框
        $('.extra').hide();
    } else {
        $('.extra').show();
    }
    if (data_type == 'sub_button') {            // 选择的按钮是二级菜单
        var parent_data_id = _this.parent().parent().parent().attr('data-id');              // 获得一级菜单的标识ID
        var menu_name = menu['button'][parent_data_id]['sub_button'][data_id]['name'];
        var menu_type = menu['button'][parent_data_id]['sub_button'][data_id]['type'];
        var menu_key = menu['button'][parent_data_id]['sub_button'][data_id]['key'];
        var menu_url = menu['button'][parent_data_id]['sub_button'][data_id]['url'];
    } else {
        var menu_name = menu['button'][data_id]['name'];
        var menu_type = menu['button'][data_id]['type'];
        var menu_key = menu['button'][data_id]['key'];
        var menu_url = menu['button'][data_id]['url'];
    }
    $('#menu_info').attr('data-id', data_id).attr('data-type', data_type).find('input[name=menu_name]').val(menu_name);
    if (menu_type == 'view') {
        $('.menu_act input').attr('placeholder', '请输入链接地址').val(menu_url);
        $('.menu_act').show();
    } else if (menu_type == 'click') {
        $('.menu_act input').attr('placeholder', '请填写关键词').val(menu_key);
        $('.menu_act').show();
    } else {
        $('.menu_act input').attr('placeholder', '').val('');
        $('.menu_act').hide();
    }
    $('input[name=menu_type]').attr('checked', false);
    $('.extra .radio-inline input[value="'+menu_type+'"]').parent().addClass('checked');
    $('.extra .radio-inline input[value="'+menu_type+'"]').attr('checked', true);
    $('.nav-group-item').removeClass('active');
    $('.sub-button').removeClass('active');
    _this.parent().addClass('active');
}

