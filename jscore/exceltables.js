/*
This is for background excel file uploading
*/
function tbl_upload_excel_file(name) {
	/* Element By Id */
	function get_el(id) {
		return document.getElementById(id);
	}
	
	/* Opera version, used in add_event() */
	function get_opera_version() {
		var opver=navigator.userAgent.match(/Opera\s*([0-9.]+)/i);
		return (opver&&opver.length>1) ? parseFloat(opver[1]) : 0;
	}
	
	/* Adds event listener to the object */
	function add_event(obj, event_type, func) {
		if(obj.addEventListener && (get_opera_version() > 6 || get_opera_version() == 0)) {
			obj.addEventListener(event_type, func, false);
		} else if (obj.attachEvent) {
			obj.attachEvent("on" + event_type, func);
		}
	}
	
	/* Finding HTML objects */
	var iframe = window.frames["tbl_upload_buffer_" + name];
	var iframe_obj = get_el("tbl_upload_buffer_" + name);
	var form = get_el("tbl_upload_form_" + name);
	var textarea = get_el("tbl_textarea_" + name);
	var informer = get_el("tbl_informer_" + name);
	var container = get_el("tbl_inputs_container_" + name);
	var submit = get_el("tbl_file_submit_" + name);
	var inputs = container.getElementsByTagName("INPUT");
	var input = null;
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].getAttribute("type") == "file") {
			input = inputs[i];
			break;
		}
	}
	if (!input) return false;
	
	/* Checking for blank file input */
	if (!input.value) {
		//informer.className = "red bold";
		//informer.innerHTML = "Вы не выбрали файл для загрузки";
		alert("Вы не выбрали файл для загрузки");
		return false;
	}
	
	/* Remembering form action */
	if (!arguments.callee.action) {
		arguments.callee.action = form.action;
	}
	
	/* Defining onload iframe function */
	function iframe_load() {
		/* Debug */
		//alert(iframe.document.body.innerHTML);
		
		/* Checking blank submit */
		if (form.action == "about:blank") {
			return;
		}
	
		/* Enabling elements */
		textarea.readOnly = false;
		submit.disabled = false;
		informer.innerHTML = "";
    	
    	/* Checking retcode */
    	var result_cont = iframe.document.getElementById("errcode")
		var result = result_cont ? result_cont.innerHTML : "ERR_UNKNOWN";
		var err_msg = "";
		if (result == "OK") {
			textarea.value = iframe.document.getElementById("result").value;
			informer.className = "green";
			informer.innerHTML = "Загрузка произошла успешно";
		} else {
			switch (result) {
				case "ERR_UPLOAD_NO_FILE" : err_msg = "Вы не выбрали файл для загрузки"; break;
				case "ERR_UPLOAD_INI_SIZE" :
				case "ERR_UPLOAD_FORM_SIZE" : {
					var max_filesize = iframe.document.getElementById("max_filesize").innerHTML;
					err_msg = "Файл слишком большой (допустимый размер: до " + max_filesize + ")";
					break;
				}
				case "ERR_UPLOAD_PARTIAL" : err_msg = "Файл был загружен не полностью"; break;
				case "ERR_FILE_INVALID" : err_msg = "Файл не является корректным Excel файлом"; break;
				case "ERR_UNKNOWN" : 
				default : err_msg = "Неизвестная ошибка на сервере"; break;
			}
			informer.className = "red bold";
			informer.innerHTML = err_msg;
		}
	}
	
	/* Setting onload iframe function */
	if (!arguments.callee.set_onload[name]) {
		arguments.callee.set_onload[name] = true;
		add_event(iframe_obj, "load", iframe_load)
	}
	
	/* Moving input to the target form */
	var inputs = form.getElementsByTagName("INPUT");
	for (var i=0; i<inputs.length; i++) {
		if (inputs[i].getAttribute("type") == "file") {
			form.removeChild(inputs[i]);
		}
	}
	form.appendChild(input);
	
	/* Adding a new file-input under textarea */
	var new_input;
	if (navigator.appName == "Microsoft Internet Explorer") {
        new_input = document.createElement('<input type="file" name="excelfile" />');
    } else {
    	new_input = document.createElement("input");
    	new_input.setAttribute("type", "file");
    	new_input.setAttribute("name", "excelfile");
    }
	container.insertBefore(new_input, submit);
	container.insertBefore(document.createTextNode(" "), submit);
	
	/* Disabling elements */
	textarea.readOnly = true;
	submit.disabled = true;
	informer.className = "orange bold";
	
	/* Defining stop function */
	function stop_loading() {
		/* Enabling elements */
		textarea.readOnly = false;
		submit.disabled = false;
		/* Setting informer */
		informer.className = "red";
		informer.innerHTML = "Загрузка была прервана пользователем";
		/* Stop loading! */
    	form.action = "about:blank";
    	form.submit();
    }
	
	/* Setting informer + stop link */
	informer.innerHTML = "Идёт загрузка… ";
	var stop_link = document.createElement("SPAN");
    stop_link.onclick = stop_loading;
    stop_link.appendChild(document.createTextNode("[Остановить]"));
    stop_link.style.cursor = "pointer";
    stop_link.style.fontWeight = "normal";
    stop_link.style.color = "black";
	informer.appendChild(stop_link);
	
	/* Sending the file */
	form.action = arguments.callee.action;
	form.submit();
	return false;
}
tbl_upload_excel_file.set_onload = new Array();
tbl_upload_excel_file.action = "";
