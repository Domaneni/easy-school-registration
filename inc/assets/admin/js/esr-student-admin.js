jQuery((function(e){e(document).ready((function(){e("body").on("click",".esr-student-download-button",(function(t){t.preventDefault(),e.post(ajaxurl,{action:"esr_ics_generate_student_calendar",wave_id:e(this).data("wave-id")},(function(t){if(!jQuery.isEmptyObject(t)){var n=ics("default","Calendar",t.timezone);e.each(t.halls,(function(t,a){!function(t,n){e.each(n.courses,(function(e,a){""!==a.from&&""!==a.to&&t.addEvent(a.title,a.id,a.title,n.hall,a.from,a.to,n.timezone,{freq:"WEEKLY",interval:1,count:a.weeks})}))}(n,a)})),n.download("classes")}}))}))}))}));