<?php
/**
 * A class representing a category.
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
 * @author		Alejandro Moreno Calvo <almorca@almorca.es>
 * @copyright	&copy; 2009 Alejandro Moreno Calvo
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @version		Release: @package_version@
 * @since		0.1
 */
class Category
{
	/**
	 * Category title
	 *
	 * @var	string
	 * @access	private
	 */
	private $_title = null;

	/**
	 * Category father
	 *
	 * @var	string
	 * @access	protected
	 */
	protected $_father = null;

	/**
	 * Category url
	 *
	 * @var	string
	 * @access	protected
	 */
	protected $_url = null;

	/**
	 * Show if a category is new or not.
	 *
	 * true show that a category exists in the data base and false show that the category is new.
	 *
	 * @var	bool
	 * @access	private
	 */
	private $_isStore = false;

	// sql fields to build an object from mysql
    const SQL = " genre_title as _title, genre_father as _father, genre_url as _url FROM Genres ";


	/**
	 * Constructor
	 *
	 * @param	string	$title	Category title
	 * @since 0.1
	 */
	public function __construct($param)
	{
		if (is_string($param)) {
			$this->_url = $param;
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
	 * Get category title
	 *
	 * @return	String with category title or NULL if there aren't category
	 * @since 0.1
	 */
	public function getTitle()
	{
		return $this->_title;
	}

	/**
	 * Get category URL
	 *
	 * @return	String with category URL or NULL if there aren't category
	 * @since 0.1
	 */
	public function getURL()
	{
		return $this->_url;
	}

	/**
	 * Read a father of category
	 *
	 * @return	String with title of father category or NULL if category doesn't have father.
	 * @since	0.1
	 */
	public function getFather()
	{
		return $this->_father;
	}

	/**
	 * Store a father of category
	 *
	 * @param	String with title of father category or NULL if category doesn't have father.
	 * @since	0.1
	 */
	public function setFather($father)
	{
		$this->_father = $father;
	}

	/**
	 * Return children of the category
	 *
	 * @return	Array with children of the category or NULL if the category doesn't have children.
	 * @since	0.1
	 */
	public function getChildren()
	{
		global $db;

		if (!is_string($this->_title))
			return false;

		return $db->get_results("SELECT genre_title FROM Genres WHERE genre_father = '$this->_title'");
	}

	/**
	 * Return ancestors of the category
	 *
	 * @return	Array with ancestors of the category or NULL if the category doesn't have ancestors.
	 * @since	0.1
	 */
	public function getAncestors()
	{
		global $db;

		if (!is_string($this->_url))
			return NULL;

		$father = $this->_father;
		$ancestors = array();
		while ( !is_null($father) ) {
			$ancestors[] = $father;
			$father = $db->get_row("SELECT * FROM Genres WHERE genre_father = '$father'");
		}

		return $ancestors;
	}

	/**
	 * Read a category
	 *
	 * @return	true if the category is read from database and false it isn't.
	 * @since	0.1
	 */
	public function read()
	{
		global $db;

		$where = "genre_url = '". $db->escape($this->_url) ."'";

		if(($category = $db->get_row("SELECT".Category::SQL."WHERE $where LIMIT 1"))) {
			$this->setVariables($category);

			$this->_isStore = true;
			return true;
		} else {
			$this->_isStore = false;
			return false;
		}
	}

	/**
	 * Store a category
	 *
	 * @return	true if the category is store and false it is not.
	 * @since	0.1
	 */
	public function store()
	{
		global $db;

		$category_title = $this->_title;
		$category_father = $this->_father;

		if (is_null($category_title)) { // check category_title
			return false;
		} else {
			if ($this->_isStore === false) { // Insert a new category
				if (is_null($category_father)) {
					if (!$db->query("INSERT INTO Genres (genre_title, genre_father) VALUES ('$category_title', NULL)")) {
						return false;
					}
				} else {
					if (!$db->query("INSERT INTO Genres (genre_title, genre_father) VALUES ('$category_title', '$category_father')")) {
						return false;
					}
				}
			} else { // Update info
				if (is_null($category_father)) {
					if (!$db->query("UPDATE Genres SET genre_father = NULL WHERE genre_title = '$category_title'")) {
						return false;
					}
				} else {
					if (!$db->query("UPDATE Genres SET genre_father = '$category_father' WHERE genre_title = '$category_title'")) {
						return false;
					}
				}
			}

			$this->_isStore = true;
			return true;
		}
	}

	/**
	 * Get the parents categories (categories without father)
	 *
	 * @return	Array of string or NULL if there is an error or there is not any category
	 * @since	0.1
	 */
	public static function getParentsCategories()
	{
		global $db;

		if(($category = $db->get_results("SELECT category_title FROM Genres WHERE category_father IS NULL"))) {
			return $category;
		} else {
			return NULL;
		}
	}
}