<?php
/**
 * @file   google_weather_data.php
 * @author António P. P. Almeida <appa@perusio.net>
 * @date   Fri Oct 14 17:44:57 2011
 *
 * @brief  A simple script that queries Google for the weather data for a given city. It
 *         relies on Maxmind's GeoIP Nginx integration for getting the city of
 *         the client IP.
 */

// The language in which the language is going to be retrieved.
define('GOOGLE_WEATHER_LANG', 'pt-pt');
// The Google Weather URI.
define('GOOGLE_WEATHER_URI', 'http://www.google.com/ig/api?hl=' . GOOGLE_WEATHER_LANG . '&weather=');
// The default city for Google weather.
define('DEFAULT_CITY', 'Lisboa');

/**
 * Gets the weather data from Google.
 *
 * @param $default_city string
 *   The default city for which the data is requested. Use only if the GeoIP
 *    database doesn't return anything.
 *
 * @return string
 *   The data obtained from Google in XML.
 */

function get_google_weather_data($default_city = DEFAULT_CITY) {

  // Get the GeoIP city if possible. Otherwise use the default. The city name
  // is obtained from an HTTP header.
  $nginx_geoip_city =  empty($_GET['city']) ? $default_city :
    filter_var($_GET['city'], FILTER_SANITIZE_STRING,
               array('options' => array('default' => $default_city)));

  // Create the cURL handler.
  $ch = curl_init();
  // Set the cURL options.
  curl_setopt_array($ch, array(CURLOPT_URL => GOOGLE_WEATHER_URI . $nginx_geoip_city,
                               CURLOPT_RETURNTRANSFER => TRUE,
                               CURLOPT_HEADER => FALSE,));
  // Get the data from Google.
  $data = curl_exec($ch);
  // Get the encoding of the reply.
  $encoding = array_pop(explode('charset=', curl_getinfo($ch, CURLINFO_CONTENT_TYPE)));
  // Close the cURL handler.
  curl_close($ch);

  // Verify the encoding. Fix it if necessary.
  return $encoding != 'UTF-8' ? @iconv($encoding, 'UTF-8', $data) : $data;
} // get_google_weather_data

// Print the output.
print get_google_weather_data();