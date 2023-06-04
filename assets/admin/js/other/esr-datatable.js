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