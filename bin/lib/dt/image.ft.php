<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class ImageFTClass extends BaseFTClass {


	public function __construct($xml, $dt) {
		$this->type = "image";
		parent::__construct($xml, $dt);
	}

	function MakeFieldForDocument($row, $name, $params) {
		$dtName = $params["dtName"];
		
		$field = $this->CreateField($name, $dtName);
		$this->CreateContent($field, $row, $name, $params);
		
		return $field;
	}

	function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		$dtName = $params["dtName"];

		$field->setAttribute("file_id", $atValue);

		if ($atValue != 0) {
			$ext = isset($row["join_{$name}_ext"]) ? $row["join_{$name}_ext"] : "";
			$field->setAttribute("title", isset($row["join_{$name}_name"]) ? $row["join_{$name}_name"] : "");
			$field->setAttribute("ext", $ext);
			$field->setAttribute("size", FormatFileSize(isset($row["join_{$name}_size"]) ? $row["join_{$name}_size"] : ""));
			$field->setAttribute("rawSize", isset($row["join_{$name}_size"]) ? $row["join_{$name}_size"] : "");
			$field->setAttribute("mimeType", isset($row["join_{$name}_mimetype"]) ? $row["join_{$name}_mimetype"] : "");
			if (isset($row["join_{$name}_download_count"])) {
				$field->setAttribute("downloadCount", $row["join_{$name}_download_count"]);
			}
			if (isset($this->dtconf->dtf[$dtName][$name]["murl"])) {
				$path = $this->dtconf->dtf[$dtName][$name]["murl"];
				$path = str_replace("#", $row["id"], $path);
				$path = str_replace("@", $atValue, $path);
				$path = preg_replace("/\*$/", $ext, $path);
				$field->setAttribute("URL", $path);
			} elseif (isset($this->dtconf->dtf[$dtName][$name]["path"])) {
				$field->setAttribute("URL", $this->dtconf->dtf[$dtName][$name]["path"] . (isset($row["join_{$name}_filename"]) ? $row["join_{$name}_filename"] : ""));
			} else {
				if ($this->conf->Param("SmartFileDownload")) {
					$withoutHash = $this->conf->Param(($this->type == "file") ? "FileDownloadWithoutHash" : "ImageDownloadWithoutHash");

					$fURL = $this->conf->Param("Prefix") . "get" . $this->type . "/";
					if (!$withoutHash) $fURL .= (md5($atValue . (isset($row["join_{$name}_filename"]) ? $row["join_{$name}_filename"] : "")) . "/");
					$fURL .= ($atValue . "." . $ext);

					$field->setAttribute("URL", $fURL);
				} else {
					$field->setAttribute("URL", $this->conf->Param("Prefix") . CMSPATH_UPLOAD . (isset($row["join_{$name}_filename"]) ? $row["join_{$name}_filename"] : ""));
				}
			}

			$field->setAttribute("width", isset($row["join_{$name}_width"]) ? $row["join_{$name}_width"] : "");
			$field->setAttribute("height", isset($row["join_{$name}_height"]) ? $row["join_{$name}_height"] : "");
		}
		return true;
	}

	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		/**
		 * нет параметра
		 * "murl"  - Modified URL
		 **/
		$field->setAttribute("minWidth",  isset($this->dtconf->dtf[$dtName][$name]["minw"]) ? $this->dtconf->dtf[$dtName][$name]["minw"] : 0);
		$field->setAttribute("minHeight", isset($this->dtconf->dtf[$dtName][$name]["minh"]) ? $this->dtconf->dtf[$dtName][$name]["minh"] : 0);
		$field->setAttribute("maxWidth",  isset($this->dtconf->dtf[$dtName][$name]["maxw"]) ? $this->dtconf->dtf[$dtName][$name]["maxw"] : 0);
		$field->setAttribute("maxHeight", isset($this->dtconf->dtf[$dtName][$name]["maxh"]) ? $this->dtconf->dtf[$dtName][$name]["maxh"] : 0);

		if (isset($this->dtconf->dtf[$dtName][$name]["exts"])) $field->setAttribute("extensions", $this->dtconf->dtf[$dtName][$name]["exts"]);

		if (isset($this->dtconf->dtf[$dtName][$name]["maxs"])) {
			$field->setAttribute("maxSize", FormatFileSize($this->dtconf->dtf[$dtName][$name]["maxs"]));
			$field->setAttribute("rawMaxSize", $this->dtconf->dtf[$dtName][$name]["maxs"]);
		}
		
		return true;
	}
}
?>