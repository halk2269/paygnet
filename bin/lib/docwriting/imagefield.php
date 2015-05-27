<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
require_once(CMSPATH_LIB . "graph/simplegraph.php");
/**
 * Класс проверки поля с изображением
 */
class ImageField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return ImageField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	protected function _CheckConstraints() {
		if ($this->_IsFileForDelete()) {
			$this->documentValidator->SetImage($this->fieldName, "Delete");
			return;
		}

		if ($this->_IsThumbnail()) {
			/**
			 * _NeedToDeleteThumbnail() является частично дублирующий (в том числе
			 * по смыслу) для _IsFileForDelete()
			 */
			if ($this->_NeedToDeleteThumbnail()) {
				$this->documentValidator->SetImage($this->fieldName, "Delete");
				return;
			}

			if ($this->_IsNoThumbNailFileAndMainFile()) {
				return;
			}

			$dtf = $this->documentValidator->GetDocTypeField($this->conf["isth"]);

			$img = new ImageField($this->documentValidator, $this->query, $dtf, $this->conf["isth"]);
			if ($this->error = $img->GetError()) {
				return;
			}
			
			$this->_CreateThumbNail();
			if ($this->error) {
				return;
			}
		} else {
			if ($this->_IsFileNotPassed()) {
				return;	
			}
			
			if ($this->_IsBadExtension()) {
				$this->error = "BadFileExt";
				return;
			}

			if ($this->_IsTooBig()) {
				$this->error = "TooLargeFile";
				return;
			}

			if ($this->_IsBadImage()) {
				$this->error = "ThisIsNotImage";
				return;
			}

			if ($this->_IsBadImageSize()) {
				$this->error = "BadImageSizes";
				return;
			}
		}

		if ($this->_IsSaveNeeded()) {
			$this->documentValidator->SetImage($this->fieldName, "Insert");
		}
	}

	protected function _IsBlank() {
		$importance = (isset($this->conf["impt"]) and $this->conf["impt"]);
		
		return (
			$importance
			and "Create" == $this->documentValidator->GetAct()
			and (!isset($_FILES[$this->fieldName]) or !$_FILES[$this->fieldName]["name"])
		);
	}

	protected function _IsFileForDelete() {
		$importance = (isset($this->conf["impt"]) and $this->conf["impt"]);

		return (
			(!isset($_FILES[$this->fieldName]) or !$_FILES[$this->fieldName]["name"])
			and !$importance
			and "Edit" == $this->documentValidator->GetAct()
			and $this->query->GetParam($this->fieldName . "_delete")
		);
	}

	function _IsBadExtension() {
		$fName = $_FILES[$this->fieldName]["name"];

		$fExt = (preg_match("/\.([0-9a-zA-Z$#()_]{1,10})$/", $fName, $matches)) ? $matches[1] : "";
		if (!$fExt) return true;

		$fExt = strtolower($fExt);

		// Смотрим, разрешённое ли расширение у файла. Если нет, выдаём ошибку пользователю
		$fExtQuoted = preg_quote($fExt);
		return (!$fExt or !preg_match("/{$fExtQuoted}/", $this->conf["exts"]));
	}

	function _IsTooBig() {
		return (($_FILES[$this->fieldName]["size"] > $this->conf["maxs"]));
	}

	function _IsBadImage() {
		return !IsImage($_FILES[$this->fieldName]["name"], $_FILES[$this->fieldName]["tmp_name"], $w, $h);
	}

	function _IsBadImageSize() {
		$min_x = isset($this->conf["minw"]) ? $this->conf["minw"] : 0;
		$min_y = isset($this->conf["minh"]) ? $this->conf["minh"] : 0;
		$max_x = isset($this->conf["maxw"]) ? $this->conf["maxw"] : 0;
		$max_y = isset($this->conf["maxh"]) ? $this->conf["maxh"] : 0;
		$rv = CheckImageSize($_FILES[$this->fieldName]["tmp_name"], $min_x, $min_y, $max_x, $max_y, $_FILES[$this->fieldName]["name"]);

		return  (!$rv or -1 == $rv);
	}

	/**
	 * Сохранять ли основное изображение в случае генерации превью?
	 *
	 * @return bool
	 */
	function _IsSaveNeeded() {
		return (!isset($this->conf["save"]) or isset($this->conf["save"]) and $this->conf["save"]);
	}


	/**
	 * Если поле является "превью" и "важным" и для "превью" 
	 * не пришёл файл (это верно, для превью файл не должен приходить), 
	 * а также не пришел файл для основного изображения, то 
	 * мы не выдаем никакой ошибки, предоставляя создание ошибки
	 * объекту, проверяющему основное изображение.
	 * 
	 * @return bool
	 */
	function _IsNoThumbNailFileAndMainFile() {
		if (isset($_FILES[$this->fieldName]) and $_FILES[$this->fieldName]["name"]) return false;

		if (!isset($this->conf["isth"]) or !$this->conf["isth"]) return false;

		if (isset($this->conf["impt"]) and $this->conf["impt"]) return false;

		if (!$this->documentValidator->GetDocTypeField($this->conf["isth"])) return false;

		if (isset($_FILES[$this->conf["isth"]]) and $_FILES[$this->conf["isth"]]["name"]) return false;

		return true;
	}

	/**
	 * Enter description here...
	 *
	 * @return bool
	 */
	function _NeedToDeleteThumbnail() {

		if (isset($_FILES[$this->fieldName]) and $_FILES[$this->fieldName]["name"]) return false;

		if (!isset($this->conf["isth"]) or !$this->conf["isth"]) return false;

		if (isset($this->conf["impt"]) and $this->conf["impt"]) return false;

		if ("Edit" != $this->documentValidator->GetAct()) return false;

		if (!$this->query->GetParam($this->conf["isth"] . "_delete")) return false;

		return true;
	}

	private function _IsThumbnail() {
		return (isset($this->conf["isth"]) and $this->conf["isth"]);
	}

	function _CreateThumbNail() {
		$uploaddir = (!ini_get("upload_tmp_dir")) ? CMSPATH_MAIN . "tmp" . DIRECTORY_SEPARATOR : ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR;
		$name = uniqid("preview") . $_FILES[$this->conf["isth"]]["name"];
		$outPath = $uploaddir . $name;
		$file = $_FILES[$this->conf["isth"]]["tmp_name"];

		$conf = GlobalConfClass::GetInstance();
		
		if ($conf->Param("ImageMagickOn")) {
			$int = $this->conf["imil"] ? "-interlace Plane" : "";
			$imPath = $conf->Param("ImageMagickPath");
			$command = "\"{$imPath}\convert\" $file -thumbnail {$this->conf["twid"]}x{$this->conf["thei"]} -format jpg $int -quality {$this->conf["imqu"]} $outPath";
			$res = SimpleGraphClass::exe($command);
			$result = each($res);
			if ($result['key'] != 0) {
				$this->error = "ImageMagickFailure";
				$logger = LogClass::GetInstance();
				$logger->writeError("DocWritingWriteClass", $result['value']);
				return;
			}
		} else {
			SimpleGraphClass::SaveThumbnail($uploaddir, $file, $name, $this->conf["twid"], $this->conf["thei"]);
		}

		// создаем превьюшку и пихаем её в массив $_FILES["tmp_name"]
		$_FILES[$this->fieldName]["name"] = $name;
		$_FILES[$this->fieldName]["size"] = filesize($outPath);
		$_FILES[$this->fieldName]["tmp_name"] = $outPath;

		$this->documentValidator->SetFileToDelete($outPath);
	}

	function _IsFileNotPassed() {
		return (!isset($_FILES[$this->fieldName]) or !$_FILES[$this->fieldName]["name"]);
	}

}
?>