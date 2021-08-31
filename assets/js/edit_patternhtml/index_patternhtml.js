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
    if ($("#patternhtml_list").length) {
        let payload = {};
        let url = '/kcms/admin/ajax/jstree/get_module';
        payload.context = 'index_patternhtml';
        payload.type = 'patternhtml';

        axios.post(url, payload).then(
            (response) => {
                if (response.data.status === 'done') {
                    $("#patternhtml_list").html(response.data.html);
                    init_jstree('patternhtml');
                }
            },
            (response) => {
                console.log('An error has been encountered');
                console.log(response);
            }
        );
    }
});
