<?php
require_once(CMSPATH_LIB . "dt/base.ft.php");
class FileFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "file";
		parent::__construct($xml, $dt);
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
					$withoutHash = $this->conf->Param(($this->dtconf->dtf[$dtName][$name]["type"] == "file") ? "FileDownloadWithoutHash" : "ImageDownloadWithoutHash");
					/*
					// Without URI rewrite - it works, but it's not so nice :)
					$fURL = $this->conf->Param("Prefix") . "getf/?type=" . $type . "&id=" . $atValue;
					if (!$withoutHash) $fURL .= ("&hash=" . md5($atValue . (isset($row["join_{$name}_filename"]) ? $row["join_{$name}_filename"] : "")));
					*/
					// With URI rewrite
					$fileURL = $this->conf->Param("Prefix") . "get" . $this->dtconf->dtf[$dtName][$name]["type"] . "/";
					if (!$withoutHash) {
						$fileURL .= (md5($atValue . (isset($row["join_{$name}_filename"]) ? $row["join_{$name}_filename"] : "")) . "/");
					}
					$fileURL .= ($atValue . "." . $ext);
					// Setting attr.
					$field->setAttribute("URL", $fileURL);
				} else {
					$field->setAttribute("URL", $this->conf->Param("Prefix") . CMSPATH_UPLOAD . (isset($row["join_{$name}_filename"]) ? $row["join_{$name}_filename"] : ""));
				}
			}
		}
		return true;
	}


	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		/**
		 * нет параметра
		 * "murl"  - Modified URL
		 */
		if (isset($this->dtconf->dtf[$dtName][$name]["exts"])) $field->setAttribute("extensions", $this->dtconf->dtf[$dtName][$name]["exts"]);
		if (isset($this->dtconf->dtf[$dtName][$name]["maxs"])) {
			$field->setAttribute("maxSize", FormatFileSize($this->dtconf->dtf[$dtName][$name]["maxs"]));
			$field->setAttribute("rawMaxSize", $this->dtconf->dtf[$dtName][$name]["maxs"]);
		}
		return true;
	}
}
?>