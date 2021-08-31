import $ from "jquery";
const axios = require('axios');
axios.defaults.headers["X-Requested-With"] = "XMLHttpRequest";

function init_jstree (typeTree) {
    $('#jstree')
        .on('changed.jstree', function (e, data) {
            console.log('CHANGED');
        })
        .on('copy_node.jstree', function (e, data) {
            let payload = {};
            payload.nodeId = data.original.id;
            payload.parent = data.parent;
            payload.typeTree = typeTree;
            payload.type = data.node.type === 'default' ? 'directory' : 'node';

            let url = '/kcms/admin/ajax/jstree/copy_node';

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
        })
        .on('select_node.jstree', function (e, data) {
            console.log('SELECT');
        })
        .on('rename_node.jstree', function (e, data) {
            let payload = {};
            payload.nodeId = data.node.id;
            payload.typeTree = typeTree;
            payload.type = data.node.type === 'default' ? 'directory' : 'node';
            payload.label = data.node.text;

            let url = '/kcms/admin/ajax/jstree/rename_node';

            axios.post(url, payload).then(
                (response) => {
                    if (response.data.status === 'done') {

                    }
                },
                (response) => {
                    console.log('An error has been encountered');
                    console.log(response);
                }
            );

        })
        .on('delete_node.jstree', function (e, data) {
            let payload = {};
            payload.nodeId = data.node.id;
            payload.typeTree = typeTree;
            payload.type = data.node.type === 'default' ? 'directory' : 'node';

            let url = '/kcms/admin/ajax/jstree/delete_node';

            axios.post(url, payload).then(
                (response) => {
                    if (response.data.status === 'refuse') {
                        $('#jstree').jstree("refresh");
                        $( "#dialog-confirm" ).html("Impossible to delete this node");
                        $( "#dialog-confirm" ).dialog({
                            resizable: false,
                            height: "auto",
                            width: 600,
                            modal: true,
                            title: "Error",
                            buttons: {
                                "Ok": function() {
                                    $(this).dialog( "close" );
                                }
                            }
                        });
                    }
                },
                (response) => {
                    $('#jstree').jstree("refresh");
                    console.log('An error has been encountered');
                    console.log(response);
                }
            );

        })
        .on('create_node.jstree', function (e, data) {
            let payload = {};
            payload.typeTree = typeTree;
            payload.parentId = data.node.parent;
            payload.type = data.node.type === 'default' ? 'directory' : 'node';
            payload.label = data.node.text;

            let url = '/kcms/admin/ajax/jstree/create_node';

            axios.post(url, payload).then(
                (response) => {
                    if (response.data.status === 'done') {
                        $('#jstree').jstree(true).set_id(data.node, response.data.id);
                        $('#jstree').jstree("refresh");
                    }
                },
                (response) => {
                    console.log('An error has been encountered');
                    console.log(response);
                }
            );
        })
        .on('move_node.jstree', function (e, data) {
            let payload = {};
            payload.typeTree = typeTree;
            payload.oldParent = data.old_parent;
            payload.newParent = data.parent;
            payload.type = data.node.li_attr.type;
            payload.nodeId = data.node.id;

            let url = '/kcms/admin/ajax/jstree/move_node';

            axios.post(url, payload).then(
                (response) => {
                    if (response.data.status === 'done') {

                    }
                },
                (response) => {
                    console.log('An error has been encountered');
                    console.log(response);
                }
            );

        })
        .jstree({
            "core" : {
                "worker": false,
                "animation" : 0,
                "check_callback" : true,
                "themes" : { "stripes" : true },
                'data' : {
                    'url' : function (node) {
                        return '/kcms/admin/ajax/jstree/get_nodes/' + typeTree;
                    },
                    'data' : function (node) {
                        return { 'id' : node.id };
                    }
                }
            },
            "types" : {
                "#" : {
                    "valid_children" : ["root"]
                },
                "root" : {
                    // "icon" : "/static/3.3.11/assets/images/tree_icon.png",
                    "valid_children" : ["default"]
                },
                "default" : {
                    "valid_children" : ["default","file"]
                },
                "file" : {
                    "icon" : " fa-fw fa fa-edit",
                    "valid_children" : []
                }
            },
            "contextmenu":{
                "items": function($node) {
                    return customMenu($node, typeTree);
                }
            },
            "plugins" : [
                "contextmenu", "dnd", "search",
                "state", "types", "wholerow"
            ]
        });

    //---------------------
    // tool bar buttons
    //---------------------

    // Search
    $(function () {
        var to = false;
        $('#jstree_search').keyup(function () {
            if(to) { clearTimeout(to); }
            to = setTimeout(function () {
                var v = $('#jstree_search').val();
                $('#jstree').jstree(true).search(v);
            }, 250);
        });
    });

    // Create
    $("#jstree_create").click(function(){
        jstree_create();
    });

    // Rename
    $("#jstree_rename").click(function(){
        jstree_rename();
    });

    // Delete
    $("#jstree_delete").click(function(){
        var ref = $('#jstree').jstree(true),
            sel = ref.get_selected(true);
        if(!sel.length) { return false; }
        sel = sel[0];
        jstree_delete(sel);
    });
};

function jstree_create() {
    var ref = $('#jstree').jstree(true),
        sel = ref.get_selected();
    if(!sel.length) {
        $( "#dialog-confirm" ).html("You must select a parent directory before create a new content");
        $( "#dialog-confirm" ).dialog({
            resizable: false,
            height: "auto",
            width: 600,
            modal: true,
            title: "Please select a directory",
            buttons: {
                "Ok": function() {
                    $( this ).dialog( "close" );
                }
            }
        });

        return false;
    }
    sel = sel[0];
    sel = ref.create_node(sel, {"type":"file"});
    if(sel) {
        ref.edit(sel);
    }
};

function jstree_rename() {
    var ref = $('#jstree').jstree(true),
        sel = ref.get_selected();
    if(!sel.length) { return false; }
    sel = sel[0];
    ref.edit(sel);
};

function jstree_delete($node) {
    let contentLabel = $node.text + ' (' + $node.li_attr.type + ')';

    $( "#dialog-confirm" ).html("Are you sure you want to delete <strong>"+contentLabel+"</strong> ?");
    $( "#dialog-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 600,
        modal: true,
        title: "Confirmation",
        buttons: {
            "Yes": function() {
                var ref = $('#jstree').jstree(true),
                    sel = ref.get_selected();
                if(!sel.length) { return false; }
                ref.delete_node(sel);
                $( this ).dialog( "close" );
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });
};

function jstree_edit_content($node) {
    let href = $node.li_attr.href;
    window.open(href, '_blank');
}

function jstree_select_content($node) {
    let context = $('#data-context').data('context');
    if (context.context === 'edit_page') {
        let payload = {};
        payload.pageId = $('#kcms_page').data('page_id');
        payload.zone = context.zone;
        payload.contentId =$node.li_attr.id;

        let url = '/kcms/admin/ajax/edit_page/add_pagecontent';

        axios.post(url, payload).then(
            (response) => {
                if (response.data.status === 'done') {
                    $( "#dialog-select" ).dialog('close');
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

function jstree_select_htmlpattern($node) {
    let context = $('#data-context').data('context');
    if (context.context === 'edit_content') {
        let payload = {};
        payload.contentLocalId = context.contentLocalId
        payload.htmlPatternId =$node.li_attr.id;
        let local = context.local;

        let url = '/kcms/admin/ajax/edit_content/select_htmlpattern';

        axios.post(url, payload).then(
            (response) => {
                if (response.data.status === 'done') {
                    let formName = 'content_local_' + local;
                    $( "#dialog-select" ).dialog('close');
                    $('form[name='+formName+']').submit();
                }
            },
            (response) => {
                console.log('An error has been encountered');
                console.log(response);
            }
        );
    }
}

/**
 * Build contextual menu
 */
function customMenu($node, typeTree) {

    var tree = $("#jstree").jstree(true);
    let context = $('#data-context').data('context');
    var items = {
        "EditContent": {
            "separator_before": false,
            "separator_after": false,
            "label": "Edit " + typeTree,
            "action": function (obj) {
                jstree_edit_content($node);
            }
        },
        "SelectContent": {
            "separator_before": false,
            "separator_after": true,
            "label": "Select " + typeTree,
            "action": function (obj) {
                jstree_select_content($node);
            }
        },
        "SelectHtmlPattern": {
            "separator_before": false,
            "separator_after": true,
            "label": "Select " + typeTree,
            "action": function (obj) {
                jstree_select_htmlpattern($node);
            }
        },
        "Create": {
            "separator_before": false,
            "separator_after": false,
            "label": "Create",
            "action": false,
            "submenu": {
                "Content": {
                    "seperator_before": false,
                    "seperator_after": false,
                    "label": typeTree,
                    action: function (obj) {
                        $node = tree.create_node($node, { text: 'New ' + typeTree, type: 'file', icon: 'glyphicon glyphicon-file' });
                        tree.deselect_all();
                        tree.select_node($node);
                    }
                },
                "Folder": {
                    "seperator_before": false,
                    "seperator_after": false,
                    "label": "Folder",
                    action: function (obj) {
                        $node = tree.create_node($node, { text: 'New Folder', type: 'default' });
                        tree.deselect_all();
                        tree.select_node($node);
                    }
                }
            }
        },
        "Rename": {
            "separator_before": false,
            "separator_after": false,
            "label": "Rename",
            "action": function (obj) {
                tree.edit($node);
            }
        },
        "Delete": {
            "separator_before": false,
            "separator_after": true,
            "label": "Delete",
            "action": function (obj) {
                jstree_delete($node);
            }
        },
        "Edit": {
            "separator_before": false,
            "separator_after": false,
            "label": "Edit",
            "action": false,
            'submenu' : {
                'copy': {
                    'label': 'Copy',
                    'action': function (obj) {
                        tree.copy($node);
                    }
                },
                'paste': {
                    'label': 'Paste',
                    'action': function (obj) {
                        tree.paste($node);
                    }
                }
            }
        }
    };

    if ($node.li_attr.type === 'directory') {
        delete items.EditContent;
        delete items.SelectContent;
        delete items.SelectHtmlPattern;
        delete items.Edit.submenu.copy;
    }

    if (context.context !== 'edit_content') {
        delete items.SelectHtmlPattern;
    }

    if (context.context !== 'edit_page') {
        delete items.SelectContent;
    }

    return items;
}


function refreshPage() {
    window.location = window.location.href;
}

export {init_jstree};
