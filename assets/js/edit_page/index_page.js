const $ = require('jquery');
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/widgets/dialog';
import '../../styles/jstree/default/style.css';
import '../jstree/jstree';
import {init_jstree} from '../jstree/kcms_init_jstree';
const axios = require('axios');
axios.defaults.headers["X-Requested-With"] = "XMLHttpRequest";

$(function() {
    if ($("#page_list").length) {
        let payload = {};
        let url = '/kcms/admin/ajax/jstree/get_module';
        payload.context = 'index_page';
        payload.type = 'page';

        axios.post(url, payload).then(
            (response) => {
                if (response.data.status === 'done') {
                    $("#page_list").html(response.data.html);
                    init_jstree('page');
                }
            },
            (response) => {
                console.log('An error has been encountered');
                console.log(response);
            }
        );
    }
});
