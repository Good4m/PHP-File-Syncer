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

include "SnapShot.php";

main();

function main() {
	welcomeAscii();
	
	$snapShot         = new SnapShot();
	
	$localPath        = 'test/test1';
	$remotePath       = 'testRemote';
	$snapShotFileName = 'lastSnapShot.txt';
	
	echo "Loading... ";
	// If snapshot loads continue, otherwise create a snapshot.
	if($snapShot->load($snapShotFileName)) {
		echo "\033[32mdone\033[37m.\n\n";
	} else {
		echo "\033[32mdone\033[37m. \n\nDeploying for the first time.\n";
		$snapShot->build($localPath);
		$snapShot->save($snapShotFileName);
	}
	
	echo "Checking for changes...\n";
	
	foreach($snapShot->files() as $k => $v) {
		// Remove deleted files from remote directory.
		if(!file_exists($v['file']) && file_exists($remotePath . '/' . $v['file'])) {
			echo "Removing " . $v['file'] . " from remote directory...\n";
			unlink($remotePath . '/' . $v['file']);
			continue;
		}
		
		// Copy new files to remote directory
		if(!file_exists($remotePath . '/' . $v['file'])) {
			if(!is_dir($remotePath . '/' . dirname($v['file']))) {
				mkdir($remotePath . '/' . dirname($v['file']), intval(fileperms($v['file'])), true);
			}
			echo "Copying new file " . $v['file'] . " to " . $remotePath . "/" . basename($v['file']) . "\n";
			if (!copy($v['file'], $remotePath . '/' . $v['file'])) {
				echo "Failed to copy new file " . $v['file'] . " to remote location...\n";
			}
			continue;
		}
		
		// Copy modified files to remote directory
		if(getTimeStamp_lastModified($v['file']) > getTimeStamp_lastModified($remotePath . '/' . $v['file'])) {
			echo "Updating " . $localPath . $v['file'] . " to " . $remotePath . '/' . $v['file'] . "\n";
			if (!copy($v['file'], $remotePath . '/' . $v['file'])) {
				echo "Failed to copy file to remote location: " . $v['file'] . "...\n";
			}
		}
	}

	// Update SnapShot
	$snapShot->build($localPath);
	$snapShot->save($snapShotFileName);
	
	echo "\n\033[32mAll files in sync.\033[37m\n\n";
}


/**
 * Retrieves last modified timestamp of file.
 * @param string $pathAndFilename
 * @return string timestamp
 */
function getTimeStamp_lastModified($pathAndFilename) {
	clearstatcache($pathAndFilename);
	$dateUnix = shell_exec('stat --format "%y" "' . $pathAndFilename . '"');
	$date = explode(".", $dateUnix);
	return filemtime($pathAndFilename) . "." . substr($date[1], 0, 8);
}


/**
 * Prints out an array
 */
function debugArr($arr) {
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

/**
 * Echo welcome ascii
 * 
 */
function welcomeAscii() {
	echo "
 _____ _ _        _____                     \r
|   __|_| |___   |   __|_ _ ___ ___ ___ ___ \r
|   __| | | -_|  |__   | | |   |  _| -_|  _|\r
|__|  |_|_|___|  |_____|_  |_|_|___|___|_|  \r
                       |___|  Satecha.com\n\n";
}
?>