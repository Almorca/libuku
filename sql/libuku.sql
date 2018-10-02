-- SQL Libuku

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- ADVERTENCIAS:
-- La fecha '0000-00-00' se usa como un equivalente a NULL puesto que
-- usa menos espacio de datos e índice que usar valores NULL.
--
--
-- Base de datos: `libuku`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `authors`
--
DROP TABLE IF EXISTS `Authors`;
CREATE TABLE `Authors` (
  `author_id` integer unsigned auto_increment,
  `author_url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `author_name` varchar(255) collate utf8_spanish_ci,
  `author_born_date` date default '0000-00-00',
  `author_died_date` date default '0000-00-00',
  `author_biography` text collate utf8_spanish_ci,
  PRIMARY KEY  (`author_id`),
  UNIQUE KEY `author_url` (`author_url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


--
-- Estructura de tabla para la tabla `books`
--
DROP TABLE IF EXISTS `Books`;
CREATE TABLE `Books` (
  `book_id` integer unsigned auto_increment,
  `book_url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `book_title` varchar(255) collate utf8_spanish_ci NOT NULL,
  `book_language` char(2) character set ascii default 'es', -- 'ISO 639-1 code'
  `book_date_submitted` date default '0000-00-00', -- 'Date of submission of the book'
  `book_collection` varchar(255) collate utf8_spanish_ci, -- Serie of books
  `book_rating` TINYINT unsigned default NULL,
  `book_votes` integer unsigned default 0,
  `book_description` text collate utf8_spanish_ci,
  `book_genre` varchar(255) CHARACTER SET latin1 NOT NULL REFERENCES Genres(genre_url),
  `book_image` varchar(100)  collate utf8_spanish_ci,
  PRIMARY KEY  (`book_id`),
  UNIQUE KEY `book_url` (`book_url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Estructura de tabla para la tabla `Genres`
--
DROP TABLE IF EXISTS `Genres`;
CREATE TABLE `Genres` (
  `genre_url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `genre_title` varchar(255) collate utf8_spanish_ci NOT NULL,
  `genre_father` varchar(255) CHARACTER SET latin1 default NULL REFERENCES Genres(genre_url),
  PRIMARY KEY  (`genre_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Estructura de tabla para la tabla `Votes`
--
DROP TABLE IF EXISTS `Votes`;
CREATE TABLE `Votes` (
  `vote_id` integer unsigned auto_increment,
  `vote_date_submitted` date NOT NULL, -- 'Date of submission of the vote'
  `vote_rating` TINYINT unsigned NOT NULL,
  `vote_book_id` INTEGER UNSIGNED NOT NULL REFERENCES Books(book_id),
  PRIMARY KEY  (`vote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Estructura de tabla para la tabla `Users`
--
DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `user_id` integer unsigned auto_increment,
  `user_login` varchar(64) collate utf8_spanish_ci NOT NULL,
  `user_password` varchar(64) collate utf8_spanish_ci NOT NULL,
  `user_email` varchar(320) collate utf8_spanish_ci NOT NULL,
  `user_date_registered` timestamp default CURRENT_TIMESTAMP, -- 'Date of registered of the user'
  `user_mail_ok` boolean NOT NULL default FALSE, -- User validated
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_login` (`user_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


--
-- Estructura de tabla para la tabla `Comments`
--
DROP TABLE IF EXISTS `Comments`;
CREATE TABLE `Comments` (
  `comment_id` integer unsigned auto_increment,
  `comment_time_submitted` timestamp default CURRENT_TIMESTAMP,
  `comment_content` text collate utf8_spanish_ci NOT NULL,
  `comment_user_id` integer unsigned REFERENCES Users(user_id),
  `comment_book_id` integer unsigned REFERENCES Books(book_id),
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


--
-- Table structure for table `Tags`
--
DROP TABLE IF EXISTS `Tags`;
CREATE TABLE `Tags` (
  `tag_id` integer unsigned auto_increment,
  `tag_word` char(40) collate utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_word` (`tag_word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


--
-- n - n  tables
--
DROP TABLE IF EXISTS `Books_Tags`;
CREATE TABLE `Books_Tags` (
  `book_id` integer unsigned NOT NULL,
  `tag_id` integer unsigned NOT NULL,
  PRIMARY KEY (book_id, tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `Books_Authors`;
CREATE TABLE `Books_Authors` (
  `book_id` integer unsigned,
  `author_id` integer unsigned,
  `contribution` TINYINT unsigned NOT NULL default 1, -- 1 = author; 2 = 'translator', 3 = 'preface', 4 = 'editor'
  `author_nickname` varchar(255) collate utf8_spanish_ci, -- Seudónimo con el que el autor firma la obra si existiese
  PRIMARY KEY (book_id, author_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


---
-- triggers
---
DELIMITER |

---
-- trigger para actualizar el rating de un tag
---
CREATE TRIGGER Books_Tags_updateTagRating AFTER INSERT ON Books_Tags
  FOR EACH ROW BEGIN
    UPDATE Tags SET tag_rating = tag_rating + 1 WHERE Tags.tag_word = NEW.tag_word;
  END
|

---
-- trigger para actualizar el rating de un libro
---
CREATE TRIGGER Votes_updateBookRating AFTER INSERT ON Votes
  FOR EACH ROW BEGIN
    UPDATE Books SET
        book_votes = book_votes + 1,
        book_rating = ( SELECT SUM(vote_rating) / COUNT(*) FROM Votes WHERE Votes.vote_book_id = NEW.vote_book_id )
    WHERE Books.book_id = NEW.vote_book_id;
  END
|

DELIMITER ;



-- DATOS DE EJEMPLO

---
-- Constitución española
---
INSERT INTO Books (book_title, book_url, book_language, book_rating, book_description, book_genre, book_image)
VALUES ('Constitución Española', 'constitucion-espanola', 'es', NULL, '<p>Constitución española de 1978</p>', 'derecho', 'cover-medium-1.jpeg');


-- Armantia
INSERT INTO Books (book_title, book_url, book_language, book_rating, book_description, book_genre, book_collection, book_image)
VALUES ('Armantia', 'armantia', 'es', NULL, '<p>Alix Corp es una multinacional en pleno auge, que durante el año 2161 ha controlado su propia expansión gracias al dominio exclusivo de su tecnología más secreta: aquella que les hace moverse por otros universos a su antojo. Al poder entrar en otros universos iguales al suyo, pero más avanzados en el tiempo, pueden ver el futuro, el cual controlan.</p><p>Sin embargo, el jefe de investigación que creó dicha tecnología, Boris Ourumov, escapa de la compañía con planes en su contra, razón por la que Alix envía a Marla Enea, una de sus mejores agentes, a capturarle allí donde haya huido. Sin embargo su viaje se trunca, y aparece en un extraño mundo que bajo su superficie familiarmente medieval, oculta un secreto relacionado con Alix, el multiverso y la propia Marla.</p>', 'historia-alternativa', 'Serie Multiverso', 'cover-medium-2.jpeg');


INSERT INTO Authors (author_url, author_name, author_born_date, author_biography)
VALUES ('moises_cabello_aleman', 'Moisés Cabello', '1981', 'Moisés Cabello Alemán es un escritor español de ciencia ficción nacido en Tenerife (España) en 1981.');

INSERT INTO Books_Authors VALUES (2, 1, 1, NULL);

INSERT INTO Tags (tag_word) VALUES ('Multiverso');

INSERT INTO Books_Tags VALUES (2, 1);


-- Gemini
INSERT INTO Books (book_title, book_url, book_language, book_rating, book_description, book_genre, book_collection, book_image)
VALUES ('Gemini', 'gemini', 'es', NULL, '<p>Este libro es la segunda parte de <a href="http://www.libuku.com/libro/armantia">Armantia</a>, libro que comenzó la triología del Multiverso.</p><p>En este libro, Marla y Olaf repelen como pueden la inesperada invasión de una colonia cercana llamada Gemini mientras su única esperanza parece estar en un lugar considerado sólo una leyenda: Diploma. El virulento ataque separará a Enea de sus compañeros, que tendrá que seguir sus propio camino para salvar Armantia. Mientras, Julio Steinberg, presidente y emperador de la Red de la Humanidad, planea empezar a asimilar también mundos del caos, y quiere empezar por el que alberga a Armantia.</p>', 'historia-alternativa', 'Serie Multiverso', 'cover-medium-3.jpeg');
INSERT INTO Books_Authors VALUES (3, 1, 1, NULL);
INSERT INTO Books_Tags VALUES (3, 1);

-- Olimpo
INSERT INTO Books (book_title, book_url, book_language, book_rating, book_description, book_genre, book_collection, book_image)
VALUES ('Olimpo', 'olimpo', 'es', NULL, '<p>Olimpo es una antología de historias que despide Serie Multiverso recorriendo los vacíos de <a href="http://www.libuku.com/libro/armantia">Armantia</a> y <a href="http://www.libuku.com/libro/gemini">Gemini</a>, revelando secretos de algunas tramas y profundizando en varios personajes secundarios. Por tanto, es imprescindible leer antes las dos novelas anteriores.</p>', 'historia-alternativa', 'Serie Multiverso', 'cover-medium-4.jpeg');
INSERT INTO Books_Authors VALUES (4, 1, 1, NULL);
INSERT INTO Books_Tags VALUES (4, 1);


-- La cautiva
INSERT INTO Books (book_title, book_url, book_language, book_rating, book_description, book_genre, book_collection, book_image)
VALUES ('La cautiva', 'la-cautiva', 'es', NULL, '<p>La cautiva es un poema épico, publicado en 1837, dentro del libro Rimas. El texto ha sido considerado como la primera gran obra de la literatura argentina, antecedente inmediato de la aparición de la novela en ese país y a la vez vehículo para el éxito del romanticismo, que el propio Echeverría había introducido en la literatura de habla hispana, en una Argentina que aún se encontraba en formación.</p><p>Un malón de indios irrumpe en una población fronteriza de blancos y toma cautiva (entre otros) a María, más tarde su esposo Brian, al intentar rescatarla sufre la misma suerte que la mujer. Los aborígenes festejan la victoria con un gran festín y la mujer &mdash;puñal en mano&mdash; aprovecha la confusión para liberar a su esposo malherido. Ambos buscan refugio en el desierto, en tanto que las tropas cristianas llegan hasta la toldería pero no encuentran a su jefe. La pareja comienza entonces una penosa huida en la que deben soportar la sed que los abrasa, la presencia de un tigre y la quemazón de unos pajonales que los rodean. Brian no resiste la aventura y muere. María sepulta a su esposo y continúa su camino con una sola esperanza: encontrar a su hijo. La mujer es hallada, finalmente, por un grupo de soldados que le informan la muerte del niño, degollado por los salvajes. Frente a esta noticia, María fallece. La llanura pampeana encierra en su seno las tumbas de los esposos.</p>', 'poesia', NULL, 'cover-medium-5.jpeg');


INSERT INTO Authors (author_url, author_name, author_born_date, author_died_date, author_biography)
VALUES ('esteban-echeverria', 'Esteban Echeverría', '1805-09-02', '1851-01-19', '<p>José Esteban Antonio Echeverría Espinosa (Buenos Aires, Argentina, 2 de septiembre de 1805 - Montevideo, Uruguay, 19 de enero de 1851) fue un escritor y poeta argentino, que introdujo el romanticismo en su país. Perteneciente a la denominada Generación del 37, es autor de obras como Dogma Socialista, La cautiva y El matadero, entre otras.</p>');

INSERT INTO Books_Authors VALUES (5, 2, 1, NULL);


-- Libro del buen amor

INSERT INTO Books (book_title, book_url, book_language, book_rating, book_description, book_genre, book_collection, book_image)
VALUES ('Libro de buen amor', 'libro-de-buen-amor', 'es', NULL, '<p>El <strong>Libro de buen amor</strong> es un mester de clerecía  considerado una de las obras cumbres de la literatura española</p>', 'literatura-medieval', NULL, NULL);


INSERT INTO Authors (author_url, author_name, author_born_date, author_biography)
VALUES ('moises_cabello_aleman', 'Moisés Cabello', '1981', 'Moisés Cabello Alemán es un escritor español de ciencia ficción nacido en Tenerife (España) en 1981.');

INSERT INTO Books_Authors VALUES (2, 1, 1, NULL);

INSERT INTO Tags (tag_word) VALUES ('Multiverso');

INSERT INTO Books_Tags VALUES (2, 1);



--
-- La Regenta
--
INSERT INTO Categories (category_title) VALUES ('Novela');

INSERT INTO Books (book_title, book_url, book_language, book_rating, book_description, book_genre)
VALUES ('La Regenta', 'la-regenta', 'es', NULL, 'La Regenta es la primera novela de Leopoldo Alas "Clarín", publicada en dos tomos en 1884 y 1885 respectivamente. Considerada la obra cumbre de su autor y de la novela del siglo XIX, además es uno de los máximos exponentes del naturalismo y del realismo progresista. Además, incorpora una técnica novedosa; la técnica del sueño o fluir de los recuerdos.', 'Novela');

INSERT INTO Authors (author_url, author_name, author_surname, author_nickname, author_born_date, author_died_date, author_biography, author_webpage, author_wikipedia)
VALUES ('leopoldo_alas', 'Leopoldo', 'Alas', 'Leopoldo Alas «Clarín»', '1852-04-25', '1901-06-13', 'Célebre escritor español', NULL, 'http://es.wikipedia.org/wiki/Leopoldo_Alas_%C2%ABClar%C3%ADn%C2%BB');

INSERT INTO Books_Authors VALUES (1, 1, 'author');

INSERT INTO Authors (author_url, author_name, author_surname, author_born_date, author_died_date, author_biography)
VALUES ('benito_perez_galdos', 'Benito', 'Pérez Galdós', '1843-05-10', '1920-01-04', 'Célebre escritor español');

INSERT INTO Books_Authors VALUES (1, 2, 'preface');

INSERT INTO Tags (tag_word) VALUES ('Oviedo');

INSERT INTO Books_Tags VALUES (1, 'Oviedo');



---
-- Consultas
---


-- Obtener autores
SELECT Authors.author_id as author_id, author_name, author_surname, contribution  FROM `Authors`, `Books_Authors`
WHERE book_id = 1 -- id del libro
AND Authors.author_id = Books_Authors.author_id;

SELECT Tags.* from Books_Tags, Tags
WHERE book_id = 1
AND Tags.tag_word = Books_Tags.tag_word;
