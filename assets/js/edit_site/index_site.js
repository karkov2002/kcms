const $ = require('jquery');
import 'jquery-ui/ui/widgets/dialog';
import axios from "axios";

$(function() {
    $('.delete_site').each(function(){
        $(this).click(function(){

            let siteId = $(this).data('id');

            $( "#dialog-confirm" ).html('Are you sure you want to delete this site ?');
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Ok": function() {
                        let payload = {};
                        payload.siteId = siteId;
                        let url = '/kcms/admin/ajax/edit_site/delete_site';

                        axios.post(url, payload).then(
                            (response) => {
                                if (response.data.status === 'success') {
                                    refreshPage();
                                } else if (response.data.status === 'ko') {
                                    $( "#dialog-confirm" ).html('Impossible to delete this site');
                                    $( "#dialog-confirm" ).dialog({
                                        buttons: {
                                            Cancel: function() {
                                                $( this ).dialog( "close" );
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
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        });
    });
});

function refreshPage()
{
    window.location = window.location.href;
}