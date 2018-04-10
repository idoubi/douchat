
$(function () {
   
})



function QueryString(qs) {
    var s = location.href.toLowerCase();
    qs = qs.toLowerCase();
    s = s.replace("?", "?&").split("&");
    var re = "";
    for (i = 1; i < s.length; i++)
        if (s[i].indexOf(qs + "=") == 0)
            re = s[i].replace(qs + "=", "");
    return re;
};



Date.prototype.format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}




function loadUserLogin() {
    $.ajax({
        cache: false,
        url: "/account/islogin", success: function (data) {
          
            if (data != "") {
                $("#t_user").addClass("logined").attr("href", "/my");
                if (data.NewMsgCount > 0)
                    $("#t_user").append("<a href='/my/msgs' class='new-msg animated bounceIn'>" + data.NewMsgCount + "</a>");
            }
        }
    })
};



function toDate(value) {
    if (value == undefined) {
        return "";
    }
    /*json格式时间转js时间格式*/
    value = value.substr(1, value.length - 2);
    var obj = eval('(' + "{Date: new " + value + "}" + ')');
    var dateValue = obj["Date"];
    if (dateValue.getFullYear() < 1900) {
        return "";
    }

    return dateValue.format("yyyy-MM-dd hh:mm");
}

function cookie_queue_add(name, value) {
    var array = new Array();
    var items = $.cookie(name);
    if (items == null)
        items = "";
    items = items.split(',');
    array.push(value);
    for (var i = 0; i < items.length; i++) {
        if (items[i] != value)
            array.push(items[i]);
    }
    $.cookie(name, array.join(','), { path: "/", expires: 365 });
};
var searchTime;
function goLogin() {
    var url = "";
    if (window.location.href != undefined)
        url = window.location.href;
    window.location.href = "/account/login?callbackurl=" + url;
};

function search() {
    var k = $.trim($(".searchBox-input").val());
    if (k.length == 0)
        window.location.href = "/so/";
    else
        window.location.href = "/so/s?t=" + $(".searchBox-input").attr("c") + "&key=" + escape(k);
};

$.fn.waterFallList = function (settings) {
    var elements = this;
    var defaultSettings = {
        url: "",
        id: 1,
        pageSize: 0,
        firstPageSize: 0,
        showLoading: true,
        loaded: null
    }
    settings = $.extend(defaultSettings, settings);
    var isFirst = true;
    function first() {
     
        load_data();
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var scrollHeight = $(document).height();
            var windowHeight = $(this).height();
            if (elements.attr("loadedall") != "1" && elements.attr("loading") != "1") {
                if ((scrollTop + windowHeight) > (scrollHeight - 70)) {
                    load_data();
                }
            }
            $("#pageTop" + settings.id).val(scrollTop);
        });
       
    }

    function load_data() {
        var page =parseInt($("#page" + settings.id).val());
        var top = parseInt($("#pageTop" + settings.id).val());
        var _pageSize = settings.pageSize;
        var _page = page;
        if (isFirst) {
            if (page >0) {
                _page = 0;
                _pageSize = settings.pageSize * page;
            }
        }
      
        _page++;
        if(settings.showLoading)
            elements.append('<div class="list_loading"><div class="ll_box"><div class="loadingAn"></div><div class="ll_txt">更多数据加载中</div></div></div>');
        elements.attr("loading", "1");
        $.ajax({
            cache: false,
            url: settings.url + (settings.url.indexOf('?') > 0 ? "&page=" + _page : "?page=" + _page) + "&pageSize=" + _pageSize, success: function (r) {
                if (r.status) {
                    // r = $.parseJSON(r);
                    
                    for (var i = 0; i < r.data.length; i++) {
                        var data = r.data[i];
                        if(settings.loaded!=undefined)
                            settings.loaded(data);
                        if (isFirst && page>0)
                            _page =page;
                        $("#page" + settings.id).val(_page);

                    }
                    if (r.data.length < settings.pageSize || (r.data.length == 1 && r.data[0] == ""))
                        elements.attr("loadedall", "1");
                }
                else
                    alert(data.error);
                if (isFirst)
                    scrollTo(0, $("#pageTop" + settings.id).val());
                elements.attr("loading", "");
                elements.find(".list_loading").remove();
                isFirst = false;
            }
        })
    }
    first();
};


function viewBigImg(id) {
    $("#" + id + " .bigImgView").click(function () {
        $("#viewBigImg .swipe-wrap").html("");
        var total = $("#" + id + " .bigImgView").length;
        var cur = $(this).index() + 1;
        $("#" + id + " .bigImgView").each(function () {
            $("#viewBigImg .swipe-wrap").append("<div><img src='" + $(this).find("img").attr("bigimg") + "'/></div>");
        })
        $("#viewBigImage .total").html(total);
        $("#viewBigImage .cur").html(cur);
        $("#viewBigImagebg,#viewBigImage").show();
        var focus = new Swipe(document.getElementById('viewBigImg'), {
            speed: 400,
            stopPropagation: true,
            continuous: false,
            startSlide: cur - 1,
            callback: function (pos) {
                $("#viewBigImage .cur").html(pos + 1);
            }
        });
    })
}

function viewBigImg_close() {
    $("#viewBigImagebg,#viewBigImage").hide();
}


function check_mobilePhone(str) {
    //var f = /^[1]+[3,4,5,7,8]+\d{9}$/;
    var f = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    return f.test(str);
};

function check_tel(str) {
    var f = /^([0-9]{3,4}-)?[0-9]{7,8}|1\d{10}$/;
    return f.test(str);
};

function check_length(str, min, max) {
    if (str.length < min || str.length > max)
        return false;
    return true;
};

function check_int(str, min, max) {
    if (str < min || str > max)
        return false;
    return true;
};

var parserDate = function (date) {
    var t = Date.parse(date);
    if (isNaN(t) ) {
        return new Date(Date.parse(date.replace(/-/g, "/")));
    } else {
        return new Date(date);
    }
};

function show_mask(object, index,fun) {
    if ($("body").find(".maskk").length > 0) {
        if( $("[holdMask='true']").length==0)
            $("body").find(".maskk").remove();
    }
    else {
        object.append("<div class='mask maskk' style='" + (index != undefined ? "z-index:" + index : "") + "'></div>");
        object.find(".maskk").height(object.height());
        $(".maskk").show();
        if (fun != undefined)
            $(".maskk").click(function () { fun() });
      
    }
}

function custom_class_more(e) {
    var c = $(e).closest(".c-c");
    if (c.attr("going") == "true")
        return;
    c.attr("going", "true");
    if (c.find(".c-c-expand").is(":hidden")) {
        c.find(".c-c-expand").show().addClass('animated bounceInDown2');
        $(e).attr("holdMask", "true");
        show_mask($("body"), 99);
        setTimeout(function () {
            c.attr("going", "");
        }, 400);
    } else {
        c.find(".c-c-expand").removeClass('animated bounceInDown2');
        c.find(".c-c-expand").addClass('animated slideOutUp');
        $(e).attr("holdMask", "");
        show_mask($("body"));
        setTimeout(function () {
            c.find(".c-c-expand").removeClass('animated slideOutUp').hide();
            c.attr("going", "");

        }, 400);
    }

};

var toastTime = null;
$.toast = function (msg,type, css,closeTime) {
    $(".cus_toast").remove();
    var icon = '';
    if (type == "ok")
        icon = "&#xe603;";
    else if (type == "error")
        icon = "&#xe60a;";
    var $toast = $('<div class="cus_toast cus_toast_' + type + '" style="' + (css || '') + '">'+((type!=undefined && type!="")?'<i class="iconfont i_'+type+'">'+icon+'</i>':"")+'<div class="txt">' + msg + '</div></div>').appendTo(document.body);
    if (toastTime != null)
        clearTimeout(toastTime);
    toastTime = setTimeout(function () {
        $(".cus_toast").remove();
    }, closeTime || 3000)
    
};

$.loading = function (msg, css) {
    $(".cus_loading").remove();
    var $toast = $('<div class="cus_loading style="' + (css || '') + '"><div class="cus_spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' +((msg!="" && msg!=undefined)?'<div class="txt">' + msg + '</div></div>':"")).appendTo(document.body);
};

$.loading_hide = function (msg, css) {
    $(".cus_loading").remove();
}


$.confirm = function (msg, css,okCallBack, cancelCallBack) {
    $(".cus_confirm").remove();
    
    var $toast = $('<div class="cus_confirm_wrap"><div class="mask" style="display:block"></div><div class="cus_confirm style="' + (css || '') + '">' + '<div class="txt">' + msg + '</div><div class="ft"><a href="javascript:;" onclick="">确定</a><a href="javascript:;" onclick="">取消</a></div></div></div>').appendTo(document.body);
    var o = $(".cus_confirm_wrap");
    o.find("a").eq(0).click(function () {
        o.remove()
        okCallBack();
    });
    o.find("a").eq(1).click(function () {
        o.remove();
     
        if (cancelCallBack != undefined)
            cancelCallBack();
    });

};


$.actionsheet = function (id) {
    var wrap = $("#" + id);
    var o = wrap.find(".cus_actionsheet");
    if (o.hasClass("cus_actionsheet_toggle")) {
        wrap.find(".mask").fadeOut();
        o.removeClass("cus_actionsheet_toggle");
    } else {
        wrap.show();
        var mask = wrap.find(".mask");
        mask.show();
        mask.on('click', function () {
            hide();
        });
        o.addClass("cus_actionsheet_toggle");
        function hide() {
            mask.fadeOut();
            o.removeClass("cus_actionsheet_toggle");
        }
    }
}

$.actionsheet_hide = function (id) {
    var wrap = $("#" + id);
    wrap.show();
    var o = wrap.find(".cus_actionsheet");
    var mask = wrap.find(".mask");
    mask.fadeOut();
    o.removeClass("cus_actionsheet_toggle");
};

function fav(type, id, callback) {
    $.ajax({
        cache: false,
        url: "/functions/fav?type=" + type + "&id=" + id, success: function (data) {
            if (data.status) {
                $.toast('收藏成功');
                if (callback != undefined)
                    callback();
            }
            else
                $.toast(data.error);
        }
    })
};

function unfav(type, id, callback) {
    $.ajax({
        cache: false,
        url: "/functions/unfav?type=" + type + "&id=" + id, success: function (data) {
            if (data.status) {
                $.toast('取消收藏成功');
                if (callback != undefined)
                    callback();
            }
            else
                $.toast(data.error);
        }
    })
};

function showSearchPage(initType) {
    $('#s-p').toggle();
    if (!$('#s-p').is(".visible")) {
        $("#s-p .s-tab div[v='"+initType+"']").click();
    }
}
function scroll(obj, count) {
    if (count == undefined)
        count = 1;
    var $self = obj.find("ul:first");
    var lineHeight = $self.find("li:first").height();
    for (var i = 0; i < count; i++) {
        $self.find("li").eq(i).clone().appendTo($self);
    }
    $self.animate({ "margin-top": -lineHeight + "px" }, 1000, function () {
        for (var i = 0; i < count; i++) {
            $self.find("li:first").remove();
        }
        $self.css({ "margin-top": "0px" })
    })
}

function isWeiXin() {
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}

function weixinReload() {
    if (isWeiXin) {
        var url = window.location.href;
        var ran = 10000 * Math.random();
        if (QueryString("grand")) {
            var re = eval('/(grand=)([^&]*)/gi');
            url = url.replace(re, 'grand=' + ran);
        }
        else {
            url+=(url.indexOf('?')>-1?"&":"?")+ "grand="+ran
        }
        window.location.href =url;
    }
    else
        window.location.reload();
}

$.fn.defaultsEmpty = function (settings) {
    var els = this;
    var defaultSettings = {
        css: "",
        linkTxt: "",
        link:"",
    }
    settings = $.extend(defaultSettings, settings);
    if (els.find("li,a").length == 0) {
        els.append("<div class='default-empty " + settings.css + "'>" + (settings.linkTxt != '' ? "<a href='" + settings.link + "'>" + settings.linkTxt + "</a>" : "") + "</div>");
      
    }
}

$.fn.dropmenu = function (settings) {
    var elements = this;
    var defaultSettings = {
        info: '',
        opacity: 0,
        clickClose: true,
        autoCloseTime: 1500,
        updateInfo: '',
        css: "",
        callBack: null,
        showError: false,
    }
    function init() {
        elements.find(".cus-dropmenu-box a").width(100 / elements.find(".cus-dropmenu-box a").length + "%");
        elements.find(".cus-dropmenu-box a").click(function () {
            if ($(this).hasClass("on")) {
                out();
                return;
            }
            elements.addClass("exp");
            $(this).siblings().removeClass("on");
            $(this).addClass("on");
            elements.find(".cus-dropmenu-menu").hide();
            $("#" + $(this).data("menu")).show();

           
            if ($(".mask[type='select']").length == 0) {
                $("body").append("<div class='mask' style='display:block;z-index:1000;position:fixed;background:rgba(0,0,0,0.8)' type='select'></div>");
                $(".mask[type='select']").click(function(){
                    out();
                })
            }
            refreshUI();
           
        })
        elements.find(".sec-class").each(function () {
            $(this).find("div").eq(0).find("a").click(function () {
                $(this).closest(".sec-class").find("p").hide();
                $(this).closest(".sec-class").find("p[parentId='" + $(this).attr("fid") + "']").show();
                $(this).addClass("sel");
                $(this).siblings().removeClass("sel");
                refreshUI();
            })
        });
        elements.find(".cus-dropmenu-box").show();
        
    }

    function refreshUI() {
        var div = elements.find(".sec-class").find("div");
        div.height("auto");
        var h = div.eq(0).outerHeight();
        if (div.eq(1).outerHeight() > h)
            h = div.eq(1).outerHeight();
        div.eq(0).height(h);
        div.eq(1).height(h);
     
    }

    function out() {
        elements.find(".cus-dropmenu-box a").removeClass("on");
        elements.find(".cus-dropmenu-menu").hide();
        $(".mask[type='select']").remove();
        elements.removeClass("exp");
    }
    init();
}

function cus_dropmenu_set(select, id) {
    var item = $("#" + $("#" + select).data("menu")).find("a[sid='" + id + "']");
    item.addClass("sel");
    if (item.parent().is("p")) {
        item.closest(".cus-dropmenu-menu").find("a[fid='" + item.parent().attr("parentId") + "']").click();
    }

}


$.fn.select= function (settings) {
    var elements = this;
    var defaultSettings = {
        css: "",
    }
    function init() {
        elements.hide();
        $div = $("<div class='cus-select'>" + elements.find("option:selected").text()+ "</div>");
        elements.after($div);
        var s = "<div class='cus-select-list-box' oid='"+elements.attr("id")+"'><div class='cus-select-list'>";
        elements.find("option").each(function(){
            s += "<a href='javascript:;' title='" + $(this).val() + "'>" + $(this).text() + "</a>";
        })
        s += "</div><div class='mask'></div></div>";
        $list = $(s);
        elements.after($list);
        $list.find("a").click(function(){
            sel($(this));
        })
        
        $list.find("a[title='" + elements.val() + "']").addClass("sel");
        $div.click(function () {
            show(this);
        })
    }

    function show(e) {
        var wrap = $(e).prev();
        var o = wrap.find(".cus-select-list");
        if (o.hasClass("cus-select-lis-toggle ")) {
            wrap.find(".mask").fadeOut();
            o.removeClass("cus-select-lis-toggle ");
        } else {
            wrap.show();
            var mask = wrap.find(".mask");
            mask.show();
            mask.on('click', function () {
                hide();
            });
            o.addClass("cus-select-lis-toggle");
            function hide() {
                mask.fadeOut();
                o.removeClass("cus-select-lis-toggle ");
            }
        }

    }

    function sel(e) {
        $select = $("#" + e.closest(".cus-select-list-box").attr("oid"));
        var val = e.attr("title");
        $select.val(val);
        e.closest(".cus-select-list").removeClass("cus-select-lis-toggle ");
        e.closest(".cus-select-list-box").find(".mask").fadeOut();
        e.closest(".cus-select-list-box").next().html(e.text());
        e.closest(".cus-select-list").find("a").removeClass("sel");
        e.addClass("sel");
       
    }


    init();
}



//function killerrors() {
//    return true;
//}
//window.onerror = killerrors;