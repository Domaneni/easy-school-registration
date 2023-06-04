jQuery(function ($) {
  $(document).ready(function () {
    $("body").on("click", ".actions.esr-student .esr-action.show", function (e) {
      e.preventDefault();
      var data = {
        "action": "esr_load_student_data",
        "student_id": $(this).closest("ul").data("id")
      };
      $.post(ajaxurl, data, function (response) {
        var user_data = jQuery.parseJSON(response);
        if (user_data) {
          $.each(user_data.data, function (key, value) {
            $(`td.esr-user-${key}`).empty().text(value);
          });
          $("td.esr-user-note textarea").data("user_id", user_data.user_id).empty().val(user_data.note);
          $("td.esr-user-registrations tbody").empty();
          $.each(user_data.registrations, function (key, value) {
            const {status, wave_name, course_name} = value;
            $("td.esr-user-registrations tbody").append(`<tr class="esr-row status-${status}"><td>${user_data.registration_status[status].title}</td><td>${wave_name}</td><td>${course_name}</td></tr>`);
          });
          $("td.esr-user-payments tbody").empty();
          $.each(user_data.payments, function (key, value) {
            const {status, wave_name, to_pay, payment} = value;
            $("td.esr-user-payments tbody").append(`<tr class="esr-row status-${status}"><td>${wave_name}</td><td>${user_data.payment_status[status].title}</td><td>${to_pay}</td><td>${(payment !== null ? payment : 0)}</td></tr>`);
          });
        }
      });
    }).on("click", ".actions.esr-student .esr-action.download", function (e) {
      e.preventDefault();
      if (confirm("Do you want to sent user data export for this student?")) {
        var data = {
          "action": "esr_send_student_export",
          "student_id": $(this).closest("ul").data("id")
        };
        $.post(ajaxurl, data, function (response) {
          $.notify({message: response.message}, {type: response.type});
        });
      }
    }).on("click", "button[name=esr_save_student_note]", function () {
      var note_wrapper = $(this).parent();
      var note_area = note_wrapper.find("textarea");
      var user_id = note_area.val();
      if (user_id !== "") {
        note_wrapper.find(".esr_save_spinner").css("display", "inline-block");
        var data = {
          "action": "esr_save_student_note",
          "user_id": note_area.data("user_id"),
          "note": user_id
        };

        $.post(ajaxurl, data, function (response) {
          note_wrapper.find(".esr_save_spinner").hide();
          note_wrapper.find(".esr_save_confirmed").css("display", "inline-block");

          setTimeout(function () {
            note_wrapper.find(".esr_save_confirmed").hide();
          }, 3000);
        });
      }
    });
  });
});
