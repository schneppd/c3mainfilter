<?php
/*
 * Provides all functionalities for the module's IO operations
 * 
 * @author Schnepp David <david.schnepp@schneppd.com>
 * @since 2016/09/13
 */

namespace NsC3MainFilterFramework;

class ModuleIO {
	
	/*
	 * Checks if given directory exists
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $dirPath absolute path to directory
	 * @return boolean does the directory exists
	 */
	public static function existDirectory($dirPath) {
		return file_exists($dirPath);
	}
	
	/*
	 * Checks if given file exists
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $filePath absolute path to file
	 * @return boolean does the file exists
	 */
	public static function existFile($filePath) {
		return file_exists($filePath);
	}
	
	/*
	 * Create given directory and apply correct rights
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $dirPath absolute path to directory
	 * @return boolean if the directory creation succeeded
	 */
	public static function createDirectory($dirPath) {
		return mkdir($dirPath, 0755, false);
	}
	
	/*
	 * Empty given directory and then deletes it
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $dirPath absolute path to directory
	 */
	public static function emptyAndDeleteDirectory(&$dirPath) {
		static::emptyDirectory($dirPath);
		static::deleteDirectory($dirPath);
	}
	
	/*
	 * Empty given directory od it's content
	 * 
	 * @author Schnepp David
	 * @since v0.2 2016/09/18
	 * @param string $dirPath absolute path to directory
	 */
	public static function emptyDirectory(&$dirPath) {
		if (static::existDirectory($dirPath)) {
			//empty the dir of its content
			$files = scandir($dirPath);
			foreach ($files as $file) {
				if (filetype($dirPath . "/" . $file) == "file") {
					$filePath = $dirPath . "/" . $file;
					static::deleteFile($filePath);
				}
			}
		}
	}
	
	/*
	 * Delete given directory
	 * 
	 * @author Schnepp David
	 * @since v0.2 2016/09/18
	 * @param string $dirPath absolute path to the directory
	 */
	public static function deleteDirectory(&$dirPath) {
		rmdir($dirPath);
	}
	
	/*
	 * Delete given file
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $filePath absolute path to the file
	 */
	public static function deleteFile($filePath) {
		unlink($filePath);
	}
	
	/*
	 * Only delete the file if exists
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $filePath absolute path to the file
	 */
	public static function safeDeleteFile($filePath) {
		if(static::existFile($filePath)) {
			static::deleteFile($filePath);
		}
	}
	
	/*
	 * Read file content to memory
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $filePath absolute path to the file
	 * @param string the file content
	 */
	public static function getFileContentToString($filePath) {
		$res = file_get_contents($filePath);
		return $res;
	}
	
	/*
	 * Write given string to given file (override if exists)
	 * 
	 * @author Schnepp David
	 * @since 2016/09/13
	 * @param string $str the content to write to the file
	 * @param string $filePath absolute path to the file
	 */
	public static function writeStringToFile($str, $filePath) {
		$file = fopen($filePath, "w") or die("Unable to open cache!");
		fwrite($file, $str);
		fclose($file);
	}

	/*
	 * Convert given array to json and save in given file
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @param string $dataArray the content to write to the fileconvert to json
	 * @param string $filePath absolute path to the file
	 */
	public static function writeArrayToJsonFile($dataArray, $filePath) {
		$file = fopen($filePath, "w") or die("Unable to open cache!");
		$json = json_encode($dataArray);
		fwrite($file, $json);
		fclose($file);
	}
	
	/*
	 * Read content of given json file to array
	 * 
	 * @author Schnepp David
	 * @since 2016/09/18
	 * @param string $filePath absolute path to the file
	 * @return mixed[] the array
	 */
	public static function getJsonFileContentToArray($filePath) {
		$txt = static::getFileContentToString($filePath);
		$res = json_decode($txt, true);
		return $res;
	}
}