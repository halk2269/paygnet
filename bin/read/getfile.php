<?php

/**
 * Скачивание файлов из папки uploads
 * @author IDM
 */

class GetFileReadClass extends ReadModuleBaseClass {

	public function CreateXML() {
		// Is script-download allowed?
		if (!$this->conf->Param("SmartFileDownload")) {
			return false;
		}

		$id = $this->_GetSimpleParam("id");
		if (!IsGoodNum($id)) {
			return false;
		}

		// Download type (image or file)
		$type = $this->_GetSimpleParam("type");
		$table = "";
		$withoutHash = false;
		$contentDesp = "inline";
		switch ($type) {
			case "image" : {
				$table = "sys_dt_images";
				$withoutHash = $this->conf->Param("ImageDownloadWithoutHash");
				$contentDesp = "inline";
				break;
			}
			case "file" : {
				$table = "sys_dt_files";
				$withoutHash = $this->conf->Param("FileDownloadWithoutHash");
				$contentDesp = "attachment";
				break;
			}
			default : {
				return false;
			}
		}

		// Getting from DB
		$file = $this->db->GetRow("SELECT id, name, ext, size, mimetype, filename, chtime FROM {$table} WHERE id = {$id}");
		if (!$file) {
			return false;
		}

		// Hash check
		if (!$withoutHash) {
			$hash = $this->_GetSimpleParam("hash");
			if (!$hash) {
				return false;
			}
			$md5 = md5($id . $file->filename);
			if ($md5 != $hash) {
				return false;
			}
		}

		// File out
		if (file_exists(CMSPATH_UPLOAD . $file->filename)) {
			if ($this->conf->Param("CalculateDownloadCount")) {
				$this->db->SQL("UPDATE {$table} SET download_count = download_count + 1 WHERE id = {$id}");
			}
			$this->headers[] = "Content-Type: {$file->mimetype}";
			$this->headers[] = "Content-Length: {$file->size}";
			// MSIE check - it doesn't understand UTF names
			$browser = $_SERVER["HTTP_USER_AGENT"];
			if (preg_match("/MSIE/", $browser) and !preg_match("/Opera/i", $browser)) $file->name = iconv("UTF-8", "WINDOWS-1251", $file->name);
			$this->headers[] = $file->name ? "Content-Disposition: {$contentDesp}; filename=\"{$file->name}\"" : "Content-Disposition: {$contentDesp}";
			$this->headers[] = "Cache-Control: must-revalidate";

			echo file_get_contents(CMSPATH_UPLOAD . $file->filename);
		} else {
			// File doesn't exist
			$this->headers[] = "Content-Type: text/plain; charset=UTF-8";
			$this->log->writeWarning("GetFile", ($type == "file" ? "File #" : "Image #") . $id . " (filename = " . $file->filename . ") was not found on the server" );
			echo "Sorry, requested file was not found on the server / Извините, запрошенный файл не найден на сервере";
		}

		return true;
	}

}

?>