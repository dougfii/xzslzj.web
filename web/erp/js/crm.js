function Redirect(url){
	window.location.href = url;
}
function showSalesBox() {
	var obj = $('#sales');
	var offset = $('#sales').offset();
	$('#salesbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', onBodyDownSales);
}
function showAdminBox() {
	var obj = $('#admin');
	var offset = $('#admin').offset();
	$('#adminbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', onBodyDownAdmin);
}
function ShowGroupBox() {
	var obj = $('#group');
	var offset = $('#group').offset();
	$('#groupbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnGroupDown);
}
function HideGroupBox() {
	$('#groupbox').fadeOut('fast');
	$('body').unbind('mousedown', OnGroupDown);
}
function OnGroupDown(event) {
	if (!(event.target.id == 'group' || event.target.id == 'groupbox' || $(event.target).parents('#groupbox').length > 0)) HideGroupBox();
}
function ShowOrgBox() {
	var obj = $('#org');
	var offset = $('#org').offset();
	$('#orgbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnOrgDown);
}
function HideOrgBox() {
	$('#orgbox').fadeOut('fast');
	$('body').unbind('mousedown', OnOrgDown);
}
function OnOrgDown(event) {
	if (!(event.target.id == 'org' || event.target.id == 'orgbox' || $(event.target).parents('#orgbox').length > 0)) HideOrgBox();
}
function ShowUserBox() {
	var obj = $('#user');
	var offset = $('#user').offset();
	$('#userbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnUserDown);
}
function HideUserBox() {
	$('#userbox').fadeOut('fast');
	$('body').unbind('mousedown', OnUserDown);
}
function OnUserDown(event) {
	if (!(event.target.id == 'user' || event.target.id == 'userbox' || $(event.target).parents('#userbox').length > 0)) HideUserBox();
}
function ShowCatalogBox() {
	var obj = $('#catalog');
	var offset = $('#catalog').offset();
	$('#catalogbox').css({left:offset.left + 'px', top:offset.top + obj.outerHeight() + 'px'}).slideDown('fast');
	$('body').bind('mousedown', OnCatalogDown);
}
function HideCatalogBox() {
	$('#catalogbox').fadeOut('fast');
	$('body').unbind('mousedown', OnCatalogDown);
}
function OnCatalogDown(event) {
	if (!(event.target.id == 'catalog' || event.target.id == 'catalogbox' || $(event.target).parents('#catalogbox').length > 0)) HideCatalogBox();
}
function onBodyDownAdmin(event) {
	if (!(event.target.id == "admin" || event.target.id == "adminbox" || $(event.target).parents("#adminbox").length > 0)) {
		hideAdminBox();
	}
}
function onBodyDownSales(event) {
	if (!(event.target.id == "sales" || event.target.id == "salesbox" || $(event.target).parents("#salesbox").length > 0)) {
		hideSalesBox();
	}
}
function hideAdminBox() {
	$('#adminbox').fadeOut('fast');
	$('body').unbind('mousedown', onBodyDownAdmin);
}
function hideSalesBox() {
	$('#salesbox').fadeOut('fast');
	$('body').unbind('mousedown', onBodyDownSales);
}

function ShowApproveCondition() {
	$.post('?m=System&a=ApproveConditionList', function(data){ $('#approvecondition').html(data); });
}

$(function(){
	$(document).ajaxStop(function(){
		$('#ok').attr('disabled',false);
		$('#execute').attr('disabled',false);
		$('#stop').attr('disabled',false);
	});
	
	$('.bback').click(function(){
		history.back();
	});
});