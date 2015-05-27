function MultiRefClass() {
	// элементы, которые загружаются в AddElement
	// нужны при откате изменений назад, при нажатии кнопки "отмена"
	// формат id -> value
	this.defaultElements = new Array();
	// загружаются в AddElement, 
	// могут изменятся в ModifyElements
	// формат id -> value
	this.elements = new Array();

	// поле, в котором хранится список выбранных id
	this.inputField = "";
	// поле с идентификатором изменений
	this.inputIsModified = "";
	// div со списком всех доступных элементов
	this.divAllItems = "";
	// div со списком выбранных элеменов
	this.divSelectedItems = "";
	// ссылка-переключиталь режима "редактирование"/"отображение выбранных"
	this.hrefModeChanger = "";

	this.isActive = false;
	this.isModified = false;

	
	this.construct = function() {
		this.name = name;

		this.inputField = document.getElementById("ref_selectedids") || false;
		if (!this.inputField) {
			alert("input поле ref__selectedids не существует");
			return false;
		}

		this.inputIsModified = document.getElementById("ref_ismodified") || false;
		if (!this.inputIsModified) {
			alert("input поле ref_ismodified не существует");
			return false;
		}

		this.divAllItems = document.getElementById("ref_div_all");
		if (!this.inputIsModified) {
			alert("div элемент ref_div_all не существует");
			return false;
		}

		this.divSelectedItems = document.getElementById("ref_div_selected") || false;
		if (!this.inputIsModified) {
			alert("div элемент ref_div_selected не существует");
			return false;
		}

		this.hrefModeChanger = document.getElementById("ref_modechanger");
		if (!this.inputIsModified) {
			alert("a элемент ref_modechanger не существует");
			return false;
		}
		
		
		
		this.divAllItems.style.display = "none";
		return true;
	}

	
	this.ChangeActive = function() {
		
		this.isActive = (this.isActive) ? false : true
		
		
		this.divAllItems.style.display = (this.isActive) ? "block" : "none";
		this.divSelectedItems.style.display = (this.isActive) ? "none" : "block";
		
		this.hrefModeChanger.style.display = (this.isActive) ? "none" : "inline";
		
		this.ShowSelectedDiv();
		
		return true;
	}


	this.ChangeStatus = function(status) {
		if (status) {
			this.isModified = true;
			this.inputIsModified.value = 1;
		} else {
			this.isModified = false;
			this.inputIsModified.value = 0;
		}
		return true;
	}

	
	this.ShowSelectedDiv = function() {
		var str = this.MakeSelectedDiv();

		if (0 == str.length) {
			this.divSelectedItems.style.display = "none";
		}
		
		this.divSelectedItems.innerHTML = str;
		
		return true;
	}

	
	this.CancelChanges = function() {
		for (var index in this.elements) {
			if(1 == this.elements[index]){
				var chkbox = document.getElementById("ref_" + index);
				chkbox.checked = false;
			}
		}
		
		for (var index in this.defaultElements) {
			if(1 == this.defaultElements[index]){
				var chkbox = document.getElementById("ref_" + index);
				chkbox.checked = true;
			}
			
			this.elements[index] = this.defaultElements[index];
		}
		
		this.inputField.value = this.ElementsToString();
		this.ChangeStatus(0);
		this.ChangeActive(0);
		return true;
	}

	
	this.AddElement = function(id, checked) {
		var value = checked ? 1 : 0;
		var field = id;
		this.defaultElements[field] = value;
		this.elements[field] = value;
		return true;
	}
	

	this.ModifyElement = function(id, checked) {
		var value = checked ? 1 : 0;
		var field = id;
		this.elements[field] = value;
		
		this.ChangeStatus(1);
		this.inputField.value = this.ElementsToString();
		return true;
	}

	
	this.ElementsToString = function() {
		var str = "";
		for (var index in this.elements) {
			if (1 == this.elements[index]) {
				str += index + ";";
			}
		}
		if (str.length > 0) {
			str = str.substring(0, str.length - 1);
		}
		return str;
	}
	

	this.ApplyChanges = function() {
		this.inputField.value = this.ElementsToString();
		this.ChangeStatus(1);
		this.ChangeActive(0);
		return true;
	}
	

	this.MakeSelectedDiv = function() {
		var str = "<ul class='linkedDocs'>";
		
		for (var index in this.elements) {
			if(1 == this.elements[index]){
				var label = document.getElementById("ref_" + index + "_label");
				var link = document.getElementById("ref_" + index + "_href");
				str += "<li><a href=\"" + link.href + "\">" + label.innerHTML + "</a></li>";
			}
		}
		str += "</ul>";
		
		return str;
	}
}