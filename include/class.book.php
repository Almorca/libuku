<?php
/**
 * A class representing a book.
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

include_once (lbkinclude.'class.author.php');

class Book {
	private $_id = NULL; /** book id */
	private $_title = NULL; /** book title */
	private $_language = NULL; /** ISO 639-1 code */
	private $_date_submitted = NULL; /** Date of submission of the book */
	private $_rating = NULL;
	private $_votes = NULL;
	private $_description = NULL;
	private $_category = NULL;
	private $_image = 'default.png';
	private $_book_url = NULL;
	private $_authors = NULL;
	private $_collection = NULL;
	private $_publisher = NULL;
	protected $downloads=NULL;

	// sql fields to build an object from mysql
    const SQL = " Books.book_id as _id, book_title as _title, book_language as _language, UNIX_TIMESTAMP(book_date_submitted) as _date_submitted, book_collection as _collection, book_publisher as _publisher, book_downloads as downloads, book_rating as _rating, book_description as _description, book_genre as _category, book_votes as _votes, book_image as _image, book_url as _book_url
	FROM Books ";

	/**
	 * Constructor
	 *
	 * @param	id	Book id or array with initial values
	 * @since	0.1
	 */
	public function __construct($param) {
		if (is_int($param)) {
			$this->_id = (int)$param;
			$this->read();
		} else if (is_array($param)) {
			$this->setVariables($param);
		}
	}

	/**
	 * Add a new download to the counter
	 *
	 * @return	Boolean	Returns TRUE if update the downloads counter is OK, FALSE otherwise.
	 */
	public function addDownload()
	{
		global $db;

		if (!isset($this->_id)) {
			return FALSE;
		}

		if (!$db->query("UPDATE Books SET book_downloads=book_downloads+1 WHERE book_id = $this->_id")) {
			return FALSE;
		}

		return TRUE;
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
	 * Get book category
	 *
	 * @return	String with book category or NULL if there aren't book
	 */
	public function getCategory()
	{
		return $this->_category;
	}

	/**
	 * Get book Id
	 *
	 * @return	Integer with book id or NULL if there aren't book
	 * @since 0.1
	 */
	public function getId()
	{
		return $this->_id;
	}


	/**
	 * Get title book
	 *
	 * @return	String with book title or NULL if there aren't book
	 * @since 0.1
	 */
	public function getTitle()
	{
		return $this->_title;
	}

	/**
	 * Get language book
	 *
	 * @return	String with language book or NULL if there aren't book
	 * @since 0.1
	 */
	public function getLanguage()
	{
		return $this->_language;
	}

	/**
	 * Get book description
	 *
	 * @return	String with book description or NULL if there aren't book
	 * @since 0.1
	 */
	public function getDescription()
	{
		return $this->_description;
	}

	/**
	 * Get image of the book
	 *
	 * @return	String with image book
	 * @since 0.1
	 */
	public function getImage()
	{
		return $this->_image;
	}

	/**
	 * Get rating of the book
	 *
	 * @return	Float with rating of the book or NULL if there aren't book
	 * @since 0.1
	 */
	public function getRating()
	{
		return (float)$this->_rating;
	}


	/**
	 * Get collection of book
	 *
	 * @return	String with collection of book or NULL if there aren't book
	 * @since 0.1
	 */
	public function getCollection()
	{
		return $this->_collection;
	}

	/**
	 * Get votes of the book
	 *
	 * @return	Integer with votes of the book or NULL if there aren't book
	 * @since 0.1
	 */
	public function getVotes()
	{
		return (int)$this->_votes;
	}

	/**
	 * Get URL
	 *
	 * @return	String with
	 * @since 0.1
	 */
	public function getURL()
	{
		return $this->_book_url;
	}


	/**
	 * Get all the tag
	 *
	 * @return	array with tag or NULL
	 * @since 0.1
	 */
	public function getTags()
	{
		global $db;


		if (is_null($this->_id)) {
			return NULL;
		}

		return $db->get_results("SELECT tag_word FROM Books_Tags WHERE book_id = $this->_id");

	}

	/**
	 * Get all the authors
	 *
	 * @return	array with authors information or NULL
	 * @since 0.1
	 */
	public function getAuthors()
	{
		global $db;

		if (is_null($this->_authors)) {
			if (is_null($this->_id)) {
				return NULL;
			}

			$this->_authors = $db->get_results("SELECT Authors.author_id, author_url, COALESCE(author_nickname, author_name) as author_name, contribution FROM Books_Authors, Authors WHERE Books_Authors.book_id = $this->_id and Books_Authors.author_id = Authors.author_id");
		}

		return $this->_authors;
	}

	/**
	 * Get only contributors of the book
	 *
	 * @return	array with writers names or NULL
	 * @since 0.1
	 */
	public function getContributors()
	{
		$contributors = array();

		if ($authors = $this->getAuthors()) {
			foreach( $authors as $author ) {
				if ($author['contribution'] != Author::$author_types['author']) {
					$contributors[] = $author;
				}
			}
		}

		return $contributors;
	}

	/**
	 * Get only writers of the book
	 *
	 * @return	array with writers names or NULL
	 * @since 0.1
	 */
	public function getWriters()
	{
		$writers = NULL;

		if($authors = $this->getAuthors()) {
			foreach($authors as $author ) {
				if ($author['contribution'] == Author::$author_types['author']) {
					$writers[] = $author;
				}
			}
		}

		return $writers;
	}

	/**
	 * Get the id of a book
	 *
	 * @param	url	Url of the book
	 * @return	integer or NULL if there is an error or there is not any book
	 * @since	0.1
	 */
	public static function getBookId($url)
	{
		global $db;

		if (!is_string($url)) {
			return NULL;
		}

		$where = "book_url = '". $db->escape($url) ."'";

		if(($book = $db->get_row("SELECT book_id FROM Books WHERE $where LIMIT 1"))) {
			return (int)$book['book_id'];
		}

		return NULL;
	}

	/**
	 * Read a book from de data base
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

		if(($book = $db->get_row("SELECT".Book::SQL."WHERE Books.book_id = $this->_id"))) {
			$this->setVariables($book);
			return TRUE;
		}

		return FALSE;
	}
}
