const $ = require('jquery');

$(document).ready(function(){
    var roxyFileman = '/kcms/fileman?integration=ckeditor';
    var CKEDITOR_BASEPATH = '/ckeditor/';
    var configCKEditor =
        {
            "default":
                {
                    "allowedContent": true,
                    "toolbar": [
                        ["Source", "-", "Preview", "-"], ["Cut", "Copy", "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo"], ["Maximize", "ShowBlocks"], "\/",
                        ["Bold", "Italic", "Underline", "Strike"], ["NumberedList", "BulletedList", "-", "Outdent", "Indent", "-", "Blockquote", "-", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock", "-"], "\/",
                        ["Link", "Unlink", "Anchor"], ["Image", "Table"], ["TextColor", "BGColor", "Format", "FontSize"]
                    ],
                    "uiColor": "#efefef",
                    "language": "fr-fr",
                    "filebrowserBrowseUrl": roxyFileman,
                },
            "full":
                {
                    "allowedContent": true,
                    "toolbar": "full",
                    "uiColor": "#efefef",
                    "language": "fr-fr",
                    "height": 400,
                    "filebrowserBrowseUrl": roxyFileman,
                },
            "light":
                {
                    "allowedContent": true,
                    "toolbar": [
                        ["Source", "-", "Preview", "-"], ["Cut", "Copy", "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo"], ["Maximize"],
                        ["Bold", "Italic", "Underline", "Strike"], ["NumberedList", "BulletedList", "-", "Blockquote", "-", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock", "-"],
                        ["Link", "Unlink", "Anchor"], ["FontSize"]
                    ],
                    "uiColor": "#efefef",
                    "language": "fr-fr",
                    "filebrowserBrowseUrl": roxyFileman,
                },
        };

    // ModuleHtml
    $('.field-ckeditor').each(function(){
        let id = $(this).attr('id');
        if (id !== undefined) {
            if (CKEDITOR.instances[id]) {
                CKEDITOR.instances[id].destroy(true);
                delete CKEDITOR.instances[id];
            }
            CKEDITOR.replace(id, configCKEditor["full"]);
        }
    });

    // ModuleHtmlLight
    $('.field-ckeditor-light').each(function(){
        let id = $(this).attr('id');
        if (id !== undefined) {
            if (CKEDITOR.instances[id]) {
                CKEDITOR.instances[id].destroy(true);
                delete CKEDITOR.instances[id];
            }
            CKEDITOR.replace(id, configCKEditor["light"]);
        }
    });
});

