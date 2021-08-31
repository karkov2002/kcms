import {init_jstree} from "../jstree/kcms_init_jstree";

const $ = require('jquery');
import axios from 'axios';
import 'jquery-ui/ui/widgets/dialog';

// tab management
$('.tab_local').click(function(){
   $('.tab_local').each(function(){
       $(this).removeClass('tab_local_selected');
   });
   $('.tab_content_local').each(function(){
       $(this).css('display','none');
   });

   $(this).addClass('tab_local_selected');
   let local = $(this).data('local');
   $('#tab_content_'+local).fadeIn();
});

// ImageType
$('.add_img_btn').click(function(){
    $('.select_img').each(function(){
        $(this).removeClass('select_img');
    });

    let id = $(this).data('id');
    let input = $('#img_selector_' + id);
    input.addClass('select_img');

    let url = '/kcms/admin/iframe/media_center';

    axios.get(url).then(
        (response) => {
            $( "#dialog-confirm" ).html(response.data);
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height: "auto",
                width: 1000,
                modal: true,
                title: "Select a media",
                buttons: {
                    "Ok": function() {
                        $(this).dialog( "close" );
                        input.removeClass('select_img');
                    }
                }
            });
        },
        (response) => {
            console.log('An error has been encountered');
            console.log(response);
        }
    );
});

$('.img_preview img').click(function(){
    $(this).parent().parent().find('.add_img_btn').click();
});

$('.img_clear').click(function(){
    let id = $(this).data('id');

    $( "#dialog-confirm" ).html('Are you sure you want to clear this image ?');
    $( "#dialog-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Ok": function() {
                $('#'+id).val(null);
                $('#img_preview_'+id+' img').attr('src',null);
                $( this ).dialog( "close" );
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });
});

$('.add_element_html').click(function(){
    let id = $(this).parent().data('id');
    if (CKEDITOR.instances[id]) {
        let maxId = 1;
        let html = CKEDITOR.instances[id].getData();
        let matches = html.match(/{{ELEMENT:([0-9]*):([A-Z_]*)}}/g);

        if (matches !== null) {
            matches.forEach(element => {
                let sub = element.match(/{{ELEMENT:([0-9]*):([A-Z_]*)}}/);
                let elementId = parseInt(sub[1]);
                if (elementId > maxId) {
                    maxId = elementId;
                }
            });
            maxId++;
        }

        let value = '{{ELEMENT:'+maxId+':' + $(this).val() + '}}';
        CKEDITOR.instances[id].insertText(value);
    }
});

$('.add_external_html_pattern').click(function(){
    let payload = {};
    payload.context = 'edit_content';
    payload.type = 'patternhtml';
    payload.contentLocalId = $(this).data('id');
    payload.local = 'fr_FR';

    let url = '/kcms/admin/ajax/jstree/get_module';

    axios.post(url, payload).then(
        (response) => {
            if (response.data.status === 'done') {

                $("#dialog-select").html(response.data.html);
                init_jstree('patternhtml');

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

$('.htmlpattern_remove').click(function(){
    let id = $(this).data('id');
    let local = $(this).data('local');

    $( "#dialog-confirm" ).html('Are you sure you want to detach the html pattern ?');
    $( "#dialog-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Ok": function() {

                let payload = {};
                payload.contentLocalId = id;
                let url = '/kcms/admin/ajax/edit_content/detach_htmlpattern';

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

function refreshPage() {
    window.location = window.location.href;
}