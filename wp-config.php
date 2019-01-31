<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'woocommerce');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', '');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Y7`xokRzX:&0#,*6|R:8wIk$pqwu-!z;~|y6;a:Xbwyf_f[76~Q&+P6ZdScHhEmk');
define('SECURE_AUTH_KEY',  '6=ZPSDCVGv%otuq{9Ew(rw*R T~.e[Q)#SSlI%8 e~1sNZqGGVC5)VX^+)/L}H}T');
define('LOGGED_IN_KEY',    ')66PqasA}s]ay}S,F]?N%Nf$TB]oso)7MvV)^UK;DVO6{C;Wu5 e94MA0KNG.EE#');
define('NONCE_KEY',        '( H_oXtYYNc-kSh4rK*8Sl<6M*l!(3,N_+og]N=Z)V?~^f{dt*M&g1:Vl5S3*ido');
define('AUTH_SALT',        'MA[:8 );hmj^|+b!bQ#$W@A>EMD=8/Lq3e.Ve)pZ%/w ibe 0-uDQOMI^c(a)oC@');
define('SECURE_AUTH_SALT', '%#!De!g1sz5%j .GQ[96[iN7x)=.b;*h#yQ=i^`T%3ZX%twc$O<rF1afm81X6E&.');
define('LOGGED_IN_SALT',   'oldtI*x,&g@^rbj|p2N4zFf6=rcZqUu+3%IDixP*p2:<x#&EgDGutH?*eXl~)g.C');
define('NONCE_SALT',       'D&&ZJ#[S0~;tC_oXUZ`Q2U3RC1O Jh{[Hr.TdMAm>G =*^r,1&24}IiG34AK`>Zp');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wc_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');