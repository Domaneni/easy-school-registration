jQuery(function ($) {

  $(document).on("click", ".esr-edit-box .close", function () {
    var editBox = $(".esr-edit-box");
    cleanInputs(editBox);
    editBox.hide();
  }).on("click", ".esr-actions-box.esr-registrations-actions .esr-action.edit", function () {
    showEditBox();
    let box = $(this).closest(".esr-actions-box");
    let registration = $(`.esr-row[data-id=${(box).data("id")}]`);
    prepopulateRegistrationData(registration);
    $("html, body").animate({
      scrollTop: $("#esr-edit-box").offset().top - 25
    }, 2000);
  });

  function cleanInputs(box) {
    box.find("input:not([type=submit])").val("");
    box.find("textarea").val("");
    box.find("select").val(null);
  }

  function showEditBox() {
    var editBox = $(".esr-edit-box");
    cleanInputs(editBox);
    editBox.show();
  }

  function prepopulateRegistrationData(row) {
    var editBox = $(".esr-edit-box");
    editBox.find("[name=student_email]").val(row.data("student-email"));
    editBox.find("[name=course_id]").val(parseInt(row.data("course-id")));
    editBox.find("[name=dancing_as]").val(parseInt(row.data("dancing-as")));
    editBox.find("[name=dancing_with]").val(row.data("dancing-with"));
    editBox.find("[name=partner_email]").val(row.data("partner-email"));
    editBox.find("[name=registration_id]").val(row.data("id"));
  }

  // remove user from class
  $("body").on("click", "[name=esr_registration_edit_submit]", function (e) {
    e.preventDefault();
    var editBox = $(".esr-edit-box");
    $(".esr-error", editBox).remove();
    var data = {
      "action": "esr_edit_registration",
      "registration_id": editBox.find("[name=registration_id]").val(),
      "student_email": editBox.find("[name=student_email]").val(),
      "course_id": editBox.find("[name=course_id]").val(),
      "dancing_as": editBox.find("[name=dancing_as]").val(),
      "dancing_with": editBox.find("[name=dancing_with]").val(),
      "partner_email": editBox.find("[name=partner_email]").val()
    };
    $.post(ajaxurl, data, function (response) {
      if (response !== "-1") {
        var newData = jQuery.parseJSON(response);

        if (!newData.hasOwnProperty("error")) {
          var editRow = $(".esr-row[data-id=" + editBox.find("[name=registration_id]").val() + "]");
          cleanInputs(editBox);
          editBox.hide();

          if (newData.hasOwnProperty("student")) {
            if (newData.student.hasOwnProperty("name")) {
              editRow.find(".student-name").html(newData.student.name);
            }
            if (newData.student.hasOwnProperty("email")) {
              editRow.data("student-email", newData.student.email);
              editRow.find(".student-email").html(newData.student.email);
            }
          }
          if (newData.hasOwnProperty("dancing_as")) {
            if (newData.dancing_as.hasOwnProperty("id")) {
              editRow.data("dancing-as", newData.dancing_as.id);
            }
            if (newData.dancing_as.hasOwnProperty("text")) {
              editRow.find(".dancing-as").html(newData.dancing_as.text);
            }
          }
          if (newData.hasOwnProperty("partner")) {
            if (newData.partner.hasOwnProperty("name")) {
              editRow.find(".partner-name").html(newData.partner.name);
            }
            if (newData.partner.hasOwnProperty("email")) {
              editRow.data("partner-email", newData.partner.email);
            }
          }
          if (newData.hasOwnProperty("dancing_with")) {
            if (newData.dancing_with.hasOwnProperty("email")) {
              editRow.find(".dancing-with").html(newData.dancing_with.email);
              editRow.data("dancing-with", newData.dancing_with.email);
            }
          }
          if (newData.hasOwnProperty("course")) {
            editRow.data("course-id", newData.course.id).find(".course").html(newData.course.id + " - " + newData.course.title + " (" + newData.course.day_title + ")");
          }
          if (newData.hasOwnProperty("confirm_registrations")) {
            if (newData.confirm_registrations.hasOwnProperty("student")) {
              editRow.removeClass("status-1").removeClass("status-3").addClass("status-2").find(".status").text(response.status_title);
            }
            if (newData.confirm_registrations.hasOwnProperty("partner")) {
              $(".esr-row[data-id=" + newData.confirm_registrations.partner.reg_id + "]").removeClass("status-1").removeClass("status-3").addClass("status-2").find(".status").text(response.status_title);
            }
          }
        } else {
          if (newData.error.hasOwnProperty("student")) {
            $(".esr-student td").append("<span class='esr-error'>" + newData.error.student + "</span>");
          } else if (newData.error.hasOwnProperty("partner")) {
            $(".esr-registered-partner td").append("<span class='esr-error'>" + newData.error.partner + "</span>");
          }
        }
      }
    });
  }).on("click", ".esr-actions-box.esr-registrations-actions .esr-action.remove", function () {
    addWaiting();
    let box = $(this).closest(".esr-actions-box");
    let registration = $(`.esr-row[data-id=${(box).data("id")}]`);
    var data = {
      "action": "esr_remove_user_course_registration",
      "registration_id": registration.data("id")
    };
    $.post(ajaxurl, data, function (response) {
      if (response !== "-1") {
        registration.removeClass("status-1").removeClass("status-2").addClass("status-3").find(".status").text(response.status_title);
        registration.find("td.dancing-with").empty();
        registration.find("td.partner-name").empty();

        if (Number.parseInt(response.partner_registration) > 0) {
          var $partner = $("tr[data-id='" + response.partner_registration + "']");
          $partner.find("td.dancing-with").empty();
          $partner.find("td.partner-name").empty();
        }
      }
      removeWaiting();
    });
  }).on("click", ".esr-actions-box.esr-registrations-actions .esr-action.remove-forever", function () {
    let box = $(this).closest(".esr-actions-box");
    let registration = $(`.esr-row[data-id=${(box).data("id")}]`);
    var data = {
      "action": "esr_remove_registration_forever",
      "registration_id": registration.data("id")
    };
    $.post(ajaxurl, data, function (response) {
      if (response !== "-1") {
        registration.remove();
      }
    });
  }).on("click", ".esr-actions-box.esr-registrations-actions .esr-action.update-free-registration", function () {
    let action_button = $(this);
    let box = $(this).closest(".esr-actions-box");
    let registration = $(`.esr-row[data-id=${(box).data("id")}]`);

    if (registration.hasClass("free-registration-status-0")) {
      let data = {
        "action": "esr_set_free_registration",
        "registration_id": registration.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        registration.removeClass("free-registration-status-0").addClass("free-registration-status-" + response.free_registration);
        $.notify({message: response.message}, {type: response.type});
      });
    } else if (registration.hasClass("free-registration-status-1")) {
      let data = {
        "action": "esr_set_paid_registration",
        "registration_id": registration.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        registration.removeClass("free-registration-status-1").addClass("free-registration-status-" + response.free_registration);
        $.notify({message: response.message}, {type: response.type});
      });
    }

  }).on("click", ".esr-actions-box.esr-registrations-actions .esr-action.confirm", function () {
    addWaiting();
    let box = $(this).closest(".esr-actions-box");
    let registration = $(`.esr-row[data-id=${(box).data("id")}]`);
    var data = {
      "action": "esr_add_user_course_registration",
      "esr_registration_id": registration.data("id")
    };
    $.post(ajaxurl, data, function (response) {
      if (response !== "-1") {
        registration.removeClass("status-1").removeClass("status-3").addClass("status-2").find(".status").text(response.status_title);
      }
      removeWaiting();
    });
  });

  function addWaiting() {
    $("body").append("<div id=\"esr-waiting\"></div>");
    var opts = {
      lines: 13 // The number of lines to draw
      , length: 28 // The length of each line
      , width: 14 // The line thickness
      , radius: 42 // The radius of the inner circle
      , scale: 1 // Scales overall size of the spinner
      , corners: 1 // Corner roundness (0..1)
      , color: "#000" // #rgb or #rrggbb or array of colors
      , opacity: 0.25 // Opacity of the lines
      , rotate: 0 // The rotation offset
      , direction: 1 // 1: clockwise, -1: counterclockwise
      , speed: 1 // Rounds per second
      , trail: 60 // Afterglow percentage
      , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
      , zIndex: 2e9 // The z-index (defaults to 2000000000)
      , className: "spinner" // The CSS class to assign to the spinner
      , top: "50%" // Top position relative to parent
      , left: "50%" // Left position relative to parent
      , shadow: false // Whether to render a shadow
      , hwaccel: false // Whether to use hardware acceleration
      , position: "absolute" // Element positioning
    };
    var target = document.getElementById("esr-waiting");
    /** global: spinner, Spinner */
    var spinner = new Spinner(opts).spin(target);
  }

  function removeWaiting() {
    $("#esr-waiting").remove();
  }
});
