<?php
/**
 * A class to manage a error.
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
 * @copyright	&copy; 2011 Alejandro Moreno Calvo
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @version		Release: @package_version@
 * @since		0.1
 */
class Error
{
	public static function printError($str = '' ) {
		print "<div class='error'>Ocurrió un error mientras se intentaba mostrar la página.</div>";
	}
}