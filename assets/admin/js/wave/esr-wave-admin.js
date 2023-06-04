jQuery(function ($) {
  $(document).ready(function () {
    if ($(".esr-question").length !== 0) {
      tippy(".esr-question", {
        interactive: true,
        content: (reference) => reference.getAttribute('title')
      });
    }

    var esr_hide_actions = true;
    var esr_hide_download_actions = true;

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
      if (esr_hide_download_actions) {
        $(".esr-download-calendar-buttons").hide();
      }
      esr_hide_actions = esr_hide_download_actions = true;
    }).on("click", ".actions.esr-wave .esr-action.deactivate", function () {
      var row = $(this).closest(".esr-row");
      var data = {
        "action": "esr_set_wave_passed",
        "wave_id": row.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        if (response !== "-1") {
          row.addClass("passed");
        }
      });
    }).on("click", ".actions.esr-wave .esr-action.activate", function () {
      var row = $(this).closest(".esr-row");
      var data = {
        "action": "esr_set_wave_active",
        "wave_id": row.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        if (response !== "-1") {
          row.removeClass("passed");
        }
      });
    }).on("click", ".actions.esr-wave .esr-action.remove", function () {
      var answer = confirm("By confirming we will delete all data connected with this wave");
      if (answer === true) {
        var row = $(this).closest(".esr-row");
        var data = {
          "action": "esr_remove_wave",
          "wave_id": row.data("id")
        };
        $.post(ajaxurl, data, function (response) {
          if (response !== "-1") {
            row.remove();
          }
        });
      }
    }).on("click", ".esr_choose_calendar", function (e) {
      e.preventDefault();
      $(this).next(".esr-download-calendar-buttons").show();
      esr_hide_download_actions = false;
    }).on("click", ".esr-full-calendar-generation", function (e) {
      e.preventDefault();
      var row = $(this).closest(".esr-row");
      $.post(ajaxurl, {
        "action": "esr_ics_generate_full_calendar",
        "wave_id": $(row).data("id")
      }, function (response) {
        if (!jQuery.isEmptyObject(response)) {
          esr_process_calendar(response);
        }
      });
    }).on("click", ".esr-hall-calendar-generation", function (e) {
      e.preventDefault();
      var row = $(this).closest(".esr-row");
      $.post(ajaxurl, {
        "action": "esr_ics_generate_hall_calendar",
        "wave_id": $(row).data("id"),
        "hall_key": $(this).data("hall-key")
      }, function (response) {
        if (!jQuery.isEmptyObject(response)) {
          esr_process_calendar(response);
        }
      });
    });

    function esr_process_calendar(response) {
      var cal = ics("default", "Calendar", response.timezone);
      $.each(response.halls, function (key, waveData) {
        esr_add_hall_courses(cal, waveData);
      });
      cal.download("classes");
    }

    function esr_add_hall_courses(cal, waveData) {
      $.each(waveData.courses, function (key, course) {
        if ((course.from !== "") && (course.to !== "")) {
          cal.addEvent(course.title, course.id, course.title, waveData.hall, course.from, course.to, waveData.timezone, {
            freq: "WEEKLY",
            interval: 1,
            count: course.weeks
          });
        }
      });
    }
  });
});