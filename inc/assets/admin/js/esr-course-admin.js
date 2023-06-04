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
jQuery(function ($) {
  $(document).ready(function () {
    if ($(".esr-datatable:not(.esr-all-false)").length) {
      $.each($(".esr-datatable:not(.esr-all-false)"), function (key, table) {
        var datatableSettings = {
          "lengthMenu": [
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
          ],
          "pageLength": 100,
          order: [[0, "desc"]],
          "aoColumnDefs": [
            {"bSortable": false, "aTargets": ["no-sort"]}
          ],
          dom: "lBfrtip",
          buttons: [
            "colvis"
          ],
          columnDefs: [{
            "targets": "no-sort",
            "orderable": false
          }],
          initComplete: function () {
            this.api().columns().every(function () {
              var column = this;
              var multiple_filters = $(column.header()).hasClass("esr-multiple-filters");

              $(column.header()).data("label", $(column.header()).text());
              if (!$(column.header()).hasClass("esr-filter-disabled")) {
                var select = $("<select><option value=\"\">" + $(column.header()).text() + "</option></select>")
                .appendTo($(column.header()).empty())
                .on("change", function () {
                  var val = $.fn.dataTable.util.escapeRegex(
                    $(this).val()
                  );

                  if (multiple_filters) {
                    column
                    .search(val ? val + "+" : "", true, false)
                    .draw();
                  } else {
                    column
                    .search(val ? "^" + val + "$" : "", true, false)
                    .draw();
                  }
                });

                var mf_data = [];

                column.data().unique().sort().each(function (d) {
                  if (multiple_filters) {
                    $.each(d.split(", "), function (k, t) {
                      if (t !== "") {
                        if ($.inArray(t, mf_data) === -1) {
                          select.append("<option value=\"" + t + "\">" + t + "</option>");
                          mf_data.push(t);
                        }
                      }
                    })
                  } else {
                    if (d !== "") {
                      select.append("<option value=\"" + d + "\">" + d + "</option>");
                    }
                  }
                });
              }
            });

            if ($("[data-cin-course-id]").length > 0) {
              jQuery("th.course select option").filter(function () {
                return (jQuery(this).text().indexOf($("[data-cin-course-id]").data("cin-course-id") + " - ")) === 0;
              }).prop("selected", true).trigger("change");
            }
          }
        };

        if ($(table).hasClass("esr-email-export")) {
          datatableSettings.buttons.push({
            extend: "copyHtml5",
            text: "Copy Emails",
            title: "",
            header: false,
            exportOptions: {
              columns: [".esr-student-email:visible:not(.esr-hide-print)"],
              customizeData: function (data) {
                var outputArray = [];
                var outputEmails = [];
                $.each(data.body, function (key, email) {
                  if ($.inArray(email[0], outputEmails) === -1) {
                    outputArray.push(email);
                    outputEmails.push(email[0]);
                  }
                });
                data.body = outputArray;
              }
            }
          });
        }

        if ($(table).hasClass("esr-newsletter-email-export")) {
          datatableSettings.buttons.push({
            extend: "copyHtml5",
            text: "Copy newsletter emails",
            title: "",
            header: false,
            exportOptions: {
              columns: [".esr-student-email:visible:not(.esr-hide-print)"],
              rows: [".esr-has-newsletter"],
              customizeData: function (data) {
                var outputArray = [];
                var outputEmails = [];
                $.each(data.body, function (key, email) {
                  if ($.inArray(email[0], outputEmails) === -1) {
                    outputArray.push(email);
                    outputEmails.push(email[0]);
                  }
                });
                data.body = outputArray;
              }
            }
          });
        }

        if ($(table).hasClass("esr-copy-table")) {
          datatableSettings.buttons.push({
            extend: "copyHtml5",
            text: "Copy Table",
            title: "",
            exportOptions: {
              columns: [":visible:not(.esr-hide-print)"],
              format: {
                header: function (data, row, column, node) {
                  return $(column).data("label");
                }
              }
            }
          });
        }

        if ($(table).hasClass("esr-excel-export")) {
          datatableSettings.buttons.push({
            extend: "excel",
            text: "Excel",
            title: "",
            exportOptions: {
              columns: [":visible:not(.esr-hide-print)"],
              format: {
                header: function (data, row, column, node) {
                  return $(column).data("label");
                }
              }
            }
          });
        }

        if ($(table).hasClass("esr-enable-scroll")) {
          datatableSettings.scrollX = true;
          datatableSettings.scrollCollapse = true;
          datatableSettings.fixedColumns = true;
          datatableSettings.columnDefs.push({
            width: "20%",
            targets: 0
          });
        }

        if ($(table).data("idisplaylengt") !== "undefined") {
          datatableSettings.iDisplayLength = $(table).data("idisplaylengt");
        }

        var datatable = $(table).DataTable(datatableSettings);

        if ($(table).hasClass("esr-enable-scroll")) {
          datatable.columns.adjust().draw();
        }

        if ($("[data-cin-student-id]").length > 0) {
          jQuery(".dataTables_filter input[type=search]").val($("[data-cin-student-id]").data("cin-student-id")).trigger('keyup');
        }
      });
    } else if ($(".esr-datatable.esr-all-false").length) {
      $(".esr-datatable.esr-all-false").DataTable({
        bFilter: false,
        bInfo: false,
        bPaginate: false,
        bLength: false,
        bSort: false
      });
    }
  });
});