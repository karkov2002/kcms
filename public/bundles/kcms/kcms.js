(self["webpackChunk"] = self["webpackChunk"] || []).push([["kcms"],{

/***/ "../karkov/kcms-bundle/assets/js/kcms.js":
/*!***********************************************!*\
  !*** ../karkov/kcms-bundle/assets/js/kcms.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! bootstrap */ "../karkov/kcms-bundle/node_modules/bootstrap/dist/js/bootstrap.js");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(bootstrap__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jquery_ui_ui_widgets_draggable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jquery-ui/ui/widgets/draggable */ "../karkov/kcms-bundle/node_modules/jquery-ui/ui/widgets/draggable.js");
/* harmony import */ var jquery_ui_ui_widgets_draggable__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery_ui_ui_widgets_draggable__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var jquery_ui_ui_widgets_droppable__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! jquery-ui/ui/widgets/droppable */ "../karkov/kcms-bundle/node_modules/jquery-ui/ui/widgets/droppable.js");
/* harmony import */ var jquery_ui_ui_widgets_droppable__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(jquery_ui_ui_widgets_droppable__WEBPACK_IMPORTED_MODULE_2__);
var $ = __webpack_require__(/*! jquery */ "../karkov/kcms-bundle/node_modules/jquery/dist/jquery.js");





var axios = __webpack_require__(/*! axios */ "../karkov/kcms-bundle/node_modules/axios/index.js");

axios.defaults.headers["X-Requested-With"] = "XMLHttpRequest";
$(document).ready(function () {
  initIfConnected();
}); //===================================
// Security / Init menu
//===================================

function initIfConnected() {
  if (typeof BUILD_KCMS_MENU !== 'undefined' && BUILD_KCMS_MENU === true) {
    var url = '/kcms/api/is_connected';
    axios.get(url).then(function (response) {
      if (response.data.connected) {
        buildKcmsMenu();
      }
    }, function (response) {
      console.log('An error has been encountered');
      console.log(response);
    });
  }
} //===================================
// Libs
//===================================


function initState() {
  var state = {
    "show": false
  };
  setZoneState(state);
  setContentState(state);
}

function buildKcmsMenu() {
  // Init menu
  $('.kcms_menu').remove();
  $('body').append(buildKcmsFrontMenu());
  initState(); // Click on "show zone button"

  $('#kcms_front_menu_show_zones').click(function () {
    var state = getZoneState();

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
  }); // Click on "show content button"

  $('#kcms_front_menu_show_contents').click(function () {
    var state = getContentState();

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
  return "\n<div class=\"kcms_menu\">\n    <input type=\"button\" value=\"zones\" id=\"kcms_front_menu_show_zones\" data-state=\"\">\n    <input type=\"button\" value=\"contents\" id=\"kcms_front_menu_show_contents\" data-state=\"\">\n    <a href=\"/kcms/admin/edit_page/" + KCMS_PAGE_ID + "\" target=\"_blank\"><input type=\"button\" value=\"Edit this page\" id=\"kcms_front_menu_goto_admin_edit_page\" data-state=\"\"></a>\n    <a href=\"/kcms/admin\" target=\"_blank\"><input type=\"button\" value=\"admin\" id=\"kcms_front_menu_goto_admin\" data-state=\"\"></a>\n</div>\n    ";
}

function buildZoneLayer(zoneObj) {
  var height = zoneObj.height();
  var width = zoneObj.width();
  var position = zoneObj.position();
  zoneObj.append('<div class="kcms_zone_layer" style="top: ' + position.top + 'px; left: ' + position.left + 'px; height:' + height + 'px; width:' + width + 'px; min-height: 28px;"></div>');
  zoneObj.append('<div class="kcms_zone_nb" style="top: ' + position.top + 'px; left: ' + position.left + 'px;">Zone # ' + zoneObj.data("zone") + '</div>');
}

function eraseZoneLayer(zoneObj) {
  $('.kcms_zone_layer').each(function () {
    $(this).remove();
  });
  $('.kcms_zone_nb').each(function () {
    $(this).remove();
  });
}

function buildContentLayer(contentObj) {
  var height = contentObj.height();
  var width = contentObj.width();
  var position = contentObj.position();
  var editionUrl = '/kcms/admin/edit_content/' + contentObj.data("content-id");
  contentObj.append('<div class="kcms_content_layer" style="top: ' + position.top + 'px; left: ' + position.left + 'px; height:' + height + 'px; width:' + width + 'px; min-height: 28px;"></div>');
  contentObj.append('<div class="kcms_content_nb" style="top: ' + position.top + 'px; left: ' + position.left + 'px;">Content # ' + contentObj.data("content-id") + '&nbsp;<a href="' + editionUrl + '" target="_blank"><i class="fa fa-edit"></i></a></div>');
}

function eraseContentLayer(contentObj) {
  $('.kcms_content_layer').each(function () {
    $(this).remove();
  });
  $('.kcms_content_nb').each(function () {
    $(this).remove();
  });
}

/***/ }),

/***/ "../karkov/kcms-bundle/assets/kcms.js":
/*!********************************************!*\
  !*** ../karkov/kcms-bundle/assets/kcms.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _styles_kcms_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./styles/kcms.scss */ "../karkov/kcms-bundle/assets/styles/kcms.scss");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap */ "../karkov/kcms-bundle/node_modules/bootstrap/dist/js/bootstrap.js");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(bootstrap__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _js_kcms_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./js/kcms.js */ "../karkov/kcms-bundle/assets/js/kcms.js");




/***/ }),

/***/ "../karkov/kcms-bundle/assets/styles/kcms.scss":
/*!*****************************************************!*\
  !*** ../karkov/kcms-bundle/assets/styles/kcms.scss ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
0,[["../karkov/kcms-bundle/assets/kcms.js","runtime","vendors-karkov_kcms-bundle_node_modules_jquery_dist_jquery_js","vendors-karkov_kcms-bundle_node_modules_axios_index_js-karkov_kcms-bundle_node_modules_jquery-03a9eb","vendors-karkov_kcms-bundle_node_modules_bootstrap_dist_js_bootstrap_js"]]]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi4va2Fya292L2tjbXMtYnVuZGxlL2Fzc2V0cy9qcy9rY21zLmpzIiwid2VicGFjazovLy8uLi9rYXJrb3Yva2Ntcy1idW5kbGUvYXNzZXRzL2tjbXMuanMiLCJ3ZWJwYWNrOi8vLy4uL2thcmtvdi9rY21zLWJ1bmRsZS9hc3NldHMvc3R5bGVzL2tjbXMuc2NzcyJdLCJuYW1lcyI6WyIkIiwicmVxdWlyZSIsImF4aW9zIiwiZGVmYXVsdHMiLCJoZWFkZXJzIiwiZG9jdW1lbnQiLCJyZWFkeSIsImluaXRJZkNvbm5lY3RlZCIsIkJVSUxEX0tDTVNfTUVOVSIsInVybCIsImdldCIsInRoZW4iLCJyZXNwb25zZSIsImRhdGEiLCJjb25uZWN0ZWQiLCJidWlsZEtjbXNNZW51IiwiY29uc29sZSIsImxvZyIsImluaXRTdGF0ZSIsInN0YXRlIiwic2V0Wm9uZVN0YXRlIiwic2V0Q29udGVudFN0YXRlIiwicmVtb3ZlIiwiYXBwZW5kIiwiYnVpbGRLY21zRnJvbnRNZW51IiwiY2xpY2siLCJnZXRab25lU3RhdGUiLCJzaG93IiwiYWRkQ2xhc3MiLCJyZW1vdmVDbGFzcyIsImVhY2giLCJidWlsZFpvbmVMYXllciIsImVyYXNlWm9uZUxheWVyIiwiZ2V0Q29udGVudFN0YXRlIiwiYnVpbGRDb250ZW50TGF5ZXIiLCJlcmFzZUNvbnRlbnRMYXllciIsIkpTT04iLCJzdHJpbmdpZnkiLCJwYXJzZSIsIktDTVNfUEFHRV9JRCIsInpvbmVPYmoiLCJoZWlnaHQiLCJ3aWR0aCIsInBvc2l0aW9uIiwidG9wIiwibGVmdCIsImNvbnRlbnRPYmoiLCJlZGl0aW9uVXJsIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7O0FBQUEsSUFBTUEsQ0FBQyxHQUFHQyxtQkFBTyxDQUFDLHdFQUFELENBQWpCOztBQUNBO0FBQ0E7QUFDQTs7QUFDQSxJQUFNQyxLQUFLLEdBQUdELG1CQUFPLENBQUMsZ0VBQUQsQ0FBckI7O0FBQ0FDLEtBQUssQ0FBQ0MsUUFBTixD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QSxNQUFJQyxLQUFLLEdBQUdJLFVBQVUsQ0FBQ0osS0FBWCxFQUFaO0FBQ0EsTUFBSUMsUUFBUSxHQUFHRyxVQUFVLENBQUNILFFBQVgsRUFBZjtBQUVBLE1BQUlJLFVBQVUsR0FBRyw4QkFBOEJELFVBQVUsQ0FBQ2pDLElBQVgsQ0FBZ0IsWUFBaEIsQ0FBL0M7QUFFQWlDLFlBQVUsQ0FBQ3ZCLE1BQVgsQ0FBa0IsaURBQStDb0IsUUFBUSxDQUFDQyxHQUF4RCxHQUE0RCxZQUE1RCxHQUF5RUQsUUFBUSxDQUFDRSxJQUFsRixHQUF1RixhQUF2RixHQUFxR0osTUFBckcsR0FBNEcsWUFBNUcsR0FBeUhDLEtBQXpILEdBQStILCtCQUFqSjtBQUNBSSxZQUFVLENBQUN2QixNQUFYLENBQWtCLDhDQUE0Q29CLFFBQVEsQ0FBQ0MsR0FBckQsR0FBeUQsWUFBekQsR0FBc0VELFFBQVEsQ0FBQ0UsSUFBL0UsR0FBb0YsaUJBQXBGLEdBQXNHQyxVQUFVLENBQUNqQyxJQUFYLENBQWdCLFlBQWhCLENBQXRHLEdBQW9JLGlCQUFwSSxHQUFzSmtDLFVBQXRKLEdBQWlLLHdEQUFuTDtBQUNIOztBQUVELFNBQVNaLGlCQUFULENBQTJCVyxVQUEzQixFQUF1QztBQUNuQzlDLEdBQUMsQ0FBQyxxQkFBRCxDQUFELENBQXlCOEIsSUFBekIsQ0FBOEIsWUFBVTtBQUNwQzlCLEtBQUMsQ0FBQyxJQUFELENBQUQsQ0FBUXNCLE1BQVI7QUFDSCxHQUZEO0FBR0F0QixHQUFDLENBQUMsa0JBQUQsQ0FBRCxDQUFzQjhCLElBQXRCLENBQTJCLFlBQVU7QUFDakM5QixLQUFDLENBQUMsSUFBRCxDQUFELENBQVFzQixNQUFSO0FBQ0gsR0FGRDtBQUdILEM7Ozs7Ozs7Ozs7Ozs7Ozs7QUM1SkQ7QUFDQTs7Ozs7Ozs7Ozs7OztBQ0RBIiwiZmlsZSI6ImtjbXMuanMiLCJzb3VyY2VzQ29udGVudCI6WyJjb25zdCAkID0gcmVxdWlyZSgnanF1ZXJ5Jyk7XG5pbXBvcnQgJ2Jvb3RzdHJhcCc7XG5pbXBvcnQgJ2pxdWVyeS11aS91aS93aWRnZXRzL2RyYWdnYWJsZSc7XG5pbXBvcnQgJ2pxdWVyeS11aS91aS93aWRnZXRzL2Ryb3BwYWJsZSc7XG5jb25zdCBheGlvcyA9IHJlcXVpcmUoJ2F4aW9zJyk7XG5heGlvcy5kZWZhdWx0cy5oZWFkZXJzW1wiWC1SZXF1ZXN0ZWQtV2l0aFwiXSA9IFwiWE1MSHR0cFJlcXVlc3RcIjtcblxuJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKXtcbiAgICBpbml0SWZDb25uZWN0ZWQoKTtcbn0pO1xuXG4vLz09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XG4vLyBTZWN1cml0eSAvIEluaXQgbWVudVxuLy89PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxuXG5mdW5jdGlvbiBpbml0SWZDb25uZWN0ZWQoKSB7XG4gICAgaWYgKHR5cGVvZiBCVUlMRF9LQ01TX01FTlUgIT09ICd1bmRlZmluZWQnICYmIEJVSUxEX0tDTVNfTUVOVSA9PT0gdHJ1ZSkge1xuICAgICAgICBsZXQgdXJsID0gJy9rY21zL2FwaS9pc19jb25uZWN0ZWQnO1xuICAgICAgICBheGlvcy5nZXQodXJsKS50aGVuKFxuICAgICAgICAgICAgKHJlc3BvbnNlKSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlLmRhdGEuY29ubmVjdGVkKSB7XG4gICAgICAgICAgICAgICAgICAgIGJ1aWxkS2Ntc01lbnUoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgKHJlc3BvbnNlKSA9PiB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0FuIGVycm9yIGhhcyBiZWVuIGVuY291bnRlcmVkJyk7XG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2cocmVzcG9uc2UpO1xuICAgICAgICAgICAgfVxuICAgICAgICApO1xuICAgIH1cbn1cblxuLy89PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxuLy8gTGlic1xuLy89PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxuXG5mdW5jdGlvbiBpbml0U3RhdGUoKSB7XG4gICAgbGV0IHN0YXRlID0ge1wic2hvd1wiOiBmYWxzZX07XG4gICAgc2V0Wm9uZVN0YXRlKHN0YXRlKTtcbiAgICBzZXRDb250ZW50U3RhdGUoc3RhdGUpO1xufVxuXG5mdW5jdGlvbiBidWlsZEtjbXNNZW51KCkge1xuICAgIC8vIEluaXQgbWVudVxuICAgICQoJy5rY21zX21lbnUnKS5yZW1vdmUoKTtcbiAgICAkKCdib2R5JykuYXBwZW5kKGJ1aWxkS2Ntc0Zyb250TWVudSgpKTtcbiAgICBpbml0U3RhdGUoKTtcblxuICAgIC8vIENsaWNrIG9uIFwic2hvdyB6b25lIGJ1dHRvblwiXG4gICAgJCgnI2tjbXNfZnJvbnRfbWVudV9zaG93X3pvbmVzJykuY2xpY2soZnVuY3Rpb24gKCkge1xuICAgICAgICBsZXQgc3RhdGUgPSBnZXRab25lU3RhdGUoKTtcbiAgICAgICAgaWYgKHN0YXRlLnNob3cgPT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHN0YXRlLnNob3cgPSB0cnVlO1xuICAgICAgICAgICAgc2V0Wm9uZVN0YXRlKHN0YXRlKTtcbiAgICAgICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygna2Ntc19zY2FsZV9yZWR1Y2UnKTtcbiAgICAgICAgICAgICQoJ2JvZHknKS5yZW1vdmVDbGFzcygna2Ntc19zY2FsZV9mdWxsJyk7XG4gICAgICAgICAgICAkKCcua2Ntc196b25lJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgYnVpbGRab25lTGF5ZXIoJCh0aGlzKSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHN0YXRlLnNob3cgPSBmYWxzZTtcbiAgICAgICAgICAgIHNldFpvbmVTdGF0ZShzdGF0ZSk7XG4gICAgICAgICAgICAkKCdib2R5JykuYWRkQ2xhc3MoJ2tjbXNfc2NhbGVfZnVsbCcpO1xuICAgICAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCdrY21zX3NjYWxlX3JlZHVjZScpO1xuICAgICAgICAgICAgJCgnLmtjbXNfem9uZScpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGVyYXNlWm9uZUxheWVyKCQodGhpcykpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8vIENsaWNrIG9uIFwic2hvdyBjb250ZW50IGJ1dHRvblwiXG4gICAgJCgnI2tjbXNfZnJvbnRfbWVudV9zaG93X2NvbnRlbnRzJykuY2xpY2soZnVuY3Rpb24gKCkge1xuICAgICAgICBsZXQgc3RhdGUgPSBnZXRDb250ZW50U3RhdGUoKTtcbiAgICAgICAgaWYgKHN0YXRlLnNob3cgPT0gZmFsc2UpIHtcbiAgICAgICAgICAgIHN0YXRlLnNob3cgPSB0cnVlO1xuICAgICAgICAgICAgc2V0Q29udGVudFN0YXRlKHN0YXRlKTtcbiAgICAgICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygna2Ntc19zY2FsZV9yZWR1Y2UnKTtcbiAgICAgICAgICAgICQoJ2JvZHknKS5yZW1vdmVDbGFzcygna2Ntc19zY2FsZV9mdWxsJyk7XG4gICAgICAgICAgICAkKCcua2Ntc19jb250ZW50JykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgYnVpbGRDb250ZW50TGF5ZXIoJCh0aGlzKSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHN0YXRlLnNob3cgPSBmYWxzZTtcbiAgICAgICAgICAgIHNldENvbnRlbnRTdGF0ZShzdGF0ZSk7XG4gICAgICAgICAgICAkKCdib2R5JykuYWRkQ2xhc3MoJ2tjbXNfc2NhbGVfZnVsbCcpO1xuICAgICAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCdrY21zX3NjYWxlX3JlZHVjZScpO1xuICAgICAgICAgICAgJCgnLmtjbXNfY29udGVudCcpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGVyYXNlQ29udGVudExheWVyKCQodGhpcykpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gc2V0Wm9uZVN0YXRlKHN0YXRlKSB7XG4gICAgJCgnI2tjbXNfZnJvbnRfbWVudV9zaG93X3pvbmVzJykuZGF0YSgnc3RhdGUnLCBKU09OLnN0cmluZ2lmeShzdGF0ZSkpO1xufVxuXG5mdW5jdGlvbiBnZXRab25lU3RhdGUoKSB7XG4gICAgcmV0dXJuIEpTT04ucGFyc2UoJCgnI2tjbXNfZnJvbnRfbWVudV9zaG93X3pvbmVzJykuZGF0YSgnc3RhdGUnKSk7XG59XG5cbmZ1bmN0aW9uIHNldENvbnRlbnRTdGF0ZShzdGF0ZSkge1xuICAgICQoJyNrY21zX2Zyb250X21lbnVfc2hvd19jb250ZW50cycpLmRhdGEoJ3N0YXRlJywgSlNPTi5zdHJpbmdpZnkoc3RhdGUpKTtcbn1cblxuZnVuY3Rpb24gZ2V0Q29udGVudFN0YXRlKCkge1xuICAgIHJldHVybiBKU09OLnBhcnNlKCQoJyNrY21zX2Zyb250X21lbnVfc2hvd19jb250ZW50cycpLmRhdGEoJ3N0YXRlJykpO1xufVxuXG5mdW5jdGlvbiBidWlsZEtjbXNGcm9udE1lbnUoKSB7XG4gICAgcmV0dXJuIGBcbjxkaXYgY2xhc3M9XCJrY21zX21lbnVcIj5cbiAgICA8aW5wdXQgdHlwZT1cImJ1dHRvblwiIHZhbHVlPVwiem9uZXNcIiBpZD1cImtjbXNfZnJvbnRfbWVudV9zaG93X3pvbmVzXCIgZGF0YS1zdGF0ZT1cIlwiPlxuICAgIDxpbnB1dCB0eXBlPVwiYnV0dG9uXCIgdmFsdWU9XCJjb250ZW50c1wiIGlkPVwia2Ntc19mcm9udF9tZW51X3Nob3dfY29udGVudHNcIiBkYXRhLXN0YXRlPVwiXCI+XG4gICAgPGEgaHJlZj1cIi9rY21zL2FkbWluL2VkaXRfcGFnZS9gK0tDTVNfUEFHRV9JRCtgXCIgdGFyZ2V0PVwiX2JsYW5rXCI+PGlucHV0IHR5cGU9XCJidXR0b25cIiB2YWx1ZT1cIkVkaXQgdGhpcyBwYWdlXCIgaWQ9XCJrY21zX2Zyb250X21lbnVfZ290b19hZG1pbl9lZGl0X3BhZ2VcIiBkYXRhLXN0YXRlPVwiXCI+PC9hPlxuICAgIDxhIGhyZWY9XCIva2Ntcy9hZG1pblwiIHRhcmdldD1cIl9ibGFua1wiPjxpbnB1dCB0eXBlPVwiYnV0dG9uXCIgdmFsdWU9XCJhZG1pblwiIGlkPVwia2Ntc19mcm9udF9tZW51X2dvdG9fYWRtaW5cIiBkYXRhLXN0YXRlPVwiXCI+PC9hPlxuPC9kaXY+XG4gICAgYDtcbn1cblxuZnVuY3Rpb24gYnVpbGRab25lTGF5ZXIoem9uZU9iaikge1xuICAgIGxldCBoZWlnaHQgPSB6b25lT2JqLmhlaWdodCgpO1xuICAgIGxldCB3aWR0aCA9IHpvbmVPYmoud2lkdGgoKTtcbiAgICBsZXQgcG9zaXRpb24gPSB6b25lT2JqLnBvc2l0aW9uKCk7XG5cbiAgICB6b25lT2JqLmFwcGVuZCgnPGRpdiBjbGFzcz1cImtjbXNfem9uZV9sYXllclwiIHN0eWxlPVwidG9wOiAnK3Bvc2l0aW9uLnRvcCsncHg7IGxlZnQ6ICcrcG9zaXRpb24ubGVmdCsncHg7IGhlaWdodDonK2hlaWdodCsncHg7IHdpZHRoOicrd2lkdGgrJ3B4OyBtaW4taGVpZ2h0OiAyOHB4O1wiPjwvZGl2PicpO1xuICAgIHpvbmVPYmouYXBwZW5kKCc8ZGl2IGNsYXNzPVwia2Ntc196b25lX25iXCIgc3R5bGU9XCJ0b3A6ICcrcG9zaXRpb24udG9wKydweDsgbGVmdDogJytwb3NpdGlvbi5sZWZ0KydweDtcIj5ab25lICMgJyt6b25lT2JqLmRhdGEoXCJ6b25lXCIpKyc8L2Rpdj4nKTtcbn1cblxuZnVuY3Rpb24gZXJhc2Vab25lTGF5ZXIoem9uZU9iaikge1xuICAgICQoJy5rY21zX3pvbmVfbGF5ZXInKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgfSk7XG4gICAgJCgnLmtjbXNfem9uZV9uYicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgJCh0aGlzKS5yZW1vdmUoKTtcbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gYnVpbGRDb250ZW50TGF5ZXIoY29udGVudE9iaikge1xuICAgIGxldCBoZWlnaHQgPSBjb250ZW50T2JqLmhlaWdodCgpO1xuICAgIGxldCB3aWR0aCA9IGNvbnRlbnRPYmoud2lkdGgoKTtcbiAgICBsZXQgcG9zaXRpb24gPSBjb250ZW50T2JqLnBvc2l0aW9uKCk7XG5cbiAgICBsZXQgZWRpdGlvblVybCA9ICcva2Ntcy9hZG1pbi9lZGl0X2NvbnRlbnQvJyArIGNvbnRlbnRPYmouZGF0YShcImNvbnRlbnQtaWRcIik7XG5cbiAgICBjb250ZW50T2JqLmFwcGVuZCgnPGRpdiBjbGFzcz1cImtjbXNfY29udGVudF9sYXllclwiIHN0eWxlPVwidG9wOiAnK3Bvc2l0aW9uLnRvcCsncHg7IGxlZnQ6ICcrcG9zaXRpb24ubGVmdCsncHg7IGhlaWdodDonK2hlaWdodCsncHg7IHdpZHRoOicrd2lkdGgrJ3B4OyBtaW4taGVpZ2h0OiAyOHB4O1wiPjwvZGl2PicpO1xuICAgIGNvbnRlbnRPYmouYXBwZW5kKCc8ZGl2IGNsYXNzPVwia2Ntc19jb250ZW50X25iXCIgc3R5bGU9XCJ0b3A6ICcrcG9zaXRpb24udG9wKydweDsgbGVmdDogJytwb3NpdGlvbi5sZWZ0KydweDtcIj5Db250ZW50ICMgJytjb250ZW50T2JqLmRhdGEoXCJjb250ZW50LWlkXCIpKycmbmJzcDs8YSBocmVmPVwiJytlZGl0aW9uVXJsKydcIiB0YXJnZXQ9XCJfYmxhbmtcIj48aSBjbGFzcz1cImZhIGZhLWVkaXRcIj48L2k+PC9hPjwvZGl2PicpO1xufVxuXG5mdW5jdGlvbiBlcmFzZUNvbnRlbnRMYXllcihjb250ZW50T2JqKSB7XG4gICAgJCgnLmtjbXNfY29udGVudF9sYXllcicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgJCh0aGlzKS5yZW1vdmUoKTtcbiAgICB9KTtcbiAgICAkKCcua2Ntc19jb250ZW50X25iJykuZWFjaChmdW5jdGlvbigpe1xuICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgIH0pO1xufVxuXG4iLCJpbXBvcnQgJy4vc3R5bGVzL2tjbXMuc2Nzcyc7XG5pbXBvcnQgJ2Jvb3RzdHJhcCc7XG5pbXBvcnQgJy4vanMva2Ntcy5qcydcbiIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyJdLCJzb3VyY2VSb290IjoiIn0=