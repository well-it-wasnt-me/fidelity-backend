<?php

/**
 * Convert all applicable characters to HTML entities. *
 * @param string|null $text The string
 *
 * @return string The html encoded string
 */
function html(string $text = null): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Set locale
 *
 * @param string $locale The locale (en_US)
 * @param string $domain The text domain (messages) *
 * @throws UnexpectedValueException
 *
 * @retrun void
 */
function set_language(string $locale, string $domain = 'messages'): void
{
    clearstatcache();
    $actual_locales = ['it_IT', 'en_US', 'hr_HR', 'es_ES'];
    if(!in_array($locale, $actual_locales)){
        $locale = 'en_US';
    }

    $codeset = 'UTF-8';
    putenv("LANGUAGE=$locale.UTF-8");
    putenv("LC_ALL=$locale");
    putenv("LC_LANG=$locale");
    putenv("LC_LANGUAGE=$locale");
    $directory = __DIR__ . '/../../resources/text';
    // Set locale information
    setlocale(LC_ALL, $locale);
// Check for existing mo file (optional)
    $file = sprintf('%s/%s/LC_MESSAGES/%s_%s.mo', $directory, $locale, $domain, $locale);
    if (!file_exists($file)) {
        throw new UnexpectedValueException(sprintf('File not found: %s', $file));
    }
    // Generate new text domain
    $textDomain = sprintf('%s_%s', $domain, $locale); // Set base directory for all locales
   // echo $textDomain ." - " . $directory;
    bindtextdomain($textDomain, $directory); // Set domain codeset
    bind_textdomain_codeset($textDomain, $codeset);
    textdomain($textDomain);
}
/**
 * Text translation.
 *
 * @param string $message The message
 * @param string|int|float|bool ...$context The context *
 * @return string The translated string
 */
function __(string $message, ...$context): string
{
    $translated = gettext($message);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source https://gravatar.com/site/implement/images/php/
 */
function get_gravatar($email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array()): string
{
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email ?? '')));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val) {
            $url .= ' ' . $key . '="' . $val . '"';
        }
        $url .= ' />';
    }
    return $url;
}

function returnLocale(){
    clearstatcache();
    if( !isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en_US';
    }
    $actual_locale = substr(str_replace("-", "_",$_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 5);
    $registered_locale = ['it_IT', 'en_US', 'es_ES', 'hr_HR'];

    if(!in_array($actual_locale, $registered_locale)){
        return 'en_US';
    } else {
        return $actual_locale;
    }
}