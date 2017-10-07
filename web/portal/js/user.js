function Redirect(url) {
    window.location.href = url;
}

function TreeBox(o) {
    var name = $(o).attr('id');
    var obj = $(o);
    var offset = $(o).offset();
    $('#' + name + 'box').css({left: offset.left + 'px', top: offset.top + obj.outerHeight() + 'px', width: obj.width() + 'px'}).slideDown('fast');
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
    $('.bback').click(function () {
        history.back();
    });
    $('.back').click(function () {
        history.back();
    });
});