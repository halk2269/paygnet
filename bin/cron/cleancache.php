<?php

/**
 * Очищает XSLT-кэш. Удаляет все файлы старше одной недели из директории XSLT-кэша.
 * Очистка один раз в неделю.
 * @author IDM
 */

class CleanCacheCronClass extends CronBaseClass {

	function MakeChanges() {
 		$this->report = "Starting cleaning cache...\n";
 		
 		if ($dh = opendir(CMSPATH_CACHE_XSLT)) {
 			// время, файлы старше которого должны быть удалены
 			// на 2 нужно умножать потому, что strtotime($t) = time() + $t
			$birthTime = 2 * time() - strtotime("00000007000000");
 			while (($file = readdir($dh)) !== false) {
 				if ($file != "." and $file != ".." and is_file(CMSPATH_CACHE_XSLT . $file)) {
 					$fileName = CMSPATH_CACHE_XSLT . $file;
 					$st = stat($fileName);
 					// проверяем файлы на "старость"
 					if ($st["mtime"] < $birthTime) {
 						$ret = @unlink($fileName);
 						if (!$ret) $this->report .= "Can`t delete cache file: {$file}\n";
 					}
 				}
 			}
 			closedir($dh);
 			$this->report .= "Cache clean complete.\n";
 		} else {
 			// если не получилось "открыть" папку
 			$this->report .= "Can`t open folder: " . CMSPATH_CACHE_XSLT . "\nCache clean is not completed!";
 		}
	}

}

?>