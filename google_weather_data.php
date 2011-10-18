<?php
/**
 * @file   google_weather_data.php
 * @author António P. P. Almeida <appa@perusio.net>
 * @date   Fri Oct 14 17:44:57 2011
 *
 * @brief  A simple script that queries Google for the weather data for a given city. It
 *         relies
 *
 *
 */

// The language in which the language is going to be retrieved.
define('GOOGLE_WEATHER_LANG', 'pt-pt');
// The Google Weather URI.
define('GOOGLE_WEATHER_URI', 'http://www.google.com/ig/api?hl=' . GOOGLE_WEATHER_LANG . '&weather=');

/**
 * Gets the weather data from Google.
 *
 *
 * @return string
 *   The data obtained from Google in XML.
 */
function get_google_weather_data() {

  if (isset($nginx_geoip_city)) {
    printf("nginx city: %s\n", $nginx_geoip_city);
  }

  $nginx_geoip_city = empty($nginx_geoip_city) ? 'Lisboa' : $nginx_geoip_city;

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

print_r(get_google_weather_data());