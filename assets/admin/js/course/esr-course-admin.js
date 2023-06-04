jQuery(function ($) {
  $(document).ready(function () {
    window["moment-range"].extendMoment(moment);

    if ($(".esr-question").length !== 0) {
      tippy(".esr-question", {
        interactive: true,
        content: (reference) => reference.getAttribute('title')
      });
    }

    if ($("body").find(".esr-hall").size() === 0) {
      $("body").find(".esr-header .esr-hall-header").hide();
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
    }).on("click", ".actions.esr-course .esr-action.remove-forever", function () {
      var answer = confirm("Do you really want to completely delete this course with statistics and registrations?");
      if (answer === true) {
        var row = $(this).closest(".esr-row");
        var data = {
          "action": "esr_remove_course_forever",
          "course_id": row.data("id")
        };
        $.post(ajaxurl, data, function (response) {
          if (response !== "-1") {
            row.remove();
          }
        });
      }
    }).on("click", ".actions.esr-course .esr-action.deactivate", function () {
      var row = $(this).closest(".esr-row");
      var data = {
        "action": "esr_set_course_passed",
        "course_id": row.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        if (response !== "-1") {
          row.addClass("passed");
          row.find(".esr_course_status").text(response.status);
        }
      });
    }).on("click", ".actions.esr-course .esr-action.activate", function () {
      var row = $(this).closest(".esr-row");
      var data = {
        "action": "esr_set_course_active",
        "course_id": row.data("id")
      };
      $.post(ajaxurl, data, function (response) {
        if (response !== "-1") {
          row.removeClass("passed");
          row.find(".esr_course_status").text(response.status);
        }
      });
    }).on("click", "body", function () {
      if (esr_hide_actions) {
        $(".esr-actions-box").hide();
      }
      esr_hide_actions = true;
    }).on("change", "[name=is_solo]", function () {
      if ($(this).is(":checked")) {
        $(".hide_solo").hide();
        $(".show_solo").show();
      } else {
        $(".hide_solo").show();
        $(".show_solo").hide();
      }
    }).on("click", ".default-times a", function (e) {
      e.preventDefault();
      var ul = $(this).parent().parent().parent();
      $("input.esr-time-from", ul).val($(this).data("time-from"));
      $("input.esr-time-to", ul).val($(this).data("time-to"));
    }).on("click", ".esr-add-new-day", function () {
      var tr = $(this).parent().parent();
      var new_row = $(tr).clone();
      $(new_row).find("select").val("");
      $(new_row).find("input").val(null);

      var new_key = Math.floor(Math.random() * 26) + Date.now();
      $(new_row).data("name", new_key);
      $(new_row).find("select, input").each(function(i) {
        var name = $(this).attr('name');
        name = name.replace(tr.data("name"), new_key);
        $(this).attr('name', name);
      });

      $(tr).parent().append($(new_row));
    }).on("click", ".esr-remove-new-day", function () {
      $(this).parent().parent().remove();
    }).on("change", "select[name=hall_key]", function () {
      if ($(this).val() !== '') {
        var option = $(this).find("option[value=" + $(this).val() + "]");
        $("input[name=max_solo]").val($(option).data("solo"));
        $("input[name=max_leaders], input[name=max_followers]").val($(option).data("couples"));
      }
    }).on("change", "input[name=course_from]", function () {
      $("input[name=course_to]").attr("min", $(this).val());
    });

    if ($("select[name='wave_id'] option[data-course-from][data-course-to]").length > 0) {
      $("body").on("change", ".esr-edit-form :not(.esr-course-days) select[name='day']", function () {
        esr_change_course_dates();
      }).on("change", ".esr-edit-form select[name='wave_id']", function () {
        esr_change_course_dates();
      });

      function esr_change_course_dates() {
        if ($("select[name='day']").val() !== "") {
          var box = $(".esr-edit-form");
          var selected_wave = $("select[name='wave_id']", box).val();
          if ("" !== selected_wave) {
            var wave = $("select[name='wave_id'] option[value=" + selected_wave + "]", box);
            var day = $("select[name='day']", box).val();
            if (($("select[name='wave_id']", box).val() !== "")) {
              var course_from = wave.data("course-from");
              var course_to = wave.data("course-to");
              if ((undefined !== course_from) && (undefined !== course_to)) {
                var d = new Date(course_from);
                d.setDate(d.getDate() + ((7 - d.getDay()) % 7 + parseInt(day)) % 7);

                var moment_from = moment(course_from);
                var moment_to = moment(course_to);

                var moment_range = moment.rangeFromInterval("week", moment_to.diff(moment_from, "weeks"), moment(d));

                $("input[name='course_from']", box).val(moment_range.start.format("YYYY-MM-DD"));
                $("input[name='course_to']", box).val(moment_range.end.format("YYYY-MM-DD"));
              }
            }
          }
        }
      }
    }
  });
});