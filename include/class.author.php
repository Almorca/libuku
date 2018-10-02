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
 * @since      0.1
 */

class Author {
	public static $author_types = array("author" => 1, "translator" => 2, 'preface' => 3);

	protected $_id = NULL; /** author id */
	protected $_name = NULL;
	protected $_born_date = NULL;
	protected $_died_date = NULL;
	protected $_url = NULL;
	protected $_biography = NULL;

	// sql fields to build an object from mysql
    const SQL = " author_id as _id, author_url as _url, author_name as _name, UNIX_TIMESTAMP(author_born_date) as _born_date, UNIX_TIMESTAMP(author_died_date) as _died_date, author_biography as _biography
	FROM Authors ";

	/**
	 * Constructor
	 *
	 * @param	id	Author id or array with initial values
	 * @since	0.1
	 */
	public function __construct($param) {
		if (is_int($param)) {
			$this->_id = $param;
			$this->read();
		} else if (is_array($param)) {
			$this->setVariables($param);
		}
	}

	/** Inicialice the object variables
		@param	$param Array with values.
		@since	0.1
	*/
	public function setVariables($param) {
		foreach($this as $var => $value) {
			if ( isset($param[$var]) ) {
				$this->$var = $param[$var];
			}
		}
	}

	/**
	 * Read a author from de data base
	 *
	 * @return	true if the book is read and false it isn't.
	 * @since 0.1
	 */
	public function read()
	{
		global $db;

		if (!is_int($this->_id)) {
			return FALSE;
		}

		if(($book = $db->get_row("SELECT".Author::SQL."WHERE Authors.author_id = $this->_id"))) {
			$this->setVariables($book);
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Get URL
	 *
	 * @return	String with URL
	 * @since 0.1
	 */
	public function getURL()
	{
		return $this->_url;
	}

	/**
	 * Get Name
	 *
	 * @return	String with name
	 * @since 0.1
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Get the id of a author from his url
	 *
	 * @param	url	Url of the author
	 * @return	integer or NULL if there is an error or there is not any book
	 * @since	0.1
	 */
	public static function getAuthorId($url)
	{
		global $db;

		if (!is_string($url)) {
			return NULL;
		}

		$where = "author_url = '". $db->escape($url) ."'";

		if(($author = $db->get_row("SELECT author_id FROM Authors WHERE $where LIMIT 1"))) {
			return (int)$author['author_id'];
		}

		return NULL;
	}

}