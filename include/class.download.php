<?php
/*
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Signals that an attempt to open the file denoted by a specified pathname has failed.
 *
 * @author Alejandro Moreno Calvo <almorca@almorca.es>
 * @copyright &copy; 2009 Alejandro Moreno Calvo
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @version	@package_version@
 * @since 0.1
*/
class FileNotFoundException extends Exception
{
	/**
	 * Constructs a <code>FileNotFoundException</code> with the specified detail message.
	 *
	 * @param s the detail message.
	 */
	public function __construct($s) {
		parent::__construct($s);
	}
}


/**
 * Download any file from a server.
 *
 * Class has simple interface to download any file from a server without displaying the location of the file.
 *
 * @author Alejandro Moreno Calvo <almorca@almorca.es>
 * @copyright &copy; 2009 Alejandro Moreno Calvo
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @version @package_version@
 * @since 0.1
 */
class Download
{
	/** Size of buffers in which the file will be sent. */
	const BUFFER_SIZE = 8192; // 8*1024

	/**
	 * List of all mimetypes
	 *
	 * @access protected
	 * @var array
	 */
	protected $_mediatypes;

	/**
	 * File name
	 *
	 * @access private
	 * @var string
	 */
	private $_filename;

	/**
	 * File path whit the file name. E.g. /etc/file
	 *
	 * @access private
	 * @var string
	 */
	private $_filepath;

	/**
	 * Mimetype
	 *
	 *
	 * @access private
	 * @var string
	 */
	private $_mimetype;


	/**
	 * Constructs a <code>Download</code> with the specified file.
	 *
	 * @param name File name
	 * @param path File path
	 * @since 0.1
	 */
	public function __construct($name, $path = '/')
	{
		$this->_mediatypes = array (
			'epub' => 'application/epub+zip',
			'fb2'  => 'text/xml',
			'pdf'  => 'application/pdf',
			'rtf'  => 'application/rtf',
			'txt'  => 'text/plain');
		$this->_filename = $name;
		$this->findFilepath($path, $name);
		$this->findMimetype($name);
	}

	/**
	 * Returns the file path
	 *
	 * @return string
	 */
	public function getFilepath() {
		return $this->_filepath;
	}

	/**
	 * Returns the mime type
	 *
	 * @return string
		*/
	public function getMimetype() {
		return $this->_mimetype;
	}

	/**
	 * This method determines the file path and if the file path is good.
	 *
	 * @param path folder where you keep the file for download
	 * @return None
	 * @exception FileNotFoundException if the file does not exist.
	 * @exception Exception if the file name starts with '.'
	 * @since	0.1
	 */
	protected function findFilepath($path)
	{
		if ( $this->_filename[0] == '.' ) {
			throw new Exception("The file name can not start with '.' Current file name: " . $name);
		}

		$filepath = $path . $this->_filename;

		if ( file_exists($filepath) === true && is_file($filepath) === true && is_readable($filepath) === true ) {
			$this->_filepath = $filepath;
		} else {
			throw new FileNotFoundException("The system can not find the file specified: " . $filepath);
		}
	}

	/**
	 * Get the mime type
	 *
	 * @return String with mime type
	 * @exception FileNotFoundException if the file does not exist.
	 * @exception Exception if the file name starts with '.'
	 * @since	0.1
	 */
	protected function findMimetype()
	{
		$pathInfo = pathinfo($this->_filepath);
		$extension = $pathInfo['extension'];
		if ( array_key_exists($extension, $this->_mediatypes) ) {
			/* NOTE: This method have a security risk because the file may have been misslabled intentionally.
			 * E.g. .exe rename to .jpg
			 */
			$this->_mimetype = $this->_mediatypes[$extension];
		} else { // mime type is not set, get from server settings
			$mediatype = '';

			if ( class_exists('finfo', false) ) { // PHP >= 5.3.0 or PECL fileinfo >= 0.1.0
				$constant = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
				$fileInfo = finfo_open($constant);
				$mediatype = finfo_file($fileInfo, $this->_filepath);
				finfo_close($fileInfo);
			} else if ( function_exists("mime_content_type") ) {
				/* NOTE: this function is available since PHP 4.3.0, but only if
				 * PHP was compiled with --with-mime-magic or, before 4.3.2, with --enable-mime-magic.
				 *
				 * On Windows, you must set mime_magic.magicfile in php.ini to point to the mime.magic
				 * file bundeled with PHP; sometimes, this may even be needed under linux/unix.
				 *
				 * Also note that this has been DEPRECATED in favor of the fileinfo extension
				 */
				$tempMime = mime_content_type($this->_filepath);
				list($mediatype, $charset) = explode('; ', $tempMime); // get the mime type and delete the charset
			} else if ( strstr($_SERVER[HTTP_USER_AGENT], "Macintosh") ) { // correct output on macs
				$mediatype = trim(exec('file -b --mime ' . escapeshellarg($this->_filepath)));
			} else { // regular unix systems
				$mediatype = trim(exec('file -bi '. escapeshellarg($this->_filepath)));
			}

			if ($mediatype == '') { // mediatype is unknow
				$mediatype = "application/force-download";
			}

			$this->_mimetype = $mediatype;
		}
	}

	/**
	 * Send the file via http
	 *
	 * @param $downloadName Name of the file the user will see.
	 * @return None
	 * @exception Exception if can not open the file.
	 */
	public function sendFile($downloadName = NULL)
	{
		/* Make sure program execution doesn't time out.
		 * Set maximum script execution time in seconds (0 means no limit)
		  * Disable for security reasons. TODO: See why?
		 */
		//set_time_limit(0);

		// file size in bytes
		$fsize = filesize($this->_filepath);

		// set headers
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime($this->_filepath)) . " GMT");
		header("Content-Description: File Transfer"); // Will help force a download for the user.
		if ( is_null($downloadName) ) { // change the file name
			header("Content-Disposition: attachment; filename=\"$this->_filename\"");
		} else {
			header("Content-Disposition: attachment; filename=\"$downloadName\"");
		}
		header("Content-Length: $fsize");
		header("Content-Type: $this->_mimetype");

		// download
		$file = @fopen($this->_filepath,"rb");
		if ($file) {
			while( !feof($file) && connection_status() == CONNECTION_NORMAL ) {
				echo fread($file, self::BUFFER_SIZE);
				flush();
			}
			@fclose($file);
		} else {
			throw new Exception("Can not open file $this->_filepath");
		}
	}
}
