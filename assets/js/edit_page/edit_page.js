const $ = require('jquery');
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/widgets/dialog';
import 'jstree/dist/themes/default/style.min.css';
import '../jstree/jstree';
import {init_jstree} from '../jstree/kcms_init_jstree';
const axios = require('axios');
axios.defaults.headers["X-Requested-With"] = "XMLHttpRequest";

$(document).ready(function(){

    $('.draggable_element').draggable({
        handle: "p.draggable_handle",
        revert: "invalid",
        stop: function(event, ui) {
            $(this).draggable({"revert": "invalid"});
        }
    });

    $( ".zone" ).droppable({
        classes: {
            "ui-droppable-hover": "zone_drop_highlight"
        },
        accept: function(dropElem) {
            let initialZone = dropElem.data('initial_zone');
            let targetZone = $(this).data('zone');

            if (targetZone === initialZone) {
                return false;
            }

            return true;
        },
        drop: function( event, content ) {

            let initialZone = content.draggable.data('initial_zone');

            if (initialZone !== undefined) {
                let payload = {};
                payload.pageId = $('#kcms_page').data('page_id');
                payload.pageContentId = content.draggable.data('id');
                payload.zone = $(this).data('zone');

                if (payload.zone !== initialZone && payload.pageContentId !== undefined && payload.zone !== undefined) {

                    let url = '/kcms/admin/ajax/edit_page/change_zone';

                    axios.post(url, payload).then(
                        (response) => {
                            if (response.data.status === 'done') {
                                refreshPage();
                            }
                        },
                        (response) => {
                            console.log('An error has been encountered');
                            console.log(response);
                        }
                    );
                }
            }
        }
    });

    $('.block_page_content_sortable').sortable({
        handle: "p.sortable_handle",
        axis: "y",
        containment: "parent",
        deactivate: function (event, ui) {
            let payload = {};
            payload.pageId = $('#kcms_page').data('page_id');
            payload.zone = $(this).data('initial_zone');
            payload.rank = $(this).sortable('toArray', { attribute: 'data-id' });

            let i = 1;

            $(this).find('.sortable_element').each(function(index, value){
                $(this).find('.page_content_input_rank').val(index+1);
            });

            let url = '/kcms/admin/ajax/edit_page/change_rank';

            axios.post(url, payload).then(
                (response) => {
                    if (response.data.status !== 'done') {
                        console.log('An error has been encountered');
                        console.log(response);
                    }
                },
                (response) => {
                    console.log('An error has been encountered');
                    console.log(response);
                }
            );

        }
    });

    $('.delete-page_content').click(function(){

        let pageContent = $(this).parent('.page_content').data('id');

        $( "#dialog-confirm" ).html('Are you sure you want to delete this content ?');

        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "Ok": function() {
                    let payload = {};
                    payload.pageId = $('#kcms_page').data('page_id');
                    payload.pageContentId = pageContent;

                    let url = '/kcms/admin/ajax/edit_page/delete_pagecontent';

                    axios.post(url, payload).then(
                        (response) => {
                            if (response.data.status === 'done') {
                                refreshPage();
                            }
                        },
                        (response) => {
                            console.log('An error has been encountered');
                            console.log(response);
                        }
                    );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });

    $('.zone_add_pageContent').click(function(){
        let zone = $(this).parent('.zone').data('zone');
        let payload = {};
        payload.context = 'edit_page';
        payload.type = 'content';
        payload.pageId = $('#kcms_page').data('page_id');
        payload.zone = zone;

        let url = '/kcms/admin/ajax/jstree/get_module';

        axios.post(url, payload).then(
            (response) => {
                if (response.data.status === 'done') {

                    $("#dialog-select").html(response.data.html);
                    init_jstree('content');

                    $( "#dialog-select" ).dialog({
                        resizable: true,
                        height: "auto",
                        width: 1200,
                        modal: true,
                        close: function(){
                        },
                        buttons: {
                            Cancel: function() {
                                $(this).dialog( "close" );
                            }
                        }
                    });
                }
            },
            (response) => {
                console.log('An error has been encountered');
                console.log(response);
            }
        );
    });

    $('.delete_slug').click(function(){

        let pageSludId = $(this).data('id');

        $( "#dialog-confirm" ).html('Are you sure you want to delete this slug ?');

        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "Ok": function() {
                    let payload = {};
                    payload.pageSlugId = pageSludId;

                    let url = '/kcms/admin/ajax/edit_page/delete_slug';

                    axios.post(url, payload).then(
                        (response) => {
                            if (response.data.status === 'done') {
                                refreshPage();
                            }
                        },
                        (response) => {
                            console.log('An error has been encountered');
                            console.log(response);
                        }
                    );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    });
});

function refreshPage()
{
    window.location = window.location.href;
}
