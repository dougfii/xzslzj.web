function Redirect(url) {
    window.location = url;
}

function Refresh() {
    window.location.reload();
}

function Location(params) {
    var qs = ((location.search.length > 0) ? location.search.substring(1) : '') + params;
    var args = {};
    var items = qs.length ? qs.split('&') : [], item = null, name = null, len = items.length;
    for (var i = 0; i < len; i++) {
        item = items[i].split('=');
        name = decodeURIComponent(item[0]);
        val = decodeURIComponent(item[1]);
        if (name.length) args[name] = val;
    }

    var qr = '?';
    for (var arg in args) {
        qr += "&" + encodeURIComponent(arg) + "=" + encodeURIComponent(args[arg]);
    }

    Redirect(qr);
}

function GoBack() {
    history.back();
}

function Loading() {
    layer.load("正在加载中...");
}

function Processing() {
    layer.load("正在处理中...");
}

function Uploading() {
    layer.load("正在上传中...");
}

function Export(url) {
    window.location.href = url;
}

function Printer(url) {
    window.open(url, '打印','height=600,width=810,top=0,left=0,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no');
}

function TreeBox(o) {
    var name = $(o).attr('id');
    var obj = $(o);
    var offset = $(o).offset();
    $('#' + name + 'box').css({
        left: offset.left + 'px',
        top: offset.top + obj.outerHeight() + 'px',
        width: obj.width() + 'px'
    }).slideDown('fast');
    $('body').on('mousedown', o, function (event) {
        if (!(event.target.id == name || event.target.id == name + 'box' || $(event.target).parents('#' + name + 'box').length > 0)) {
            $('#' + name + 'box').fadeOut('fast');
            $('body').off('mousedown', o);
        }
    });
}

$(function () {
    $('#openfind').click(function () {
        $('.find').toggle('fast');
    });
    $('.back').click(function () {
        history.back();
    });
    $('.bback').click(function () {
        history.back();
    });
    $('.delattr').click(function(){
        var id=$(this).attr('did');
        layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Project&a=OnUpFlowDelete', {id:id}, function(ret){if(ret.code==1)Refresh();else layer.msg(ret.msg, 2, -1);}, 'json');});
    });
});