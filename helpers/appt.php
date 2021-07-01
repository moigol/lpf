<?php
/**
 * PHP 7++
 *
 * LightPHPFrame
 * Copyright (c) Mo Ses
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @package       Utility helper
 * @version       LightPHPFrame v1.1.10
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Appt
{
    static public function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hr',
            'i' => 'min',
            's' => 'sec',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function getLanguages()
    {
        $db = new DB();
        $sql = "SELECT LanguageCode, LanguageName
            FROM `languages`";

        $data = $db->query($sql);        

        return $data;
    }

    public static function getLanguagesAsOption()
    {
        $return = array();
        foreach (self::getLanguages() as $key => $value) {
            $return[] = $key . ':' . $value;
        }

        return $return;
    }

    public static function getSalutation()
    {
        return array("" => "Select Prefix", "Mr" => "Mr", "Mrs" => "Mrs", "Miss" => "Miss", "Ms" => "Ms", "Dr" => "Dr", "Prof" => "Prof", "Rev" => "Rev");
    }

    public static function getGender()
    {
        return array("M" => "Male", "F" => "Female");
    }

    public static function getAge($date)
    {
        $from = new DateTime($date);
        $to   = new DateTime('today');
        return intval($from->diff($to)->y);
    }

    public static function getCountries($code = true)
    {
        $countries = array("Australia", "Canada", "Germany", "New Zealand", "Spain", "United Kingdom", "United States", "Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

        if ($code) {
            $countries = array(
                'AU' => 'Australia',
                'CA' => 'Canada',
                'DE' => 'Germany',
                'NZ' => 'New Zealand',
                'ES' => 'Spain',
                'GB' => 'United Kingdom',
                'US' => 'United States',

                'AF' => 'Afghanistan',
                'AX' => 'Aland Islands',
                'AL' => 'Albania',
                'DZ' => 'Algeria',
                'AS' => 'American Samoa',
                'AD' => 'Andorra',
                'AO' => 'Angola',
                'AI' => 'Anguilla',
                'AQ' => 'Antarctica',
                'AG' => 'Antigua And Barbuda',
                'AR' => 'Argentina',
                'AM' => 'Armenia',
                'AW' => 'Aruba',
                'AT' => 'Austria',
                'AZ' => 'Azerbaijan',
                'BS' => 'Bahamas',
                'BH' => 'Bahrain',
                'BD' => 'Bangladesh',
                'BB' => 'Barbados',
                'BY' => 'Belarus',
                'BE' => 'Belgium',
                'BZ' => 'Belize',
                'BJ' => 'Benin',
                'BM' => 'Bermuda',
                'BT' => 'Bhutan',
                'BO' => 'Bolivia',
                'BA' => 'Bosnia And Herzegovina',
                'BW' => 'Botswana',
                'BV' => 'Bouvet Island',
                'BR' => 'Brazil',
                'IO' => 'British Indian Ocean Territory',
                'BN' => 'Brunei Darussalam',
                'BG' => 'Bulgaria',
                'BF' => 'Burkina Faso',
                'BI' => 'Burundi',
                'KH' => 'Cambodia',
                'CM' => 'Cameroon',
                'CV' => 'Cape Verde',
                'KY' => 'Cayman Islands',
                'CF' => 'Central African Republic',
                'TD' => 'Chad',
                'CL' => 'Chile',
                'CN' => 'China',
                'CX' => 'Christmas Island',
                'CC' => 'Cocos (Keeling) Islands',
                'CO' => 'Colombia',
                'KM' => 'Comoros',
                'CG' => 'Congo',
                'CD' => 'Congo, Democratic Republic',
                'CK' => 'Cook Islands',
                'CR' => 'Costa Rica',
                'CI' => 'Cote D\'Ivoire',
                'HR' => 'Croatia',
                'CU' => 'Cuba',
                'CY' => 'Cyprus',
                'CZ' => 'Czech Republic',
                'DK' => 'Denmark',
                'DJ' => 'Djibouti',
                'DM' => 'Dominica',
                'DO' => 'Dominican Republic',
                'EC' => 'Ecuador',
                'EG' => 'Egypt',
                'SV' => 'El Salvador',
                'GQ' => 'Equatorial Guinea',
                'ER' => 'Eritrea',
                'EE' => 'Estonia',
                'ET' => 'Ethiopia',
                'FK' => 'Falkland Islands (Malvinas)',
                'FO' => 'Faroe Islands',
                'FJ' => 'Fiji',
                'FI' => 'Finland',
                'FR' => 'France',
                'GF' => 'French Guiana',
                'PF' => 'French Polynesia',
                'TF' => 'French Southern Territories',
                'GA' => 'Gabon',
                'GM' => 'Gambia',
                'GE' => 'Georgia',
                'GH' => 'Ghana',
                'GI' => 'Gibraltar',
                'GR' => 'Greece',
                'GL' => 'Greenland',
                'GD' => 'Grenada',
                'GP' => 'Guadeloupe',
                'GU' => 'Guam',
                'GT' => 'Guatemala',
                'GG' => 'Guernsey',
                'GN' => 'Guinea',
                'GW' => 'Guinea-Bissau',
                'GY' => 'Guyana',
                'HT' => 'Haiti',
                'HM' => 'Heard Island & Mcdonald Islands',
                'VA' => 'Holy See (Vatican City State)',
                'HN' => 'Honduras',
                'HK' => 'Hong Kong',
                'HU' => 'Hungary',
                'IS' => 'Iceland',
                'IN' => 'India',
                'ID' => 'Indonesia',
                'IR' => 'Iran, Islamic Republic Of',
                'IQ' => 'Iraq',
                'IE' => 'Ireland',
                'IM' => 'Isle Of Man',
                'IL' => 'Israel',
                'IT' => 'Italy',
                'JM' => 'Jamaica',
                'JP' => 'Japan',
                'JE' => 'Jersey',
                'JO' => 'Jordan',
                'KZ' => 'Kazakhstan',
                'KE' => 'Kenya',
                'KI' => 'Kiribati',
                'KR' => 'Korea',
                'KW' => 'Kuwait',
                'KG' => 'Kyrgyzstan',
                'LA' => 'Lao People\'s Democratic Republic',
                'LV' => 'Latvia',
                'LB' => 'Lebanon',
                'LS' => 'Lesotho',
                'LR' => 'Liberia',
                'LY' => 'Libyan Arab Jamahiriya',
                'LI' => 'Liechtenstein',
                'LT' => 'Lithuania',
                'LU' => 'Luxembourg',
                'MO' => 'Macao',
                'MK' => 'Macedonia',
                'MG' => 'Madagascar',
                'MW' => 'Malawi',
                'MY' => 'Malaysia',
                'MV' => 'Maldives',
                'ML' => 'Mali',
                'MT' => 'Malta',
                'MH' => 'Marshall Islands',
                'MQ' => 'Martinique',
                'MR' => 'Mauritania',
                'MU' => 'Mauritius',
                'YT' => 'Mayotte',
                'MX' => 'Mexico',
                'FM' => 'Micronesia, Federated States Of',
                'MD' => 'Moldova',
                'MC' => 'Monaco',
                'MN' => 'Mongolia',
                'ME' => 'Montenegro',
                'MS' => 'Montserrat',
                'MA' => 'Morocco',
                'MZ' => 'Mozambique',
                'MM' => 'Myanmar',
                'NA' => 'Namibia',
                'NR' => 'Nauru',
                'NP' => 'Nepal',
                'NL' => 'Netherlands',
                'AN' => 'Netherlands Antilles',
                'NC' => 'New Caledonia',
                'NI' => 'Nicaragua',
                'NE' => 'Niger',
                'NG' => 'Nigeria',
                'NU' => 'Niue',
                'NF' => 'Norfolk Island',
                'MP' => 'Northern Mariana Islands',
                'NO' => 'Norway',
                'OM' => 'Oman',
                'PK' => 'Pakistan',
                'PW' => 'Palau',
                'PS' => 'Palestinian Territory, Occupied',
                'PA' => 'Panama',
                'PG' => 'Papua New Guinea',
                'PY' => 'Paraguay',
                'PE' => 'Peru',
                'PH' => 'Philippines',
                'PN' => 'Pitcairn',
                'PL' => 'Poland',
                'PT' => 'Portugal',
                'PR' => 'Puerto Rico',
                'QA' => 'Qatar',
                'RE' => 'Reunion',
                'RO' => 'Romania',
                'RU' => 'Russian Federation',
                'RW' => 'Rwanda',
                'BL' => 'Saint Barthelemy',
                'SH' => 'Saint Helena',
                'KN' => 'Saint Kitts And Nevis',
                'LC' => 'Saint Lucia',
                'MF' => 'Saint Martin',
                'PM' => 'Saint Pierre And Miquelon',
                'VC' => 'Saint Vincent And Grenadines',
                'WS' => 'Samoa',
                'SM' => 'San Marino',
                'ST' => 'Sao Tome And Principe',
                'SA' => 'Saudi Arabia',
                'SN' => 'Senegal',
                'RS' => 'Serbia',
                'SC' => 'Seychelles',
                'SL' => 'Sierra Leone',
                'SG' => 'Singapore',
                'SK' => 'Slovakia',
                'SI' => 'Slovenia',
                'SB' => 'Solomon Islands',
                'SO' => 'Somalia',
                'ZA' => 'South Africa',
                'GS' => 'South Georgia And Sandwich Isl.',
                'LK' => 'Sri Lanka',
                'SD' => 'Sudan',
                'SR' => 'Suriname',
                'SJ' => 'Svalbard And Jan Mayen',
                'SZ' => 'Swaziland',
                'SE' => 'Sweden',
                'CH' => 'Switzerland',
                'SY' => 'Syrian Arab Republic',
                'TW' => 'Taiwan',
                'TJ' => 'Tajikistan',
                'TZ' => 'Tanzania',
                'TH' => 'Thailand',
                'TL' => 'Timor-Leste',
                'TG' => 'Togo',
                'TK' => 'Tokelau',
                'TO' => 'Tonga',
                'TT' => 'Trinidad And Tobago',
                'TN' => 'Tunisia',
                'TR' => 'Turkey',
                'TM' => 'Turkmenistan',
                'TC' => 'Turks And Caicos Islands',
                'TV' => 'Tuvalu',
                'UG' => 'Uganda',
                'UA' => 'Ukraine',
                'AE' => 'United Arab Emirates',
                'UM' => 'United States Outlying Islands',
                'UY' => 'Uruguay',
                'UZ' => 'Uzbekistan',
                'VU' => 'Vanuatu',
                'VE' => 'Venezuela',
                'VN' => 'Viet Nam',
                'VG' => 'Virgin Islands, British',
                'VI' => 'Virgin Islands, U.S.',
                'WF' => 'Wallis And Futuna',
                'EH' => 'Western Sahara',
                'YE' => 'Yemen',
                'ZM' => 'Zambia',
                'ZW' => 'Zimbabwe',
            );
        }

        return $countries;
    }

    public static function getNationalities()
    {
        return array(
            'American',
            'Australian',
            'Belgium',
            'British',
            'Bulgarian',
            'Chinese',
            'Croatian',
            'Cyprian',
            'Czech republican',
            'Danish',
            'Dutch',
            'Estonian',
            'Filipino',
            'Finnish',
            'French',
            'German',
            'Greek',
            'Hungarian',
            'Icelander',
            'Irish',
            'Italian',
            'Latvian',
            'Lithuanian',
            'Luxembourgian',
            'Maltese',
            'Moroccan',
            'New Zealander',
            'Norwegian',
            'Polish',
            'Portuguese',
            'Romanian',
            'Russian',
            'Slovakian',
            'Slovenian',
            'Spanish',
            'Swedish',
            'Swiss',
            'Other'
        );
    }

    public static function getDay()
    {
        $d = array();
        $d[] = 'Day';
        for ($i = 1; $i < 32; $i++) {
            $d[] = $i;
        }
        return $d;
    }

    public static function getMonth()
    {
        $months = array(
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July ',
            'August',
            'September',
            'October',
            'November',
            'December'
        );

        $m = array();
        $i = 0;
        $m[] = ':Month';
        foreach ($months as $month) {
            $i++;
            $m[] = $i . ':' . $month;
        }
        return $m;
    }

    public static function getYear()
    {
        $cy = date('Y');
        $fr = $cy - 10;
        $to = $cy - 110;
        $y = array();
        $y[] = 'Year';
        for ($i = $fr; $i > $to; $i--) {
            $y[] = $i;
        }
        return $y;
    }

    public static function space($cnt = 1)
    {
        $return = '';
        for ($i = 0; $i <= $cnt; $i++) {
            $return .= "&nbsp;";
        }
        return $return;
    }

    public static function cleanFilename($str = "")
    {
        $frm = array('/', '   ', '  ', ' ');
        $tos = array('_', '_', '_', '_');
        $output = str_replace($frm, $tos, $str);

        return $output;
    }

    public static function excerptText($txt, $limit, $sub = '...')
    {
        if (strlen($txt) > $limit)
            $return = substr(strip_tags($txt), 0, $limit) . $sub;
        else
            $return = $txt;

        return $return;
    }

    //will return orignal input value if less than the limit
    public static function excerptAsNeeded($txt, $limit, $sub = '...')
    {
        $ret = substr(strip_tags($txt), 0, $limit) . $sub;
        return strlen(trim($txt)) <= $limit ? $txt : $ret;
    }

    public static function maskText($txt, $caps = '*', $small = '*')
    {
        return preg_replace(array('#[A-Z0-9]#', '#[a-z]#'), array($caps, $small), $txt);
    }

    public static function daysPassed($startdate, $enddate)
    {

        $startTimeStamp = strtotime($startdate);
        $endTimeStamp = strtotime($enddate);

        $timeDiff = abs($endTimeStamp - $startTimeStamp);

        $numberDays = $timeDiff / 86400;  // 86400 seconds in one day

        // and you might want to convert to integer
        return intval($numberDays);
    }

    public static function getExchangeLocations()
    {
        return array(
            array("A", array(
                "Albacete",
                "Alicante/Alacant",
                "Alicante/Alacant-Elche",
                "Almería",
                "Araba/Álava",
                "Asturias-Gijón",
                "Asturias-Oviedo",
            )),
            array("Á", array(
                "Ávila",
            )),
            array("B", array(
                "Badajoz",
                "Barcelona",
                "Barcelona-Sabadell",
                "Bizkaia",
                "Burgos"
            )),
            array("C", array(
                "Cáceres",
                "Cádiz",
                "Cádiz-La Línea",
                "Cantabria",
                "Castellón/Castellò",
                "Ceuta",
                "Ciudad Real",
                "Córdoba",
                "Coruña (A)",
                "Coruña (A)-Santiago",
                "Cuenca"
            )),
            array("G", array(
                "Gipuzkoa",
                "Girona",
                "Granada",
                "Guadalajara"
            )),
            array("H", array(
                "Huelva",
                "Huesca"
            )),
            array("I", array(
                "Illes Balears-Ibiza",
                "Illes Balears-Mallorca",
                "Illes Balears-Menorca"
            )),
            array("J", array(
                "Jaén"
            )),
            array("L", array(
                "Las Palmas",
                "Las Palmas-Fuerteventura",
                "Las Palmas-Lanzarote",
                "León",
                "Lleida",
                "Lugo"
            )),
            array("M", array(
                "Madrid",
                "Madrid-Alcalá de Henares",
                "Madrid-Alcorcón",
                "Málaga",
                "Melilla",
                "Murcia",
                "Murcia-Cartagena"
            )),
            array("N", array(
                "Navarra"
            )),
            array("O", array(
                "Ourense"
            )),
            array("P", array(
                "Palencia",
                "Pontevedra",
                "Pontevedra-Vigo"
            )),
            array("R", array(
                "Rioja (La)"
            )),
            array("S", array(
                "S.C. de Tenerife",
                "S.C. de Tenerife-La Palma",
                "Salamanca",
                "Segovia",
                "Sevilla",
                "Soria"
            )),
            array("T", array(
                "Tarragona",
                "Teruel",
                "Toledo",
                "Toledo-Talavera"
            )),
            array("V", array(
                "Valencia/València",
                "Valencia/València-Alzira",
                "Valladolid"
            )),
            array("Z", array(
                "Zamora",
                "Zaragoza",
            ))
        );
    }

    public static function uniqueRandomNumbers($min, $max)
    {
        $numbers = range($min, $max);
        shuffle($numbers);
        $id = array_slice($numbers, 0, 1);
        return $id[0];
    }

    public static function newUserID()
    {
        $newID = self::uniqueRandomNumbers(100000, 999999);

        $theID = User::info('UserID', $newID);

        if ($theID == '') {
            return $newID;
        } else {
            return self::newUserID();
        }
    }

    /**
     * Get unique from specified column for a given condition
     *
     * @package
     * @access public | static
     * @param (array) $array    : (required) : data table input array
     * @param (string) $colname : (required) : column name on data table
     * @return array
     */
    public static function removeDuplicate($array, $colname)
    {
        $unique = array();
        if (isset($array) && count($array)) {
            foreach ($array as $k => $v) {
                array_push($unique, $v->$colname);
            };
        }

        return isset($unique) ? array_unique($unique) : array();
    }

    /**
     * Clean String Removes special chars
     *
     * @access public | static
     * @param (string)
     */
    public static function cleanstring($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public static function number_dbformat($n)
    {
        return str_replace(',', '', $n);
    }

    public static function getEventData($y = false)
    {
        $db     = new DB();
        $y      = $y ? $y : date('Y');
        $sql    = "SELECT * FROM `events` WHERE YEAR(`DateStart`) = '$y' LIMIT 1;";
        $data   = $db->queryOne($sql);

        return $data;
    }

    public static function getEventRange($dt, $nth)
    {
        $nth    = $nth == 0 ? 0 : $nth - 1;
        $y      = date('Y', strtotime($dt));
        $m      = date('M', strtotime($dt));
        $ds     = date('d', strtotime($dt));
        $de     = date('d', strtotime($dt . '+' . $nth . ' days'));

        return $m . ' ' . $ds . '-' . $de . ', ' . $y;
    }

    public static function calculateVat($price = 0)
    {

        $vat    = 20;
        $return = array();

        //Divisor (for our math).
        $vatDivisor = 1 + ($vat / 100);

        //Determine the price before VAT.
        $priceBeforeVat = $price / $vatDivisor;

        //Determine how much of the gross price was VAT.
        $vatAmount = $price - $priceBeforeVat;

        //Print out the price before VAT.
        $return['beforevat'] = floor($priceBeforeVat) ;

        //Print out how much of the gross price was VAT.
        $return['amountvat'] = ceil($vatAmount);

        return $return;
    }

    public static function checkVatRates($country = false)
    {

        $european = self::getEuropeanCodes();
        $return   = 0;
        if ($country) {
            if (in_array($country, $european)) {
                $return   = 0;
            } else {
                if ($country == 'GB' || $country == 'gb') {
                    $return = 20;
                }
            }
        }

        return $return;
    }

    public static function getEuropeanCodes($ret = '')
    {
        $return = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE');
        return $return;
    }


    public static function userCurrency($country = false)
    {
        $european = self::getEuropeanCodes();
        $return   = "usd";
        if ($country) {
            if ($country == 'GB' || $country == 'gb') {
                $return = "gbp";
            } elseif (in_array(strtoupper($country), $european)) {
                $return = "eur";
            }
        }

        return $return;
    }

    public static function getCurrencySymbol($code = 'GBP')
    {
        $code = strtoupper($code);
        $currency_symbols = self::getCurrencies();

        return $currency_symbols[$code];
    }

    public static function getCurrencies($desc = false)
    {
        $currencies = array(
            'EUR' => '&#8364;',
            'GBP' => '&#163;',
            'USD' => '&#36;',
            'CAD' => '&#36;',
            'AUD' => '&#36;',
            'AED' => '&#1583;.&#1573;', // ?
            'AFN' => '&#65;&#102;',
            'ALL' => '&#76;&#101;&#107;',
            'AMD' => '',
            'ANG' => '&#402;',
            'AOA' => '&#75;&#122;', // ?
            'ARS' => '&#36;',
            'AWG' => '&#402;',
            'AZN' => '&#1084;&#1072;&#1085;',
            'BAM' => '&#75;&#77;',
            'BBD' => '&#36;',
            'BDT' => '&#2547;', // ?
            'BGN' => '&#1083;&#1074;',
            'BHD' => '.&#1583;.&#1576;', // ?
            'BIF' => '&#70;&#66;&#117;', // ?
            'BMD' => '&#36;',
            'BND' => '&#36;',
            'BOB' => '&#36;&#98;',
            'BRL' => '&#82;&#36;',
            'BSD' => '&#36;',
            'BTN' => '&#78;&#117;&#46;', // ?
            'BWP' => '&#80;',
            'BYR' => '&#112;&#46;',
            'BZD' => '&#66;&#90;&#36;',
            'CDF' => '&#70;&#67;',
            'CHF' => '&#67;&#72;&#70;',
            'CLF' => '', // ?
            'CLP' => '&#36;',
            'CNY' => '&#165;',
            'COP' => '&#36;',
            'CRC' => '&#8353;',
            'CUP' => '&#8396;',
            'CVE' => '&#36;', // ?
            'CZK' => '&#75;&#269;',
            'DJF' => '&#70;&#100;&#106;', // ?
            'DKK' => '&#107;&#114;',
            'DOP' => '&#82;&#68;&#36;',
            'DZD' => '&#1583;&#1580;', // ?
            'EGP' => '&#163;',
            'ETB' => '&#66;&#114;',
            'FJD' => '&#36;',
            'FKP' => '&#163;',
            'GEL' => '&#4314;', // ?
            'GHS' => '&#162;',
            'GIP' => '&#163;',
            'GMD' => '&#68;', // ?
            'GNF' => '&#70;&#71;', // ?
            'GTQ' => '&#81;',
            'GYD' => '&#36;',
            'HKD' => '&#36;',
            'HNL' => '&#76;',
            'HRK' => '&#107;&#110;',
            'HTG' => '&#71;', // ?
            'HUF' => '&#70;&#116;',
            'IDR' => '&#82;&#112;',
            'ILS' => '&#8362;',
            'INR' => '&#8377;',
            'IQD' => '&#1593;.&#1583;', // ?
            'IRR' => '&#65020;',
            'ISK' => '&#107;&#114;',
            'JEP' => '&#163;',
            'JMD' => '&#74;&#36;',
            'JOD' => '&#74;&#68;', // ?
            'JPY' => '&#165;',
            'KES' => '&#75;&#83;&#104;', // ?
            'KGS' => '&#1083;&#1074;',
            'KHR' => '&#6107;',
            'KMF' => '&#67;&#70;', // ?
            'KPW' => '&#8361;',
            'KRW' => '&#8361;',
            'KWD' => '&#1583;.&#1603;', // ?
            'KYD' => '&#36;',
            'KZT' => '&#1083;&#1074;',
            'LAK' => '&#8365;',
            'LBP' => '&#163;',
            'LKR' => '&#8360;',
            'LRD' => '&#36;',
            'LSL' => '&#76;', // ?
            'LTL' => '&#76;&#116;',
            'LVL' => '&#76;&#115;',
            'LYD' => '&#1604;.&#1583;', // ?
            'MAD' => '&#1583;.&#1605;.', //?
            'MDL' => '&#76;',
            'MGA' => '&#65;&#114;', // ?
            'MKD' => '&#1076;&#1077;&#1085;',
            'MMK' => '&#75;',
            'MNT' => '&#8366;',
            'MOP' => '&#77;&#79;&#80;&#36;', // ?
            'MRO' => '&#85;&#77;', // ?
            'MUR' => '&#8360;', // ?
            'MVR' => '.&#1923;', // ?
            'MWK' => '&#77;&#75;',
            'MXN' => '&#36;',
            'MYR' => '&#82;&#77;',
            'MZN' => '&#77;&#84;',
            'NAD' => '&#36;',
            'NGN' => '&#8358;',
            'NIO' => '&#67;&#36;',
            'NOK' => '&#107;&#114;',
            'NPR' => '&#8360;',
            'NZD' => '&#36;',
            'OMR' => '&#65020;',
            'PAB' => '&#66;&#47;&#46;',
            'PEN' => '&#83;&#47;&#46;',
            'PGK' => '&#75;', // ?
            'PHP' => '&#8369;',
            'PKR' => '&#8360;',
            'PLN' => '&#122;&#322;',
            'PYG' => '&#71;&#115;',
            'QAR' => '&#65020;',
            'RON' => '&#108;&#101;&#105;',
            'RSD' => '&#1044;&#1080;&#1085;&#46;',
            'RUB' => '&#1088;&#1091;&#1073;',
            'RWF' => '&#1585;.&#1587;',
            'SAR' => '&#65020;',
            'SBD' => '&#36;',
            'SCR' => '&#8360;',
            'SDG' => '&#163;', // ?
            'SEK' => '&#107;&#114;',
            'SGD' => '&#36;',
            'SHP' => '&#163;',
            'SLL' => '&#76;&#101;', // ?
            'SOS' => '&#83;',
            'SRD' => '&#36;',
            'STD' => '&#68;&#98;', // ?
            'SVC' => '&#36;',
            'SYP' => '&#163;',
            'SZL' => '&#76;', // ?
            'THB' => '&#3647;',
            'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
            'TMT' => '&#109;',
            'TND' => '&#1583;.&#1578;',
            'TOP' => '&#84;&#36;',
            'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
            'TTD' => '&#36;',
            'TWD' => '&#78;&#84;&#36;',
            'TZS' => '',
            'UAH' => '&#8372;',
            'UGX' => '&#85;&#83;&#104;',
            'UYU' => '&#36;&#85;',
            'UZS' => '&#1083;&#1074;',
            'VEF' => '&#66;&#115;',
            'VND' => '&#8363;',
            'VUV' => '&#86;&#84;',
            'WST' => '&#87;&#83;&#36;',
            'XAF' => '&#70;&#67;&#70;&#65;',
            'XCD' => '&#36;',
            'XDR' => '',
            'XOF' => '',
            'XPF' => '&#70;',
            'YER' => '&#65020;',
            'ZAR' => '&#82;',
            'ZMK' => '&#90;&#75;', // ?
            'ZWL' => '&#90;&#36;',
        );

        if ($desc) {
            $currencies = array(
                'AFN' => 'Afghan Afghani',
                'ALL' => 'Albanian Lek',
                'DZD' => 'Algerian Dinar',
                'AOA' => 'Angolan Kwanza',
                'ARS' => 'Argentine Peso',
                'AMD' => 'Armenian Dram',
                'AWG' => 'Aruban Florin',
                'AUD' => 'Australian Dollar',
                'AZN' => 'Azerbaijani Manat',
                'BSD' => 'Bahamian Dollar',
                'BDT' => 'Bangladeshi Taka',
                'BBD' => 'Barbadian Dollar',
                'BZD' => 'Belize Dollar',
                'BMD' => 'Bermudian Dollar',
                'BOB' => 'Bolivian Boliviano',
                'BAM' => 'Bosnia & Herzegovina Convertible Mark',
                'BWP' => 'Botswana Pula',
                'BRL' => 'Brazilian Real',
                'GBP' => 'British Pound',
                'BND' => 'Brunei Dollar',
                'BGN' => 'Bulgarian Lev',
                'BIF' => 'Burundian Franc',
                'KHR' => 'Cambodian Riel',
                'CAD' => 'Canadian Dollar',
                'CVE' => 'Cape Verdean Escudo',
                'KYD' => 'Cayman Islands Dollar',
                'XAF' => 'Central African Cfa Franc',
                'XPF' => 'Cfp Franc',
                'CLP' => 'Chilean Peso',
                'CNY' => 'Chinese Renminbi Yuan',
                'COP' => 'Colombian Peso',
                'KMF' => 'Comorian Franc',
                'CDF' => 'Congolese Franc',
                'CRC' => 'Costa Rican Colón',
                'HRK' => 'Croatian Kuna',
                'CZK' => 'Czech Koruna',
                'DKK' => 'Danish Krone',
                'DJF' => 'Djiboutian Franc',
                'DOP' => 'Dominican Peso',
                'XCD' => 'East Caribbean Dollar',
                'EGP' => 'Egyptian Pound',
                'ETB' => 'Ethiopian Birr',
                'EUR' => 'Euro',
                'FKP' => 'Falkland Islands Pound',
                'FJD' => 'Fijian Dollar',
                'GMD' => 'Gambian Dalasi',
                'GEL' => 'Georgian Lari',
                'GIP' => 'Gibraltar Pound',
                'GTQ' => 'Guatemalan Quetzal',
                'GNF' => 'Guinean Franc',
                'GYD' => 'Guyanese Dollar',
                'HTG' => 'Haitian Gourde',
                'HNL' => 'Honduran Lempira',
                'HKD' => 'Hong Kong Dollar',
                'HUF' => 'Hungarian Forint',
                'ISK' => 'Icelandic Króna',
                'INR' => 'Indian Rupee',
                'IDR' => 'Indonesian Rupiah',
                'ILS' => 'Israeli New Sheqel',
                'JMD' => 'Jamaican Dollar',
                'JPY' => 'Japanese Yen',
                'KZT' => 'Kazakhstani Tenge',
                'KES' => 'Kenyan Shilling',
                'KGS' => 'Kyrgyzstani Som',
                'LAK' => 'Lao Kip',
                'LBP' => 'Lebanese Pound',
                'LSL' => 'Lesotho Loti',
                'LRD' => 'Liberian Dollar',
                'MOP' => 'Macanese Pataca',
                'MKD' => 'Macedonian Denar',
                'MGA' => 'Malagasy Ariary',
                'MWK' => 'Malawian Kwacha',
                'MYR' => 'Malaysian Ringgit',
                'MVR' => 'Maldivian Rufiyaa',
                'MRO' => 'Mauritanian Ouguiya',
                'MUR' => 'Mauritian Rupee',
                'MXN' => 'Mexican Peso',
                'MDL' => 'Moldovan Leu',
                'MNT' => 'Mongolian Tögrög',
                'MAD' => 'Moroccan Dirham',
                'MZN' => 'Mozambican Metical',
                'MMK' => 'Myanmar Kyat',
                'NAD' => 'Namibian Dollar',
                'NPR' => 'Nepalese Rupee',
                'ANG' => 'Netherlands Antillean Gulden',
                'TWD' => 'New Taiwan Dollar',
                'NZD' => 'New Zealand Dollar',
                'NIO' => 'Nicaraguan Córdoba',
                'NGN' => 'Nigerian Naira',
                'NOK' => 'Norwegian Krone',
                'PKR' => 'Pakistani Rupee',
                'PAB' => 'Panamanian Balboa',
                'PGK' => 'Papua New Guinean Kina',
                'PYG' => 'Paraguayan Guaraní',
                'PEN' => 'Peruvian Nuevo Sol',
                'PHP' => 'Philippine Peso',
                'PLN' => 'Polish Złoty',
                'QAR' => 'Qatari Riyal',
                'RON' => 'Romanian Leu',
                'RUB' => 'Russian Ruble',
                'RWF' => 'Rwandan Franc',
                'STD' => 'São Tomé and Príncipe Dobra',
                'SHP' => 'Saint Helenian Pound',
                'SVC' => 'Salvadoran Colón',
                'WST' => 'Samoan Tala',
                'SAR' => 'Saudi Riyal',
                'RSD' => 'Serbian Dinar',
                'SCR' => 'Seychellois Rupee',
                'SLL' => 'Sierra Leonean Leone',
                'SGD' => 'Singapore Dollar',
                'SBD' => 'Solomon Islands Dollar',
                'SOS' => 'Somali Shilling',
                'ZAR' => 'South African Rand',
                'KRW' => 'South Korean Won',
                'LKR' => 'Sri Lankan Rupee',
                'SRD' => 'Surinamese Dollar',
                'SZL' => 'Swazi Lilangeni',
                'SEK' => 'Swedish Krona',
                'CHF' => 'Swiss Franc',
                'TJS' => 'Tajikistani Somoni',
                'TZS' => 'Tanzanian Shilling',
                'THB' => 'Thai Baht',
                'TOP' => 'Tongan Paʻanga',
                'TTD' => 'Trinidad and Tobago Dollar',
                'TRY' => 'Turkish Lira',
                'UGX' => 'Ugandan Shilling',
                'UAH' => 'Ukrainian Hryvnia',
                'AED' => 'United Arab Emirates Dirham',
                'USD' => 'United States Dollar',
                'UYU' => 'Uruguayan Peso',
                'UZS' => 'Uzbekistani Som',
                'VUV' => 'Vanuatu Vatu',
                'VND' => 'Vietnamese Đồng',
                'XOF' => 'West African Cfa Franc',
                'YER' => 'Yemeni Rial',
                'ZMW' => 'Zambian Kwacha'
            );
        }

        return $currencies;
    }

    public static function getEuropenaCountries()
    {
        return array(
            "AT" => "Austria",
            "BE" => "Belgium",
            "BG" => "Bulgaria",
            "HR" => "Croatia",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DK" => "Denmark",
            "EE" => "Estonia",
            "FI" => "Finland",
            "FR" => "France",
            "DE" => "Germany",
            "GR" => "Greece",
            "HU" => "Hungary",
            "IE" => "Ireland",
            "IT" => "Italy",
            "LV" => "Latvia",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "MT" => "Malta",
            "NL" => "Netherlands",
            "PL" => "Poland",
            "PT" => "Portugal",
            "RO" => "Romania",
            "SK" => "Slovakia",
            "SI" => "Slovenia",
            "ES" => "Spain",
            "SE" => "Sweden",
            "GB" => "United Kingdom"
        );
    }

    public static function getIPNumber($user_ip)
    {

        $pcs = explode('.', $user_ip);
        $set1 = $pcs[0] * (256 * 256 * 256);
        $set2 = $pcs[1] * (256 * 256);
        $set3 = $pcs[2] * 256;

        $set4 = (isset($pcs[3])) ? $pcs[3] : 0;

        $ipNumber = $set1 + $set2 + $set3 + $set4;

        return $ipNumber;
    }

    public static function getUserCountry($user_ip)
    {
        $db       = new DB();
        $ipNumber = self::getIPNumber($user_ip);
        $sql      = "SELECT countrySHORT, countryLong
                     FROM ipcountry
                     WHERE ipFROM <= " . $ipNumber . " AND ipTO >= " . $ipNumber;

        $country = $db->queryOne($sql);

        if (!$country) {

            $sql2 = "SELECT countryCode, countryName
                     FROM geoip
                     WHERE beginIpNum <= " . $ipNumber . " AND endIpNum >= " . $ipNumber;

            $country2 = $db->queryOne($sql2);
            $countryShortName = $country2->countryCode;
        } else {
            $countryShortName = $country->countrySHORT;
        }

        return $countryShortName;
    }

    public static function getUserCountryLong($user_ip)
    {
        $db       = new DB();
        $ipNumber = self::getIPNumber($user_ip);
        $sql      = "SELECT countrySHORT, countryLong
                     FROM ipcountry
                     WHERE ipFROM <= " . $ipNumber . " AND ipTO >= " . $ipNumber;

        $country = $db->queryOne($sql);

        if (!$country) {
            $sql2 = "SELECT countryCode, countryName
                     FROM geoip
                     WHERE beginIpNum <= " . $ipNumber . " AND endIpNum >= " . $ipNumber;

            $country2 = $db->queryOne($sql2);

            $countryLongName = $country2->countryName;
        } else {
            $countryLongName = $country->countryLong;
        }

        return $countryLongName;
    }

    public static function getFileSize($file)
    {
        $bytes = filesize($file);

        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

    public static function price($amount, $currencySymbol = "", $nofloat = false)
    {
        if($nofloat) {
            $amount = $amount / 100;
        }

        $amt = number_format($amount,2);

        return $currencySymbol . str_replace('.00','', (string) $amt);
    }

    public static function shortStr($filename, $limit = '30', $pre = '...')
    {
        return strlen($filename) > $limit ? $pre . substr($filename, -$limit) : $filename;
    }

    public static function parseFilename($filename)
    {
        $fArr = explode('_', $filename, 5);
        return isset($fArr[4]) ? $fArr[4] : $filename;
    }

    public static function imageExtensions()
    {
        return ['png','jpeg','jpg','ico','gif'];
    }

    public static function haveChatAlert()
    {
        $UserID     = User::info('UserID');
        $latestChat = App::load()->model('chats', true)->getClientLatest($UserID);
        
        return (($latestChat) && $latestChat->ReplyToID == $UserID && $latestChat->UserID != $UserID && $latestChat->Seen == 0) ? true : false;
    }

    public static function doCurl($url)
    {
        $curl = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/html'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        $response = curl_exec($ch);

        if(curl_error($ch)){
            return 'Request Error:' . curl_error($ch);
        }
        else
        {
            return $response;
        }

        curl_close($ch);
    }

    public static function getBlockListed()
    {
        $bld    = array();
        $string = trim(preg_replace('/\s+/', ' ', Option::get('block_listed_domains')));
        $blocks = str_replace(" ", "", $string);
        $bld    = explode(',', $blocks);
        return $bld;
    }

    public static function showAdvancedWork($ProjectID = false)
    {
        if($ProjectID) {
            $projectModel      = App::load()->model('projects', true);
            $revisionModel     = App::load()->model('revisions', true);
            $notificationModel = App::load()->model('notifications', true);
            $fileitemModel     = App::load()->model('fileitems', true);

            $projectInfo       = $projectModel->getOne($ProjectID);            
            $completedFileID   = $projectInfo->CompletedFileID;

            // update project
            $revisionModel->doShowAdvanceWork($ProjectID);
            $notificationModel->doShowAdvanceWork($ProjectID);
            $fileitemModel->doShowAdvanceWork($completedFileID);
        }
    }

    public static function normalizeCamel($str)
    {
        return implode(" ",array_filter(preg_split('/(?=[A-Z])/',$str)));
    }
}
