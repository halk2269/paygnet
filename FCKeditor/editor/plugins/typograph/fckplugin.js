var FCKTypograph = function(field, objectName, url) {
	var _objectName = objectName;

	var _url = url;

	var _field = field;

	var _xmlSocket = null;
	var _abortTimer;
	
	var _modulePair = 'jsmodule=TypoGraph';

	this.sendRequest = function() {
		if (!_field) {
			return;
		}

		if (window.ActiveXObject) {
			_xmlSocket = new ActiveXObject ("Microsoft.XMLHTTP");
		} else if(window.XMLHttpRequest) {
			_xmlSocket = new XMLHttpRequest();
		} else {
			alert("Операция завершилась неудачно. Попробуйте позже.");
		}

		_xmlSocket.onreadystatechange = this._callback;
		try {
			_xmlSocket.open("POST", url, true);
			_xmlSocket.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			_xmlSocket.send(
				_modulePair + "&text=" + encodeURIComponent(_field.body.innerHTML)
			);
			_abortTimer = window.setTimeout(objectName + ".abort();", 10000);
		} catch (e) {
			alert("Операция завершилась неудачно. Попробуйте позже.");
		}

	}

	this.abort = function() {
		if(!_field) {
			return;
		}
		_xmlSocket.abort();
	}



	this._callback = function() {
		if (_xmlSocket.readyState != 4) return;
		window.clearTimeout(_abortTimer);
		try {
			if (200 == _xmlSocket.status && _xmlSocket.responseText) {
				_field.body.innerHTML = processResponse(_xmlSocket.responseText);
				_xmlSocket = null;
			}
		} catch (e) {
			alert("Операция завершилась неудачно. Попробуйте позже.");
		}
	}


}

function processResponse(responseText) {
	try {
		eval(responseText);
		text = decodeURIComponent(text);
		return text;
	} catch (e) {
		throw e;
	}
}

var request = new FCKTypograph(FCK.EditorDocument, "request", unescape(FCKURLParams['Prefix']));

var typograph = function() {
	this.Execute = function() {
		request.sendRequest();
	}

	this.GetState = function() {
		return FCK_TRISTATE_OFF;
	}
}


FCKCommands.RegisterCommand('typograph', new typograph());

var oTypoGraphItem = new FCKToolbarButton('typograph', 'Оттипографировать текст');
oTypoGraphItem.IconPath = FCKConfig.PluginsPath + 'typograph/typo.gif';

FCKToolbarItems.RegisterItem('typograph', oTypoGraphItem);