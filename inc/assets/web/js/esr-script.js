jQuery(function ($) {
  $(document).ready(function () {
    if ($(".esr-schedule-calendar").length > 0) {
      var visible_wave = $(".esr-schedule-calendar").first().data("wave-id");
      $(".esr-group-filter[data-wave=\"" + visible_wave + "\"], .esr-level-filter[data-wave=\"" + visible_wave + "\"]").show();
    }

    if ($(".esr-choosed-courses .esr-course-row").length > 0) {
      $(".esr-choosed-courses .esr-course-row").each(function ($k, $v) {
        var $course_id = $($v).data("course");
        var $course = $(".esr-course[data-id=\"" + $course_id + "\"]");

        $course.removeClass("esr-add").addClass("esr-remove");

        if ($(".esr-course-registration-form").data("showGroups") === 1) {
          $(".esr-choosed-courses .esr-group.wave_" + $course.data("wave") + " .esr-group-content .esr-group-" + $course.data("group") + " .esr-group-header").show();
        }

        if ($course.data("leader-enabled") == 0) {
          $(".esr-choosed-courses .esr-group .course-" + $course.data("id")).find(
            "select option[value=\"0\"]"
          ).remove();
        }
        if ($course.data("follower-enabled") == 0) {
          $(".esr-choosed-courses .esr-group .course-" + $course.data("id")).find(
            "select option[value=\"1\"]"
          ).remove();
        }
        if ($course.data("solo-enabled")) {
          $(".esr-choosed-courses .esr-group .course-" + $course.data("id") + "  .registration-info .esr-info-row").remove();
        }
      });

      coursesPriceRecount($(this).closest(".esr-registration-form-container"));
    }

    const textareaLimited = $(".esr-textarea-limit");
    if (textareaLimited.length > 0) {
      $.each(textareaLimited, function (key, area) {
        const maxLength = $(area).find("textarea").attr("maxlength");
        $(area).append("<span class='esr-area-limit'><span class='actual'>0</span><span class='max'> / " + maxLength + "</span></span>");
      });
      $("body").on("keyup paste", ".esr-textarea-limit textarea", function () {
        $(this).next(".esr-area-limit").find(".actual").text($(this).val().length);
      });
    }

    $("body").on("click", ".esr-add", function () {
      var wrapper = $(this).closest(".esr-registration-form-container");
      var $schedule = $(this).closest(".esr-schedules, .esr-schedules-mobile");
      var $course = $(this);
      var $courses = $(wrapper).find(".esr-course[data-id=" + $course.data("id") + "]");
      var $prepRow = $(wrapper).find(".esr-prep-form-row").html();

      $prepRow = $prepRow.replace(/%course-name%/g, $course.find(".esr-title").text());
      $prepRow = $prepRow.replace(/%course-price%/g, $course.data("price"));
      $prepRow = $prepRow.replace(/%course-id%/g, $course.data("id"));
      $prepRow = $prepRow.replace(/%course-wave%/g, $course.data("wave"));

      $multipleDaysCourses = $(".esr-course[data-id='" + $course.data("id") + "']", $schedule);

      if ($multipleDaysCourses.length === 1) {
        if ($course.data("day") !== "") {
          $prepRow = $prepRow.replace(/%course-day%/g, $course.data("day") + " - ");
        } else {
          $prepRow = $prepRow.replace(/%course-day%/g, $course.data("day"));
        }
        $prepRow = $prepRow.replace(/%course-start%/g, $course.data("start"));
      } else {
        var dayStart = "";
        $.each($multipleDaysCourses, function (k, c) {
          if (dayStart !== "") {
            dayStart += "<br>";
          }
          if ($(c).data("day") !== "") {
            dayStart += $(c).data("day") + " - ";
          } else {
            dayStart += $(c).data("day");
          }
          dayStart += $(c).data("start");
        });
        $prepRow = $prepRow.replace(/%course-day% %course-start%/g, dayStart);
      }

      $courses.removeClass("esr-add").addClass("esr-remove");

      if ($(wrapper).find(".esr-course-registration-form").data("showGroups") === 1) {
        $(wrapper).find(".esr-choosed-courses .esr-group.wave_" + $course.data("wave") + " .esr-group-content .esr-group-" + $course.data("group") + " .esr-group-header").show();
        $(wrapper).find(".esr-choosed-courses .esr-group.wave_" + $course.data("wave") + " .esr-group-content .esr-group-" + $course.data("group") + " .esr-sub-group-content").append($prepRow);
      } else {
        $(wrapper).find(".esr-choosed-courses .esr-group.wave_" + $course.data("wave") + " .esr-group-content").append($prepRow);
      }

      if ($course.data("leader-enabled") == 0) {
        $(wrapper).find(".esr-choosed-courses .esr-group .course-" + $course.data("id")).find(
          "select option[value=\"0\"]"
        ).remove();
      }
      if ($course.data("follower-enabled") == 0) {
        $(wrapper).find(".esr-choosed-courses .esr-group .course-" + $course.data("id")).find(
          "select option[value=\"1\"]"
        ).remove();
      }
      if ($course.data("solo-enabled")) {
        $(wrapper).find(".esr-choosed-courses .esr-group .course-" + $course.data("id") + "  .registration-info .esr-info-row").remove();
      }
      if ($course.data("enforce-partner")) {
        $(wrapper).find(".esr-choosed-courses .esr-group .course-" + $course.data("id") + " .esr-row-choose-partner").remove();
        var dancing_with = $(wrapper).find(".esr-choosed-courses .esr-group .course-" + $course.data("id") + " .esr-row-dancing-with");
        dancing_with.show();
        dancing_with.find(".esr-info-row-input.esr-dancing-with").prop("required", true);
      }

      coursesPriceRecount(wrapper);
      wavePriceRecount(wrapper, $course.data("wave"));
    }).on("click", ".esr-remove", function () {
      var wrapper = $(this).closest(".esr-registration-form-container");
      var $course = $(this);
      var $courses = $(wrapper).find(".esr-course[data-id=" + $course.data("id") + "]");

      $(wrapper).find(".esr-choosed-courses div[data-course=\"" + $course.data("id") + "\"]").remove();

      $courses.removeClass("esr-remove").addClass("esr-add");

      var group = $(".esr-choosed-courses .esr-group.wave_" + $course.data("wave") + " .esr-group-content .esr-group-" + $course.data("group"));
      if (group.find(".esr-sub-group-content .esr-course-row").length === 0) {
        group.find(".esr-group-header").hide();
      }

      coursesPriceRecount(wrapper);
      wavePriceRecount(wrapper, $course.data("wave"));
    }).each(function () {
      if ($(this).find(".esr-hall").length === 0) {
        $(this).find(".esr-header .esr-hall-header").hide();
      }
    }).on("change", ".esr-filter-schedule", function () {
      var key = $(this).attr("name");
      var newValue = $(this).val();

      $(".esr-schedule-calendar").hide();
      $(".esr-schedule-calendar[data-" + key + "=" + newValue + "]").show();

      $(".esr-group-filter, .esr-level-filter").hide();
      $(".esr-group-filter[data-wave=\"" + newValue + "\"], .esr-level-filter[data-wave=\"" + newValue + "\"]").show();
      scrollableSchedule();
    }).on("change", "input.choose_partner", function () {
      if ($(this).is(":checked")) {
        var partner_input_box = $(this).closest(".esr-info-row").next();
        if ($(this).val() === "0") {
          partner_input_box.hide();
          partner_input_box.find(".esr-info-row-input.esr-dancing-with").prop("required", false);
        } else {
          partner_input_box.show();
          partner_input_box.find(".esr-info-row-input.esr-dancing-with").prop("required", true);
        }
      }
    }).on("submit", "form.esr-course-registration-form", function (e) {
      e.preventDefault();
      var form = $(this);
      var wrapper = form.parent();
      $(".esr-error").remove(); //Remove old errors
      var registration_data = {};
      var spinner = esr_run_spinner(wrapper);
      $("input[name=esr-registration-submitted]", form).prop("disabled", true);
      registration_data["courses"] = {};
      var has_errors = esr_check_form_errors(form);

      if (form.find(".esr-choosed-courses .esr-course-row").length > 0) {
        form.find(".esr-choosed-courses .esr-course-row").each(function (key, row) {
          var course_id = $(row).data("course");
          registration_data["courses"][course_id] = {};
          registration_data["courses"][course_id]["course_id"] = course_id;

          if ($(row).find(".esr-dancing-as").length !== 0) {
            registration_data["courses"][course_id]["dancing_as"] = $(row).find(".esr-dancing-as").val();
          }

          if ($(row).find(".choose_partner").length !== 0) {
            registration_data["courses"][course_id]["choose_partner"] = $(row).find(".choose_partner:checked").val();
          }

          if ($(row).find(".esr-dancing-with").length !== 0) {
            registration_data["courses"][course_id]["dancing_with"] = $(row).find(".esr-dancing-with").val();
          }
        });

        //load user data
        registration_data["user_info"] = {};
        form.find(".esr-user-form input:not(.esr-confirm-email), .esr-user-form select, .esr-user-form textarea").each(function (key, input) {
          var value = $(input).val();
          if ($(input).attr("type") === "checkbox") {
            value = $(input).prop("checked");
          }
          registration_data["user_info"][$(input).attr("name")] = value;
        });

        form.find(".esr-other-inputs input, .esr-other-inputs select, .esr-other-inputs textarea").each(function (key, input) {
          var value = $(input).val();
          if ($(input).attr("type") === "checkbox") {
            value = $(input).prop("checked");
          }
          registration_data["user_info"][$(input).attr("name")] = value;
        });

        //send ajax
        var registration_data_json = JSON.stringify(registration_data);
        var data = {
          "action": "esr_process_registration",
          "registration_data": registration_data_json
        };

        $.post(esr_ajax_object.ajaxurl, data, function (response) {
        }).done(function (response) {
          if (response.hasOwnProperty("thank_you_text")) {
            form.find(".esr-choosed-courses").remove();
            form.remove();
            esr_stop_spinner(spinner, wrapper);
            wrapper.find(".esr-schedule-wrapper").empty().append(response.thank_you_text);
            $("html, body").animate({
              scrollTop: $(".esr-thank-you", wrapper).offset().top - 50
            }, 2000);
          } else if (response.hasOwnProperty("errors")) {
            $.each(response.errors.errors, function (key, message) {
              var keys = key.split(".");
              if (keys[0] === "user_info") {
                form.find("[name=" + keys[1] + "]").after("<div class=\"esr-error\">" + message[0] + "</div>");
              } else if (keys[0] === "courses") {
                if (keys[1] === "all") {
                  form.find(".esr-choosed-courses .esr-group-content").append("<div class=\"esr-error\">" + message[0] + "</div>");
                } else {
                  form.find(".esr-choosed-courses .esr-course-row[data-course=" + keys[1] + "]").append("<div class=\"esr-error\">" + message[0] + "</div>");
                }
              }
            });
            $("html, body").animate({
              scrollTop: form.find(".esr-choosed-courses").offset().top - 50
            }, 2000);
            esr_stop_spinner(spinner, wrapper);
            $("input[name=esr-registration-submitted]", form).prop("disabled", false);
          }
        });
      } else {
        form.find(".esr-choosed-courses .esr-group-content").append("<div class='esr-error'>" + form.data("no-courses") + "</div>");
        $("html, body").animate({
          scrollTop: form.find(".esr-choosed-courses").offset().top - 50
        }, 2000);
        esr_stop_spinner(spinner, wrapper);
        $("input[name=esr-registration-submitted]", form).prop("disabled", false);
      }
    }).on("click", ".esr-group-filter-button", function () {
      var filter = $(this).closest(".esr-group-filter");
      $(".esr-group-filter-button", filter).removeClass("esr-group-filter-active");
      $(this).addClass("esr-group-filter-active");
      var schedule = $(".esr-schedule-calendar[data-wave-id=" + $(filter).data("wave") + "]", $(this).closest(".esr-schedule-wrapper")); //Add wave control
      var group_id = $(this).data("group-id");
      var hide_courses = $(filter).hasClass("esr-group-filter-hide");
      esr_filter_opacity_show(schedule.find(".esr-course:not(.esr-empty), .esr-day-hall-schedule, .esr-halls-schedule"), hide_courses);
      if (group_id !== "all") {
        esr_filter_opacity_hide(schedule.find(".esr-course:not([data-group=" + group_id + "]):not(.esr-empty)"), hide_courses);

        $.each(schedule.find(".esr-day-hall-schedule"), function (key, value) {
          if ($(value).find(".esr-course:not(.esr-empty):visible").length === 0) {
            esr_filter_opacity_hide($(value), hide_courses);
          }
        });
        $.each(schedule.find(".esr-halls-schedule"), function (key, value) {
          if ($(value).find(".esr-day-hall-schedule:visible").length === 0) {
            esr_filter_opacity_hide($(value), hide_courses);
          }
        });

      }
    }).on("click", ".esr-level-filter-button", function () {
      var filter = $(this).closest(".esr-level-filter");
      var schedule = $(".esr-schedule-calendar[data-wave-id=" + $(filter).data("wave") + "]", $(this).closest(".esr-schedule-wrapper")); //Add wave control
      var level_id = $(this).data("level-id");
      var hide_courses = $(filter).hasClass("esr-level-filter-hide");
      esr_filter_opacity_show(schedule.find(".esr-course:not(.esr-empty), .esr-day-hall-schedule, .esr-halls-schedule"), hide_courses);
      if (level_id !== "all") {
        esr_filter_opacity_hide(schedule.find(".esr-course:not([data-level=" + level_id + "]):not(.esr-empty)"), hide_courses);

        $.each(schedule.find(".esr-day-hall-schedule"), function (key, value) {
          if ($(value).find(".esr-course:not(.esr-empty):visible").length === 0) {
            esr_filter_opacity_hide($(value), hide_courses);
          }
        });
        $.each(schedule.find(".esr-halls-schedule"), function (key, value) {
          if ($(value).find(".esr-day-hall-schedule:visible").length === 0) {
            esr_filter_opacity_hide($(value), hide_courses);
          }
        });

      }
    });

    function esr_filter_opacity_hide(value, hide_courses) {
      if (hide_courses) {
        value.hide();
      } else {
        value.css("opacity", 0.4);
      }
    }

    function esr_filter_opacity_show(value, hide_courses) {
      if (hide_courses) {
        value.show();
      } else {
        value.css("opacity", 1);
      }
    }

    function esr_check_form_errors(form) {
      //Check confirm email
      if (form.find(".esr-user-form input.esr-confirm-email").length > 0) {
        var email_input = form.find(".esr-user-form input[name=email]");
        var confirm_email_input = form.find(".esr-user-form input.esr-confirm-email");

        var $error_result = !(email_input.val().trim() === confirm_email_input.val().trim());
        if ($error_result) {
          $(email_input).parent().append("<div class=\"esr-error\">" + $(confirm_email_input).data("error-message") + "</div>");
          $(confirm_email_input).parent().append("<div class=\"esr-error\">" + $(confirm_email_input).data("error-message") + "</div>");
        }

        return $error_result;
      }

      return false;
    }

    function coursesPriceRecount(wrapper) {
      var finalSum = 0;

      esr_filter_unique_courses($(wrapper).find(".esr-schedules .esr-halls-schedule .esr-remove")).each(function () {
        finalSum += Number($(this).data("price"));
      });
      var price_template = $(wrapper).find(".esr-choosed-courses").data("price-template");
      if (finalSum > 0) {
        finalSum = esr_round_price(wrapper, finalSum);
        $(wrapper).find(".esr-total-price-count").data("total-price", finalSum).text(price_template.replace("[price]", finalSum));
      } else {
        $(wrapper).find(".esr-total-price-count").data("total-price", 0).text("");
      }
    }

    function wavePriceRecount(wrapper, wave_id) {
      var finalSum = 0;
      esr_filter_unique_courses($(wrapper).find(".esr-schedules .esr-halls-schedule .esr-remove[data-wave=\"" + wave_id + "\"]")).each(function () {
        finalSum += Number($(this).data("price"));
      });
      var price_template = $(wrapper).find(".esr-choosed-courses").data("price-template");
      if (finalSum > 0) {
        finalSum = esr_round_price(wrapper, finalSum);
        $(wrapper).find(".esr-group.wave_" + wave_id + " .esr-wave-price .esr-wave-price-count").data("wave-price", finalSum).text(price_template.replace("[price]", finalSum));
      } else {
        $(wrapper).find(".esr-group.wave_" + wave_id + " .esr-wave-price .esr-wave-price-count").data("wave-price", 0).text("");
      }
    }

    function esr_filter_unique_courses(allCourses) {
      var courses = {};
      var uniqueCourses = allCourses.filter(function () {
        var id = $(this).data("id");
        if (courses[id]) {
          return false;
        } else {
          courses[id] = true;
          return true;
        }
      });

      return uniqueCourses;
    }

    function esr_round_price(wrapper, price) {
      if ($(".esr-course-registration-form", wrapper).data("round-payments") == 1) {
        return price.toFixed(0);
      } else if (price % 1 != 0) {
        return price.toFixed(2);
      }

      return price;
    }

    if ($("#esr-thank-you").length > 0) {
      $("#ui-accordion-1-header-0 a").click();
      $("html, body").animate({
        scrollTop: $("#esr-thank-you").offset().top
      }, 2000);
    }

    if ($("#esr-errors").length > 0) {
      $("#ui-accordion-1-header-0 a").click();
      $("html, body").animate({
        scrollTop: $("#esr-errors").offset().top
      }, 2000);
    }

    if ($(".esr-schedules .esr-schedule-calendar").length > 0) {
      scrollableSchedule();
    }

    function scrollableSchedule() {
      $(".esr-schedules .schedule-by-day.esr-schedule-calendar").each(function () {
        var isVisible = $(this).is(":visible");
        var totalWidth = 0;
        var schedules = $(this).closest(".esr-schedules");

        $(this).find(".esr-row").each(function (index) {
          if (isVisible) {
            totalWidth += parseInt($(this).outerWidth(), 10);
          } else {
            totalWidth += parseInt($(this).actual("outerWidth"), 10);
          }
        });

        if (totalWidth !== 0) {
          $(this).css("width", totalWidth);
        }

        esr_check_automatic_zoom(schedules, $(this), totalWidth, isVisible);
      });
      $(".esr-schedules .schedule-by-hours.esr-schedule-calendar, .esr-schedules .schedule-by-hours-compact.esr-schedule-calendar").each(function () {
        var isVisible = $(this).is(":visible");
        var maxWidth = 0;
        var newWidth = 0;
        var zoomWidth = 0;
        var schedules = $(this).closest(".esr-schedules");

        $(this).find(".esr-row").each(function (rowIndex, rowBox) {
          $(rowBox).find(".esr-day-hall-schedule").each(function (schIndex, schBox) {
            var sumWidth = 0;
            $(schBox).find(" > *").each(function (index, box) {
              if (isVisible) {
                sumWidth += $(box).outerWidth();
              } else {
                sumWidth += $(box).actual("outerWidth");
              }
            });
            $(schBox).css("width", sumWidth);
            if (maxWidth < sumWidth) {
              maxWidth = sumWidth;
            }
            var maxHeight = Math.max.apply(null, $(schBox).find(".esr-course").map(function () {
              var newHeight = jQuery(this)[0].scrollHeight + 2;
              return ((isVisible ? jQuery(this).height : jQuery(this).actual("height")) >= newHeight) ? 0 : newHeight;
            }).get());
            if (maxHeight > 0) {
              $(schBox).css("height", maxHeight);
              $(schBox).find(".esr-course, .esr-hall").css("height", maxHeight);
            }
          });

          var newWidth = 0;
          if (isVisible) {
            newWidth = parseInt($(rowBox).find(".esr-day").outerWidth(), 10) + maxWidth;
          } else {
            newWidth = parseInt($(rowBox).find(".esr-day").actual("outerWidth"), 10) + maxWidth;
          }

          if (zoomWidth < newWidth) {
            zoomWidth = newWidth;
          }

          if (newWidth !== 0) {
            $(this).css("width", newWidth);
          }
        });

        esr_check_automatic_zoom(schedules, $(this), zoomWidth, isVisible);
      });
    }

    function esr_check_automatic_zoom(schedules, schedule, totalWidth, isVisible) {
      if (schedules.hasClass("esr-automatic-zoom")) {
        if (totalWidth !== 0) {
          if (isVisible) {
            schedule.css("transform", "scale(" + schedules.parent().outerWidth() / totalWidth + ")")
            .css("transform-origin", "0 0")
            .css("-moz-transform", "scale(" + schedules.parent().outerWidth() / totalWidth + ")")
            .css("-moz-transform-origin", "0 0");
          } else {
            schedule.css("transform", "scale(" + schedules.parent().actual("outerWidth") / totalWidth + ")")
            .css("transform-origin", "0 0")
            .css("-moz-transform", "scale(" + schedules.parent().actual("outerWidth") / totalWidth + ")")
            .css("-moz-transform-origin", "0 0");
          }
          schedules.closest(".esr-schedule-wrapper").css("overflow", "hidden");

          var checkTotalWidth = 0;
          schedule.find(".esr-row").each(function (index) {
            if (isVisible) {
              checkTotalWidth += parseFloat($(this)[0].getBoundingClientRect().width);
            } else {
              checkTotalWidth += parseFloat($(this)[0].getBoundingClientRect().width);
            }
          });

          if (checkTotalWidth > totalWidth) {
            schedule.css("width", Math.ceil(checkTotalWidth));
          }
        }
      }
    }

    function esr_run_spinner(wrapper) {
      var opts = {
        lines: 12, // The number of lines to draw
        length: 30, // The length of each line
        width: 17, // The line thickness
        radius: 45, // The radius of the inner circle
        scale: 1, // Scales overall size of the spinner
        corners: 1, // Corner roundness (0..1)
        color: "#ffffff", // CSS color or array of colors
        fadeColor: "transparent", // CSS color or array of colors
        speed: 0.7, // Rounds per second
        rotate: 0, // The rotation offset
        animation: "spinner-line-fade-quick", // The CSS animation name for the lines
        direction: 1, // 1: clockwise, -1: counterclockwise
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        className: "spinner", // The CSS class to assign to the spinner
        top: "80%", // Top position relative to parent
        left: "50%", // Left position relative to parent
        shadow: "0 0 1px transparent", // Box-shadow for the lines
        position: "absolute" // Element positioning
      };

      var spinner = new Spinner(opts).spin();
      $(".spinner-bg", wrapper).show();
      $(wrapper).append(spinner.el);
      return spinner;
    }

    function esr_stop_spinner(spinner, wrapper) {
      spinner.stop();
      $(".spinner-bg", wrapper).hide();
    }

    if ($(".esr-schedule-calendar").length > 0) {
      var visible_wave = $(".esr-schedule-calendar").first().data("wave-id");
      $(".esr-group-filter[data-wave=\"" + visible_wave + "\"], .esr-level-filter[data-wave=\"" + visible_wave + "\"]").show();

      if (window.location.search.substring(1).indexOf("esr_group_preload") >= 0) {
        var esr_pare_url = window.location.search.substring(1),
          esr_variables = esr_pare_url.split("&"),
          esr_parameter,
          i;

        for (i = 0; i < esr_variables.length; i++) {
          esr_parameter = esr_variables[i].split("=");

          if (esr_parameter[0] === "esr_group_preload") {
            $(".esr-group-filter-button[data-group-id='" + decodeURIComponent(esr_parameter[1]) + "']").trigger("click");
          }
        }
      }
    }
  });
});

/*! Copyright 2012, Ben Lin (http://dreamerslab.com/)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 1.0.19
 *
 * Requires: jQuery >= 1.2.3
 */
(function (a) {
  if (typeof define === "function" && define.amd) {
    define(["jquery"], a);
  } else {
    a(jQuery);
  }
}(function (a) {
  a.fn.addBack = a.fn.addBack || a.fn.andSelf;
  a.fn.extend({
    actual: function (b, l) {
      if (!this[b]) {
        throw"$.actual => The jQuery method \"" + b + "\" you called does not exist";
      }
      var f = {absolute: false, clone: false, includeMargin: false, display: "block"};
      var i = a.extend(f, l);
      var e = this.eq(0);
      var h, j;
      if (i.clone === true) {
        h = function () {
          var m = "position: absolute !important; top: -1000 !important; ";
          e = e.clone().attr("style", m).appendTo("body");
        };
        j = function () {
          e.remove();
        };
      } else {
        var g = [];
        var d = "";
        var c;
        h = function () {
          c = e.parents().addBack().filter(":hidden");
          d += "visibility: hidden !important; display: " + i.display + " !important; ";
          if (i.absolute === true) {
            d += "position: absolute !important; ";
          }
          c.each(function () {
            var m = a(this);
            var n = m.attr("style");
            g.push(n);
            m.attr("style", n ? n + ";" + d : d);
          });
        };
        j = function () {
          c.each(function (m) {
            var o = a(this);
            var n = g[m];
            if (n === undefined) {
              o.removeAttr("style");
            } else {
              o.attr("style", n);
            }
          });
        };
      }
      h();
      var k = /(outer)/.test(b) ? e[b](i.includeMargin) : e[b]();
      j();
      return k;
    }
  });
}));