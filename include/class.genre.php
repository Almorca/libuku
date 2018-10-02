<?php
/**
 * A class representing a author.
 *
 * PHP version 5
 *
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
 *
 * @author     Alejandro Moreno Calvo <almorca@almorca.es>
 * @copyright  &copy; 2009 Alejandro Moreno Calvo
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @version    Release: @package_version@
 */

class Genre {
	protected $_title = NULL;
	protected $_url = NULL;
	protected $_father = NULL;

	// sql fields to build an object from mysql
    const SQL = " genre_title as _title, genre_url as _url, genre_father as _father
	FROM Genres ";

	/**
	 * Constructor
	 *
	 * @param	id	Genre title or array with initial values
	 */
	public function __construct($param) {
		if (is_string($param)) {
			$this->_url = $param;
			$this->read();
		} else if (is_array($param)) {
			$this->setVariables($param);
		}
	}

	/** Inicialice the object variables
		@param	$param Array with values.
	*/
	public function setVariables($param) {
		foreach($this as $var => $value) {
			if ( isset($param[$var]) ) {
				$this->$var = $param[$var];
			}
		}
	}

	/**
	 * Read a genre from database
	 *
	 * @return	true if the genre is read and false it isn't.
	 */
	public function read()
	{
		global $db;

		if (! $this->_url) {
			return FALSE;
		}

		if(($book = $db->get_row("SELECT".Genre::SQL."WHERE genre_url = '".$this->_url."'"))) {
			$this->setVariables($book);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Get URL
	 *
	 * @return	String with URL
	 */
	public function getURL()
	{
		return $this->_url;
	}

	/**
	 * Get Title
	 *
	 * @return	String with title
	 */
	public function getTitle()
	{
		return $this->_title;
	}

	public function getFather()
	{
		return $this->_father;
	}

	/**
	 * Get the id of a author from his url
	 *
	 * @param	url	Url of the author
	 * @return	integer or NULL if there is an error or there is not any book
	 */
	public static function getGenreTitle($url)
	{
		global $db;

		if (!is_string($url)) {
			return NULL;
		}

		$where = "genre_url = '". $db->escape($url) ."'";

		if(($genre = $db->get_row("SELECT".Genre::SQL."WHERE $where LIMIT 1"))) {
			return $genre['_title'];
		}

		return NULL;
	}

	/**
	 * Get the parents genres (genres without father)
	 *
	 * @return	Array of string or NULL if there is an error or there is not any genre
	 */
	public static function getParentsGenres()
	{
		global $db;

		if(($genres = $db->get_results("SELECT".Genre::SQL."WHERE genre_father IS NULL"))) {
			return $genres;
		} else {
			return NULL;
		}
	}

	/**
	 * Return ancestors of a genre
	 *
	 * @return	An array of genres or NULL if genre doesn't have ancestors.
	 */
	public function getAncestors()
	{
		$genre = NULL;
		$father = $this->_father;
		$ancestors = array();

		while ( !is_null($father) ) {
			$ancestors[] = $father;
			$genre = new Genre($father);
			$father = $genre->getFather();
		}

		return $ancestors;
	}

	/**
	 * Return children of a genre
	 *
	 * @return	An array of genres or NULL if genre doesn't have children.
	 */
	public function getChildren()
	{
		global $db;

		if(($children = $db->get_results("SELECT".Genre::SQL."WHERE genre_father = '$this->_url'"))) {
			return $children;
		} else {
			return NULL;
		}
	}
}