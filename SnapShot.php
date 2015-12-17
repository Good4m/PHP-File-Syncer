<?php
/*
* Copyright (C) 2014 Jeffrey Schweigler
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*
* This program comes with ABSOLUTELY NO WARRANTY.
* This is free software, and you are welcome to redistribute it
* under certain conditions.
*/

/**
 * This class encapsulates the $snapShot object and provides
 * operations for loading, saving, building, and returning it.
 * @author Jeffrey Schweigler, Satecha.com, 2014
 */
class SnapShot
{		
	// Contains an array of file paths and their lastmodified timestamps.
	private $snapShot;
	
	
	function __construct() {
		$snapShot = null;
	}
	
	/**
	 * Returns snapshot array
	 * @return array | null
	 */
	public function files() { return $this->snapShot; }
	
	/**
	 * Save snapshot
	 * @param string $filename
	 */
	public function save($filename) {
		if($this->snapShot != null)
			file_put_contents($filename, serialize($this->snapShot));
	}
	
	/**
	 * Load snapshot
	 * @param string $filename
	 * @return boolean
	 */
	public function load($filename) {
		if(file_exists($filename)) {
			$this->snapShot = unserialize(file_get_contents($filename));
			return true;
		}
		$this->snapShot = null;
		return false;
	}
	
	/**
	 * Builds an array of all files in a directory accompanied
	 * by their lastmodified timestamp. Stores it in the class
	 * variable $snapShot.
	 * 
	 * Array Structure:
	 *     [i] = 'file' => filename, 'modified' => timestamp
	 * 
	 * @param string $path
	 */
	function build($path) {
		$this->snapShot = array();
		
		$localIterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($path),
				RecursiveIteratorIterator::CHILD_FIRST
		);
	
		foreach ($localIterator as $splFileInfo) {
			if(!$splFileInfo->isDir()) {
				$filepathAndFilename = $splFileInfo->getPath() . '/' . $splFileInfo->getFilename();
				array_push($this->snapShot, array('file' => $filepathAndFilename, 'modified' => getTimeStamp_lastModified($filepathAndFilename)));
			}
		}
	}
}
	
?>
