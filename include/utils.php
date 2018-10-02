<?
// The source code packaged with this file is Free Software, Copyright (C) 2008 by
// Alejandro Moreno Calvo < almorca@gmail.com >
// It's licensed under the GNU AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.fsf.org/licensing/licenses/agpl-3.0.html
// GNU AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".


/**
 * Return the url name of the server
 */
function getServerName() {
	global $server_name;

	if($_SERVER['SERVER_NAME']) {
		return $_SERVER['SERVER_NAME'];
	} else {
		if ($server_name) {
			return $server_name;
		} else {
			return 'libuku.com'; // Warn: did you put the right server name?
		}
	}
}

/**
 * Return the url base of the web
 */
function getBaseUrl() {
	global $globals;

	return getServerName() . $globals['base_url'];
}


/**
 * Generate a internal URL
 *
 * @param	String	$section	Web section
 * @param	String	$page	The page to show
 * @return	String with a element or NULL if and error occurs
 * @author	Alejandro Moreno Calvo
 * @since	0.1
 */
function getURL($section, $page = '') {
	global $sections;

	if ( !array_key_exists($section, $sections) ) {
		return NULL;
	}

	return 'http://' . getBaseUrl() . $sections[$section] . $page;
}

/**
 * Generate a internal link
 *
 * @param	String	$text	Link text
 * @param	String	$section	Web section
 * @param	String	$page	The page to show
 * @param	Array	$atributes	Link attributes
 *
 * @return	String with a element or NULL if and error occurs
 * @author	Alejandro Moreno Calvo
 * @since	0.1
 */
function getLink($text, $section, $page = NULL, $atributes = NULL) {
	$url = '<a href="' . getURL($section, $page);

	if ( $url == '<a href="' ) { // Error in getURL function
		return NULL;
	}

	$url .= '"';
	if (is_array($atributes)) {
		foreach ($atributes as $key => $value) {
			$url .= " " . $key . '="' . $value . '"';
		}
	}
	$url .= '>' . $text . '</a>';

	return $url;
}


/**
* US-ASCII transliterations of Unicode text
* Ported <a href="http://search.cpan.org/~sburke/Text-Unidecode-0.04/lib/Text/Unidecode.pm">
* Sean M. Burke's Text::Unidecode Perl module (He did all the hard work!)</a>
* Warning: you should only pass this well formed UTF-8!
* Be aware it works by making a copy of the input string which it appends transliterated
* characters to - it uses a PHP output buffer to do this - it means, memory use will increase,
* requiring up to the same amount again as the input string
*
* Code under the "Artistic License"
*
* @param str	UTF-8 string to convert without BOM
* @param unkown	Character use if character unknown (default = '').
* @return		US-ASCII string or NULL if the string is not UTF-8
* @author 		harryf
* @license	http://www.perlfoundation.org/artistic_license_1_0	Artistic License
* @since 		0.1
*/
function utf82ascii($str, $unknown = '')
{
	$len = strlen($str);

	if ($len == 0) {
		return '';
	}

	/* Use an output buffer to copy the transliterated string
	   This is done for performance vs. string concatenation.
	   See http://phplens.com/lens/php-book/optimizing-debugging-php.php
	   Section  "High Return Code Optimizations"
	*/
	ob_start();

	$i = 0;
	do {
		$ord = NULL;

		$newchar = $str{$i};
		$ord0 = ord($newchar);
		# 1 byte - ASCII
		if ( $ord0 >= 0 && $ord0 <= 127 ) {
			$ord = $ord0;
			$increment = 1;
		} else { # 2 bytes
			$ord1 = ord($str{$i+1});

			if ( $ord0 >= 192 && $ord0 <= 223 ) {
				$ord = ( $ord0 - 192 ) * 64 + ( $ord1 - 128 );
				$increment = 2;
			} else { # 3 bytes
				$ord2 = ord($str{$i+2});

				if ( $ord0 >= 224 && $ord0 <= 239 ) {
					$ord = ($ord0-224)*4096 + ($ord1-128)*64 + ($ord2-128);
					$increment = 3;
				} else { # 4 bytes
					$ord3 = ord($str{$i+3});

					if ($ord0>=240 && $ord0<=247) {
						$ord = ($ord0-240)*262144 + ($ord1-128)*4096 + ($ord2-128)*64 + ($ord3-128);
						$increment = 4;
					} else {
						ob_end_clean();
						trigger_error("utf8_to_ascii: looks like badly formed UTF-8 at byte $i", E_USER_WARNING);
						return FALSE;
					}
				}
			}
	       	}

		if ($increment == 1) {
			echo $newchar;
		}

		$i += $increment;
	} while ($i < $len);

	$str = ob_get_contents();
	ob_end_clean();
	return $str;
}


/**
 * Converts all special characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param string $string Text that might have special characters
 * @return string Filtered string with replaced "nice" characters.
 * @since 0.1
 */
function convert_specials($string) {
	if ( !preg_match('/[\x80-\xff]/', $string) )
		return $string;

	$specialChar = array (
		'á' => 'a',
		'é' => 'e',
		'í' => 'i',
		'ó' => 'o',
		'ú' => 'u',
		'ü' => 'u',
		'ñ' => 'n',
		'Á' => 'A',
		'É' => 'E',
		'Í' => 'I',
		'Ó' => 'O',
		'Ú' => 'U',
		'Ü' => 'U',
		'Ñ' => 'N',
		'€' => 'E'
	);

	return str_replace(array_keys($specialChar), array_values($specialChar), $string);
}

/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL.
 *
 * @since 0.1
 *
 * @param string $url The URL to be cleaned.
 * @param integer Optional $max_length Max length of cleaned $url.
 * @return string The cleaned $url after the 'cleaned_url' filter is applied.
 */
function clean_url($url, $max_length = null)
{
	if ($url == '')
		return $url;

	$url = preg_replace("/\s+/", "-", trim($url));
	$url = convert_specials($url);
	$url = utf82ascii($url);
	$url = strtolower($url);
	if (is_int($max_length)) {
		$url = substr($url, 0, $max_length);
	}

	return $url;
}

function languageISO2Human($isoCode, $languageLocation = "en")
{
	$languageCodes["en"] = array(
		"aa" => "Afar",
		"ab" => "Abkhazian",
		"ae" => "Avestan",
		"af" => "Afrikaans",
		"ak" => "Akan",
		"am" => "Amharic",
		"an" => "Aragonese",
		"ar" => "Arabic",
		"as" => "Assamese",
		"av" => "Avaric",
		"ay" => "Aymara",
		"az" => "Azerbaijani",
		"ba" => "Bashkir",
		"be" => "Belarusian",
		"bg" => "Bulgarian",
		"bh" => "Bihari",
		"bi" => "Bislama",
		"bm" => "Bambara",
		"bn" => "Bengali",
		"bo" => "Tibetan",
		"br" => "Breton",
		"bs" => "Bosnian",
		"ca" => "Catalan",
		"ce" => "Chechen",
		"ch" => "Chamorro",
		"co" => "Corsican",
		"cr" => "Cree",
		"cs" => "Czech",
		"cu" => "Church Slavic",
		"cv" => "Chuvash",
		"cy" => "Welsh",
		"da" => "Danish",
		"de" => "German",
		"dv" => "Divehi",
		"dz" => "Dzongkha",
		"ee" => "Ewe",
		"el" => "Greek",
		"en" => "English",
		"eo" => "Esperanto",
		"es" => "Spanish",
		"et" => "Estonian",
		"eu" => "Basque",
		"fa" => "Persian",
		"ff" => "Fulah",
		"fi" => "Finnish",
		"fj" => "Fijian",
		"fo" => "Faroese",
		"fr" => "French",
		"fy" => "Western Frisian",
		"ga" => "Irish",
		"gd" => "Scottish Gaelic",
		"gl" => "Galician",
		"gn" => "Guarani",
		"gu" => "Gujarati",
		"gv" => "Manx",
		"ha" => "Hausa",
		"he" => "Hebrew",
		"hi" => "Hindi",
		"ho" => "Hiri Motu",
		"hr" => "Croatian",
		"ht" => "Haitian",
		"hu" => "Hungarian",
		"hy" => "Armenian",
		"hz" => "Herero",
		"ia" => "Interlingua",
		"id" => "Indonesian",
		"ie" => "Interlingue",
		"ig" => "Igbo",
		"ii" => "Sichuan Yi",
		"ik" => "Inupiaq",
		"io" => "Ido",
		"is" => "Icelandic",
		"it" => "Italian",
		"iu" => "Inuktitut",
		"ja" => "Japanese",
		"jv" => "Javanese",
		"ka" => "Georgian",
		"kg" => "Kongo",
		"ki" => "Kikuyu",
		"kj" => "Kwanyama",
		"kk" => "Kazakh",
		"kl" => "Kalaallisut",
		"km" => "Khmer",
		"kn" => "Kannada",
		"ko" => "Korean",
		"kr" => "Kanuri",
		"ks" => "Kashmiri",
		"ku" => "Kurdish",
		"kv" => "Komi",
		"kw" => "Cornish",
		"ky" => "Kirghiz",
		"la" => "Latin",
		"lb" => "Luxembourgish",
		"lg" => "Ganda",
		"li" => "Limburgish",
		"ln" => "Lingala",
		"lo" => "Lao",
		"lt" => "Lithuanian",
		"lu" => "Luba-Katanga",
		"lv" => "Latvian",
		"mg" => "Malagasy",
		"mh" => "Marshallese",
		"mi" => "Maori",
		"mk" => "Macedonian",
		"ml" => "Malayalam",
		"mn" => "Mongolian",
		"mr" => "Marathi",
		"ms" => "Malay",
		"mt" => "Maltese",
		"my" => "Burmese",
		"na" => "Nauru",
		"nb" => "Norwegian Bokmal",
		"nd" => "North Ndebele",
		"ne" => "Nepali",
		"ng" => "Ndonga",
		"nl" => "Dutch",
		"nn" => "Norwegian Nynorsk",
		"no" => "Norwegian",
		"nr" => "South Ndebele",
		"nv" => "Navajo",
		"ny" => "Chichewa",
		"oc" => "Occitan",
		"oj" => "Ojibwa",
		"om" => "Oromo",
		"or" => "Oriya",
		"os" => "Ossetian",
		"pa" => "Panjabi",
		"pi" => "Pali",
		"pl" => "Polish",
		"ps" => "Pashto",
		"pt" => "Portuguese",
		"qu" => "Quechua",
		"rm" => "Raeto-Romance",
		"rn" => "Kirundi",
		"ro" => "Romanian",
		"ru" => "Russian",
		"rw" => "Kinyarwanda",
		"sa" => "Sanskrit",
		"sc" => "Sardinian",
		"sd" => "Sindhi",
		"se" => "Northern Sami",
		"sg" => "Sango",
		"si" => "Sinhala",
		"sk" => "Slovak",
		"sl" => "Slovenian",
		"sm" => "Samoan",
		"sn" => "Shona",
		"so" => "Somali",
		"sq" => "Albanian",
		"sr" => "Serbian",
		"ss" => "Swati",
		"st" => "Southern Sotho",
		"su" => "Sundanese",
		"sv" => "Swedish",
		"sw" => "Swahili",
		"ta" => "Tamil",
		"te" => "Telugu",
		"tg" => "Tajik",
		"th" => "Thai",
		"ti" => "Tigrinya",
		"tk" => "Turkmen",
		"tl" => "Tagalog",
		"tn" => "Tswana",
		"to" => "Tonga",
		"tr" => "Turkish",
		"ts" => "Tsonga",
		"tt" => "Tatar",
		"tw" => "Twi",
		"ty" => "Tahitian",
		"ug" => "Uighur",
		"uk" => "Ukrainian",
		"ur" => "Urdu",
		"uz" => "Uzbek",
		"ve" => "Venda",
		"vi" => "Vietnamese",
		"vo" => "Volapuk",
		"wa" => "Walloon",
		"wo" => "Wolof",
		"xh" => "Xhosa",
		"yi" => "Yiddish",
		"yo" => "Yoruba",
		"za" => "Zhuang",
		"zh" => "Chinese",
		"zu" => "Zulu"
		);
	$languageCodes["es"] = array (
		'aa' => 'Afar',
		'ab' => 'Abjasio',
		'ae' => 'Avéstico',
		'af' => 'Afrikaans',
		'ak' => 'Akano',
		'am' => 'Amárico',
		'an' => 'Aragonés',
		'ar' => 'Árabe',
		'as' => 'Asamés',
		'av' => 'Avar',
		'ay' => 'Aimara',
		'az' => 'Azerí',
		'ba' => 'Baskir',
		'be' => 'Bielorruso',
		'bg' => 'Búlgaro',
		'bh' => 'Bhojpurí',
		'bi' => 'Bislama',
		'bm' => 'Bambara',
		'bn' => 'Bengalí',
		'bo' => 'Tibetano',
		'br' => 'Bretón',
		'bs' => 'Bosnio',
		'ca' => 'Catalán',
		'ce' => 'Checheno',
		'ch' => 'Chamorro',
		'co' => 'Corso',
		'cr' => 'Cree',
		'cs' => 'Checo',
		'cu' => 'Eslavo eclesiástico antiguo',
		'cv' => 'Chuvasio',
		'cy' => 'Galés',
		'da' => 'Danés',
		'de' => 'Alemán',
		'dv' => 'Maldivo',
		'dz' => 'Dzongkha',
		'ee' => 'Ewe',
		'el' => 'Griego (moderno)',
		'en' => 'Inglés',
		'eo' => 'Esperanto',
		'es' => 'Español',
		'et' => 'Estonio',
		'eu' => 'Euskera',
		'fa' => 'Persa',
		'ff' => 'Fula',
		'fi' => 'Finlandés',
		'fj' => 'Fiyiano',
		'fo' => 'Feroés',
		'fr' => 'Francés',
		'fy' => 'Frisio',
		'ga' => 'Irlandés',
		'gd' => 'Gaélico escocés',
		'gl' => 'Gallego',
		'gn' => 'Guaraní',
		'gu' => 'Guyaratí',
		'gv' => 'Gaélico manés',
		'ha' => 'Hausa',
		'he' => 'Hebreo',
		'hi' => 'Hindú',
		'ho' => 'Hiri motu',
		'hr' => 'Croata',
		'ht' => 'Haitiano',
		'hu' => 'Húngaro',
		'hy' => 'Armenio',
		'hz' => 'Herero',
		'ia' => 'Interlingua',
		'id' => 'Indonesio',
		'ie' => 'Occidental',
		'ig' => 'Igbo',
		'ii' => 'Yi De Sichuán',
		'ik' => 'Inupiaq',
		'io' => 'Ido',
		'is' => 'Islandés',
		'it' => 'Italiano',
		'iu' => 'Inuktitut',
		'ja' => 'Japonés',
		'jv' => 'Javanés',
		'ka' => 'Georgiano',
		'kg' => 'Kongo',
		'ki' => 'Kikuyu',
		'kj' => 'Kuanyama',
		'kk' => 'Kazajo',
		'kl' => 'Groenlandés',
		'km' => 'Camboyano',
		'kn' => 'Canarés',
		'ko' => 'Coreano',
		'kr' => 'Kanuri',
		'ks' => 'Cachemiro',
		'ku' => 'Kurdo',
		'kv' => 'Komi',
		'kw' => 'Córnico',
		'ky' => 'Kirguís',
		'la' => 'Latín',
		'lb' => 'Luxemburgués',
		'lg' => 'Luganda',
		'li' => 'Limburgués',
		'ln' => 'Lingala',
		'lo' => 'Lao',
		'lt' => 'Lituano',
		'lu' => 'Luba-katanga',
		'lv' => 'Letón',
		'mg' => 'Malgache',
		'mh' => 'Marshalés',
		'mi' => 'Maorí',
		'mk' => 'Macedonio',
		'ml' => 'Malayalam',
		'mn' => 'Mongol',
		'mo' => 'Moldavo',
		'mr' => 'Maratí',
		'ms' => 'Malayo',
		'mt' => 'Maltés',
		'my' => 'Birmano',
		'na' => 'Nauruano',
		'nb' => 'Noruego bokmål',
		'nd' => 'Ndebele del norte',
		'ne' => 'Nepalí',
		'ng' => 'Ndonga',
		'nl' => 'Holandés',
		'nn' => 'Nynorsk',
		'no' => 'Noruego',
		'nr' => 'Ndebele del sur',
		'nv' => 'Navajo',
		'ny' => 'Chichewa',
		'oc' => 'Occitano',
		'oj' => 'Ojibwa',
		'om' => 'Oromo',
		'or' => 'Oriya',
		'os' => 'Osético',
		'pa' => 'Panyabí',
		'pi' => 'Pali',
		'pl' => 'Polaco',
		'ps' => 'Pastú',
		'pt' => 'Portugués',
		'qu' => 'Quechua',
		'rm' => 'Retorrománico',
		'rn' => 'Kirundi',
		'ro' => 'Rumano',
		'ru' => 'Ruso',
		'rw' => 'Ruandés',
		'sa' => 'Sánscrito',
		'sc' => 'Sardo',
		'sd' => 'Sindhi',
		'se' => 'Sami septentrional',
		'sg' => 'Sango',
		'si' => 'Cingalés',
		'sk' => 'Eslovaco',
		'sl' => 'Esloveno',
		'sm' => 'Samoano',
		'sn' => 'Shona',
		'so' => 'Somalí',
		'sq' => 'Albanés',
		'sr' => 'Serbio',
		'ss' => 'Suazi',
		'st' => 'Sesotho',
		'su' => 'Sundanés',
		'sv' => 'Sueco',
		'sw' => 'Suajili',
		'ta' => 'Tamil',
		'te' => 'Telugú',
		'tg' => 'Tayiko',
		'th' => 'Tailandés',
		'ti' => 'Tigriña',
		'tk' => 'Turcomano',
		'tl' => 'Tagalo',
		'tn' => 'Setsuana',
		'to' => 'Tongano',
		'tr' => 'Turco',
		'ts' => 'Tsonga',
		'tt' => 'Tártaro',
		'tw' => 'Twi',
		'ty' => 'Tahitiano',
		'ug' => 'Uigur',
		'uk' => 'Ucraniano',
		'ur' => 'Urdu',
		'uz' => 'Uzbeko',
		've' => 'Venda',
		'vi' => 'Vietnamita',
		'wl' => 'Walisiano',
		'vo' => 'Volapük',
		'wa' => 'Valón',
		'wo' => 'Wolof',
		'xh' => 'Xhosa',
		'yi' => 'Yídish',
		'yo' => 'Yoruba',
		'za' => 'Zhuang',
		'zh' => 'Chino',
		'zu' => 'Zulú'
	);

	if ( (array_key_exists($languageLocation, $languageCodes) == false)  || (array_key_exists($isoCode, $languageCodes[$languageLocation]) == false) ) {
		return NULL;
	}

	return $languageCodes[$languageLocation][$isoCode];

}
