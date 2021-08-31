const $ = require('jquery');
import 'bootstrap';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
const axios = require('axios');
axios.defaults.headers["X-Requested-With"] = "XMLHttpRequest";

$(document).ready(function(){
    initIfConnected();
});

//===================================
// Security / Init menu
//===================================

function initIfConnected() {
    if (typeof BUILD_KCMS_MENU !== 'undefined' && BUILD_KCMS_MENU === true) {
        let url = '/kcms/is_connected';
        axios.get(url).then(
            (response) => {
                if (response.data.connected) {
                    buildKcmsMenu();
                }
            },
            (response) => {
                console.log('An error has been encountered');
                console.log(response);
            }
        );
    }
}

//===================================
// Libs
//===================================

function initState() {
    let state = {"show": false};
    setZoneState(state);
    setContentState(state);
}

function buildKcmsMenu() {
    // Init menu
    $('.kcms_menu').remove();
    $('body').append(buildKcmsFrontMenu());
    initState();

    // Click on "show zone button"
    $('#kcms_front_menu_show_zones').click(function () {
        let state = getZoneState();
        if (state.show == false) {
            state.show = true;
            setZoneState(state);
            $('body').addClass('kcms_scale_reduce');
            $('body').removeClass('kcms_scale_full');
            $('.kcms_zone').each(function () {
                buildZoneLayer($(this));
            });
        } else {
            state.show = false;
            setZoneState(state);
            $('body').addClass('kcms_scale_full');
            $('body').removeClass('kcms_scale_reduce');
            $('.kcms_zone').each(function () {
                eraseZoneLayer($(this));
            });
        }
    });

    // Click on "show content button"
    $('#kcms_front_menu_show_contents').click(function () {
        let state = getContentState();
        if (state.show == false) {
            state.show = true;
            setContentState(state);
            $('body').addClass('kcms_scale_reduce');
            $('body').removeClass('kcms_scale_full');
            $('.kcms_content').each(function () {
                buildContentLayer($(this));
            });
        } else {
            state.show = false;
            setContentState(state);
            $('body').addClass('kcms_scale_full');
            $('body').removeClass('kcms_scale_reduce');
            $('.kcms_content').each(function () {
                eraseContentLayer($(this));
            });
        }
    });
}

function setZoneState(state) {
    $('#kcms_front_menu_show_zones').data('state', JSON.stringify(state));
}

function getZoneState() {
    return JSON.parse($('#kcms_front_menu_show_zones').data('state'));
}

function setContentState(state) {
    $('#kcms_front_menu_show_contents').data('state', JSON.stringify(state));
}

function getContentState() {
    return JSON.parse($('#kcms_front_menu_show_contents').data('state'));
}

function buildKcmsFrontMenu() {
    return `
<div class="kcms_menu">
    <input type="button" value="zones" id="kcms_front_menu_show_zones" data-state="">
    <input type="button" value="contents" id="kcms_front_menu_show_contents" data-state="">
    <a href="/kcms/admin/edit_page/`+KCMS_PAGE_ID+`" target="_blank"><input type="button" value="Edit this page" id="kcms_front_menu_goto_admin_edit_page" data-state=""></a>
    <a href="/kcms/admin" target="_blank"><input type="button" value="admin" id="kcms_front_menu_goto_admin" data-state=""></a>
</div>
    `;
}

function buildZoneLayer(zoneObj) {
    let height = zoneObj.height();
    let width = zoneObj.width();
    let position = zoneObj.position();

    zoneObj.append('<div class="kcms_zone_layer" style="top: '+position.top+'px; left: '+position.left+'px; height:'+height+'px; width:'+width+'px; min-height: 28px;"></div>');
    zoneObj.append('<div class="kcms_zone_nb" style="top: '+position.top+'px; left: '+position.left+'px;">Zone # '+zoneObj.data("zone")+'</div>');
}

function eraseZoneLayer(zoneObj) {
    $('.kcms_zone_layer').each(function(){
        $(this).remove();
    });
    $('.kcms_zone_nb').each(function(){
        $(this).remove();
    });
}

function buildContentLayer(contentObj) {
    let height = contentObj.height();
    let width = contentObj.width();
    let position = contentObj.position();

    let editionUrl = '/kcms/admin/edit_content/' + contentObj.data("content-id");

    contentObj.append('<div class="kcms_content_layer" style="top: '+position.top+'px; left: '+position.left+'px; height:'+height+'px; width:'+width+'px; min-height: 28px;"></div>');
    contentObj.append('<div class="kcms_content_nb" style="top: '+position.top+'px; left: '+position.left+'px;">Content # '+contentObj.data("content-id")+'&nbsp;<a href="'+editionUrl+'" target="_blank"><i class="fa fa-edit"></i></a></div>');
}

function eraseContentLayer(contentObj) {
    $('.kcms_content_layer').each(function(){
        $(this).remove();
    });
    $('.kcms_content_nb').each(function(){
        $(this).remove();
    });
}

