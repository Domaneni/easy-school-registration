jQuery(function ($) {
  $(document).ready(function () {
    if ($(".esr-question").length !== 0) {
      tippy(".esr-question", {
        interactive: true,
        content: (reference) => reference.getAttribute('title')
      });
    }

    if ($("body").find(".esr-hall").size() === 0) {
      $("body").find(".esr-header .esr-hall-header").hide();
    }

    function cleanInputs(box) {
      box.find("input:not([type=submit]):not([type=radio])").val("");
      box.find("input[type=radio].esr-default").prop("checked", true);
      box.find("textarea").val("");
      box.find("select").val(null);
      $.each($("select option[data-default=1]", box), function (key, value) {
        $(value).parent().val($(value).val());
      });
      $.each($("input[data-default]", box), function (key, value) {
        $(value).val($(value).data("default"));
      });
    }

    function showEditBox() {
      var editBox = $(".esr-edit-box");
      cleanInputs(editBox);
      editBox.show();
    }

    function prepopulateTeacherData(row, is_edit) {
      var editBox = $(".esr-edit-box");

      $.each(editBox.find("input:not([type=submit])"), function (key, element) {
        if ($(element).attr("type") === "checkbox") {
          $(element).prop("checked", row.data($(element).data("name")));
        } else {
          $(element).val(row.data($(element).data("name")));
        }
      });

      $.each(editBox.find("select"), function (key, element) {
        $(element).val(parseInt(row.data($(element).data("name"))));
      });

      if (is_edit) {
        editBox.find("[name=teacher_id]").val(row.data("id"));
      } else {
        editBox.find("[name=teacher_id]").val("");
      }
    }

    function loadRow(item) {
      return item.closest(".esr-row");
    }

    function scroll_to_edit_box() {
      $("html, body").animate({
        scrollTop: $("#esr-edit-box").offset().top - 25
      }, 2000);
    }

    var esr_hide_actions = true;

    $(document).on("click", ".esr-row .actions button", function () {
      if ($(this).next().is(":visible")) {
        $(this).next().hide();
      } else {
        $(".esr-actions-box").hide();
        $(this).next().show();
        esr_hide_actions = false;
      }
    }).on("click", "body", function () {
      if (esr_hide_actions) {
        $(".esr-actions-box").hide();
      }
      esr_hide_actions = true;
    }).on("click", ".esr-add-new", function () {
      showEditBox();
      scroll_to_edit_box();
    }).on("click", ".esr-edit-box .close", function () {
      var editBox = $(".esr-edit-box");
      cleanInputs(editBox);
      editBox.hide();
    }).on("click", ".actions.esr-teacher .esr-action.edit", function () {
      showEditBox();
      prepopulateTeacherData(loadRow($(this)), true);
      scroll_to_edit_box();
    }).on("click", ".actions.esr-teacher .esr-action.duplicate", function () {
      showEditBox();
      prepopulateTeacherData(loadRow($(this)), false);
      scroll_to_edit_box();
    }).on("click", ".actions.esr-teacher .esr-action.deactivate", function () {
      var row = $(this).closest(".esr-row");
      var data = {
        "action": "esr_teacher_deactivate",
        "teacher_id": row.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        if (response !== "-1") {
          row.addClass("passed");
        }
      });
    }).on("click", ".actions.esr-teacher .esr-action.activate", function () {
      var row = $(this).closest(".esr-row");
      var data = {
        "action": "esr_teacher_activate",
        "teacher_id": row.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        if (response !== "-1") {
          row.removeClass("passed");
        }
      });
    });
  });
});