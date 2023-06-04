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

    if ($(".esr-color-picker").length > 0) {
      $(".esr-color-picker").wpColorPicker();
    }

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
                    $.each(d.split("<br>"), function (k, t) {
                      if (t !== "") {
                        if ($.inArray(t, mf_data) === -1) {
                          select.append("<option value=\"" + t + "\">" + t + "</option>");
                          mf_data.push(t);
                        }
                      }
                    });
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
          jQuery(".dataTables_filter input[type=search]").val($("[data-cin-student-id]").data("cin-student-id")).trigger("keyup");
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

    function prepopulatePaymentData(row, is_edit) {
      var editBox = $(".esr-edit-box");
      editBox.find("[name=user_email]").val(row.data("email"));
      editBox.find("[name=wave_id]").val(row.data("wave_id"));
      editBox.find("[name=payment_status]").val("paid");
      editBox.find("[name=payment]").val(parseFloat(row.data("to_pay")));
      editBox.find("[name=note]").val(row.data("note"));
      $("tr.payment").show();

      if (is_edit) {
        editBox.find("[name=payment_id]").val(row.data("id"));
        editBox.find("[name=payment]").val(parseFloat(row.data("payment")));
      }
    }

    function loadRow(item) {
      return item.closest(".esr-row");
    }

    var esr_hide_actions = true;

    $(document).on("click", ".esr-add-new", function () {
      showEditBox();
    }).on("click", ".esr-edit-box .close", function () {
      var editBox = $(".esr-edit-box");
      cleanInputs(editBox);
      editBox.hide();
    }).on("click", ".actions.esr-payment .esr-action.confirm-payment", function () {
      showEditBox();
      prepopulatePaymentData(loadRow($(this)), false);
    }).on("click", ".actions.esr-payment .esr-action.edit", function () {
      showEditBox();
      prepopulatePaymentData(loadRow($(this)), true);
    }).on("click", ".actions.esr-payment .esr-action.forgive-payment", function () {
      var data = {
        "action": "esr_forgive_payment",
        "payment_id": $(this).closest(".esr-row").data("id")
      };
      // We can also pass the url value separately from ajaxurl for front end AJAX implementations
      /** global: ajaxurl */
      $.post(ajaxurl, data, function (response) {
        if (response !== -1) {
          console.log(response);
        }
      });
    }).on("click", ".actions.esr-payment .esr-action.disable-registration", function () {
      var user_id = $(this).closest(".esr-row").data("user_id");
      var data = {
        "action": "esr_disable_student_registrations",
        "user_id": user_id
      };
      // We can also pass the url value separately from ajaxurl for front end AJAX implementations
      /** global: ajaxurl */
      $.post(ajaxurl, data, function (response) {
        if (response !== -1) {
          $(".esr-row[data-user_id=" + user_id + "]").addClass("esr-disable-registrations");
        }
      });
    }).on("click", ".actions.esr-payment .esr-action.enable-registration", function () {
      var user_id = $(this).closest(".esr-row").data("user_id");
      var data = {
        "action": "esr_enable_student_registrations",
        "user_id": user_id
      };
      // We can also pass the url value separately from ajaxurl for front end AJAX implementations
      /** global: ajaxurl */
      $.post(ajaxurl, data, function (response) {
        if (response !== -1) {
          $(".esr-row[data-user_id=" + user_id + "]").removeClass("esr-disable-registrations");
        }
      });
    }).on("input", ".esr-range", function () {
      $(this).prev().html($(this).val());
    }).on("click", "[name=esr_payment_submit]", function (e) {
      e.preventDefault();
      var $paymentForm = $(".esr-edit-box");
      var $email = $paymentForm.find("[name=user_email]").val();
      $(".esr-error", $paymentForm).remove();

      var data = {
        "action": "esr_payment_save_payment",
        "user_email": $paymentForm.find("[name=user_email]").val(),
        "wave_id": $paymentForm.find("[name=wave_id]").val(),
        "payment": $paymentForm.find("[name=payment]").val(),
        "payment_status": $paymentForm.find("[name=payment_status]").val(),
        "payment_type": $paymentForm.find("[name=payment_type]").val(),
        "esr_payment_email_confirmation": $paymentForm.find("[name=esr_payment_email_confirmation]").is(":checked"),
        "note": $paymentForm.find("[name=note]").val()
      };
      // We can also pass the url value separately from ajaxurl for front end AJAX implementations
      /** global: ajaxurl */
      $.post(ajaxurl, data, function (response) {
        if (response !== -1) {

          var data = jQuery.parseJSON(response);
          if (!data.hasOwnProperty("error")) {
            var editBox = $(".esr-edit-box");
            cleanInputs(editBox);
            editBox.hide();
            var payment_row = $("tr[data-email=\"" + $email + "\"]");

            payment_row.find(".status").text(data.payment_status_title);
            payment_row.find(".payment-type").text(data.payment_type);
            payment_row.find(".student-paid").html(data.payment);

            payment_row.data("note", data.payment_note);
            if (data.payment_note !== "") {
              payment_row.find(".esr-note").html("<span class='dashicons dashicons-admin-comments esr-show-note' title='" + data.payment_note + "' style='display: block;'></span><span class='dashicons dashicons-welcome-comments esr-hide-note' style='display: none;'></span><span class='esr-note-message' style='display: none;'>" + data.payment_note + "</span>");
            }

            var classes = payment_row[0].className.match(/paid\-status\-[0-9]/gi);
            if (classes.length > 0) {
              $(payment_row).removeClass(classes[0]).addClass("paid-status-" + data.payment_status);
            }

            if ($(".esr-payments-table tr").length > 1) {
              //$(".esr-payments-table").ddTableFilter();
            }
          } else {
            if (data.error.hasOwnProperty("student")) {
              $(".esr-student td").append("<span class='esr-error'>" + data.error.student + "</span>");
            }
          }
        }
      });
    }).on("change", "[name=payment_status]", function () {
      if ($("[name=payment_status]").val() === "paid") {
        $("tr.payment").show();
      } else {
        $("tr.payment").hide();
      }
    }).on("change", ".esr-toggle-on-change", function () {
      if ($(this).is(":checked")) {
        $($(this).data("show")).show();
        $($(this).data("hide")).hide();
      } else {
        $($(this).data("show")).hide();
        $($(this).data("hide")).show();
      }
    }).on("click", ".esr-show-note", function () {
      var parent = $(this).parent();
      $(".esr-hide-note, .esr-note-message", parent).css("display", "block");
      $(".esr-show-note", parent).hide();
    }).on("click", ".esr-hide-note", function () {
      var parent = $(this).parent();
      $(".esr-hide-note, .esr-note-message", parent).hide();
      $(".esr-show-note", parent).css("display", "block");
    }).on("click", ".esr-show-all-notes", function () {
      $(".esr-header-note." + $(this).data("class") + " .esr-hide-all-notes, ." + $(this).data("class") + " .esr-hide-note, ." + $(this).data("class") + " .esr-note-message").css("display", "block");
      $(".esr-header-note." + $(this).data("class") + " .esr-show-all-notes, ." + $(this).data("class") + " .esr-show-note").hide();
    }).on("click", ".esr-hide-all-notes", function () {
      $(".esr-header-note." + $(this).data("class") + " .esr-show-all-notes, ." + $(this).data("class") + " .esr-show-note").css("display", "block");
      $(".esr-header-note." + $(this).data("class") + " .esr-hide-all-notes, ." + $(this).data("class") + " .esr-hide-note, ." + $(this).data("class") + " .esr-note-message").hide();
    }).on("click", ".esr-add-list-item", function () {
      var row = $(this).parent().find("tr:last");
      var clone = row.clone();
      var count = row.data("key") + 1;
      clone.find("td input").val("");
      clone.find("input").each(function () {
        var name = $(this).attr("name");
        name = name.replace(/\[(\d+)\]/, "[" + parseInt(count) + "]");
        $(this).attr("name", name).attr("id", name);
      });
      clone.find("label").each(function () {
        var name = $(this).attr("for");
        name = name.replace(/\[(\d+)\]/, "[" + parseInt(count) + "]");
        $(this).attr("for", name);
      });
      clone.find(".esr-key-container").text(count);
      clone.data("key", count);
      clone.insertAfter(row);
      return false;
    }).on("click", ".esr_remove_list_item", function () {
      var list_items = $(this).closest(".esr_list_items");
      var tax_rates = list_items.find("tr:visible");
      var count = tax_rates.length;

      if (count === 2) {
        list_items.find("input[type=\"text\"]").val("");
      } else {
        $(this).closest("tr").remove();
      }
      return false;
    }).on("click", ".esr-row .actions button", function () {
      if ($(this).next().is(":visible")) {
        $(this).next().hide();
      } else {
        $(".esr-actions-box").hide();
        $(this).next().show();
        esr_hide_actions = false;
      }
    }).on("keypress keyup blur", ".esr-allow-decimal", function (event) {
      $(this).val($(this).val().replace(/[^0-9\.]/g, ""));
      if (($(this).val().indexOf(".") != -1 || !$.inArray(event.which, [37, 39, 8, 46])) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
      }
    }).on("click", "body", function () {
      if (esr_hide_actions) {
        $(".esr-actions-box").hide();
      }
      esr_hide_actions = true;
    }).on("change", "input[name=esr-select-all]", function () {
      $("input[name=\"esr_choosed_users[]\"]", $(this).closest("table")).prop("checked", $(this).is(":checked"));
    }).on("click", ".show-email-example", function () {
      var $parent = $(this).parent();
      $(".esr-email-popup", $parent).html($("textarea", $parent).val());
    }).on("click", ".esr-row.registration-row .actions button", function (e) {
      let box = $(".esr-actions-box");
      const row = $(this).closest(".esr-row");
      const offset = $(this).offset();

      box.data("id", $(row).data("id"));
      $(".esr-action, .esr-show-status-0, .esr-show-status-1", box).show();

      if (row.hasClass("status-1")) {
        $(".esr-action.remove-forever", box).hide();
      }
      if (row.hasClass("status-2")) {
        $(".esr-action.confirm, .esr-action.remove-forever", box).hide();
      }
      if (row.hasClass("status-3")) {
        $(".esr-action.edit, .esr-action.remove", box).hide();
      }
      if (row.hasClass("free-registration-status-0")) {
        $(".esr-action .esr-show-status-0", box).hide();
      }
      if (row.hasClass("free-registration-status-1")) {
        $(".esr-action .esr-show-status-1", box).hide();
      }

      box.css("top", offset.top - 2).css("left", offset.left - $(this).closest(".esr-settings").offset().left + 24).show();
    }).on("click", ".esr-deactivate-license", function () {
      $(this).prev().val("");
    }).on("click", ".esr-teacher-calendar-generation", function (e) {
      e.preventDefault();
      let row = $(this).closest(".esr-row");
      $.post(ajaxurl, {
        "action": "esr_ics_generate_teacher_calendar",
        "wave_id": $(this).data("wave-id")
      }, function (response) {
        if (!jQuery.isEmptyObject(response)) {
          esr_process_calendar(response);
        }
      });
    }).on("change", "input[name=esr_hide_passed_courses]", function () {
      var data = {
        "action": "esr_toggle_passed_courses",
        "is_checked": $(this).is(":checked")
      };
      // We can also pass the url value separately from ajaxurl for front end AJAX implementations
      /** global: ajaxurl */
      $.post(ajaxurl, data, function (response) {
        window.location.reload();
      });
    });

    $(".esr-settings-registrations .dataTables_scrollBody").on("scroll", function () {
      let box = $(".esr-actions-box");
      let registration = $(`.esr-row[data-id=${(box).data("id")}]`);
      const offset = $("button", registration).offset();
      if (offset !== undefined) {
        const left = offset.left - $(registration).closest(".esr-settings").offset().left + 24;
        box.css("left", left).show();

        if (left <= 0) {
          box.css("visibility", "hidden");
        } else {
          box.css("visibility", "visible");
        }
      }
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