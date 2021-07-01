<?php

/**
 * Application Utility Object
 *
 * @category   Utility
 * @package    AppUtility
 * @author     Mo <moises.goloyugo@gmail.com>
 * @copyright  (c) 2020 Motility
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.0.0
 * @link       http://
 * @since      Class available since Release 1.2.0
 */
class AppUtility
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

        $query = &$db->prepare($sql);
        $query->execute();
        $data = array();
        while ($row = $query->fetch(PDO::FETCH_CLASS)) {

            $data[$row->LanguageCode] = $row->LanguageName;
        }
        unset($query);

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

    public static function parseAuditProject($pAudit)
    {
        $cChart = ($pAudit) ? count($pAudit) : 0;
        $pAudit = json_decode(json_encode($pAudit), true);

        $task   = [];
        $cc_sub = [];
        $labels = [];

        foreach ($pAudit as $key => $aud) {
            $aud['ActivityDescription'] = strip_tags($aud['ActivityDescription']);

            if (isset($arr[$key + 1])) {
                $ee = $pAudit[$key + 1]['ActivityDate'];
            } else {
                $ee_d = new DateTime($aud['ActivityDate']);
                $ee_d->modify('+1 day');
                $ee_d = $ee_d->format('Y-m-d H:i:s');
                $ee = $ee_d;
            }

            $ee = isset($pAudit[$key + 1]['ActivityDate']) ? $pAudit[$key + 1]['ActivityDate'] : '';

            if ($key == $cChart - 1) {
                $ee_d = new DateTime($aud['ActivityDate']);
                $ee_d->modify('+1 day');
                $ee_d = $ee_d->format('Y-m-d H:i:s');
                $ee = $ee_d;
            }

            $task[] = ['start' => $aud['ActivityDate'], 'end' => $ee, 'label' => $aud['ActivityDescription']];
            $cc_sub[] = ['start' => $aud['ActivityDate'], 'end' => $ee, 'label' => $aud['ActivityDescription']];
            $labels[] = ['label' => $aud['ActivityStatus']];
        }

        $cat_Start = isset($cc_sub[0]) ? $cc_sub[0]['start'] : '';
        $cat_End   = isset($cc_sub[$cChart - 1]) ? $cc_sub[$cChart - 1]['end'] : 0;

        $cats[]    = ['start' => $cat_Start, 'end' => $cat_End, 'label' => 'Process'];

        $lbls      = json_encode($labels);
        $cat       = json_encode($cats);
        $ts        = json_encode($task);
        $c2        = json_encode($cc_sub);

        $data = array(
            'lbs' => $lbls,
            'cat'  => $cat,
            'ts'  => $ts,
            'c2'  => $c2
        );

        return $data;
    }

    public static function getSalutation()
    {
        return array("" => "Select Prefix", "Mr" => "Mr", "Mrs" => "Mrs", "Miss" => "Miss", "Ms" => "Ms", "Dr" => "Dr", "Prof" => "Prof", "Rev" => "Rev");
    }

    public static function getGender()
    {
        return array("M" => "Male", "F" => "Female");
    }

    public static function getMaritalStatus()
    {
        return array("Single" => "Single", "Married" => "Married", "Separated" => "Separated", "Divorced" => "Divorced", "Widower" => "Widower", "Widow" => "Widow");
    }

    public static function getNIEReasons()
    {
        return array("Economical" => "Economical", "Professional" => "Professional", "Social" => "Social");
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

    public static function getChildrenRecursive($ID)
    {
        $db = new DB();
        $sql = "SELECT u.UserID, u.Level, u.ReferrerUserID, um.FirstName, um.LastName, ul.Code, a.ApplicationDate, a.Commission, (SELECT SUM(aa.DepositedAmount) FROM accounts aa LEFT JOIN users uu ON aa.UserID = uu.UserID WHERE uu.ReferrerUserID = u.UserID) as Total FROM users u LEFT JOIN user_meta um ON u.UserID = um.UserID LEFT JOIN user_levels ul ON u.Level = ul.RoleID LEFT JOIN accounts a ON a.UserID = u.UserID WHERE u.Active = 1 AND u.Level IN(2,3,4) AND u.ReferrerUserID = $ID";
        $query = &$db->prepare($sql);
        $query->execute();
        $data = array();
        while ($row = $query->fetch(PDO::FETCH_CLASS)) {
            $data[$row->UserID] = $row;
        }
        unset($query);

        return $data;
    }

    static public function getAgencyOf($refID = false)
    {
        $db = new DB();
        if ($refID) {
            $sql = "SELECT GetAgency(UserID) as child FROM users WHERE UserID = " . $refID;

            $data = $db->get_row($sql);
            $ret = false;
            if ($data) {
                $ret = $data->child;
            }
            return $ret;
        } else {
            return false;
        }
    }

    static public function getGroupOf($refID = false)
    {
        $db = new DB();
        if ($refID) {
            $sql = "SELECT GetAgents(UserID) as child FROM users WHERE UserID = " . $refID;
            $data = $db->get_row($sql);
            $ret = $refID;
            if ($data) {
                $ret = $refID . ',' . $data->child;
            }
            return $ret;
        } else {
            return false;
        }
    }

    static public function getAgents($refID = false)
    {
        $db = new DB();
        if ($refID) {
            $sql = "SELECT GetAgents(UserID) as child FROM users WHERE UserID = " . $refID;
            $data = $db->get_row($sql);
            $ret = $refID;
            if ($data) {
                $ret = $data->child;
            }
            return $ret;
        } else {
            return false;
        }
    }

    static public function getParent($userID = false)
    {
        $db = new DB();
        if ($refID) {
            $sql = "SELECT UserID,ReferrerUserID FROM users WHERE UserID = " . $userID;
            $data = $db->get_row($sql);
            return $data;
        } else {
            return false;
        }
    }

    static public function getParentOf($refID = false)
    {
        $db = new DB();
        if ($refID) {
            $sql = "SELECT GetParent(UserID) as parent FROM users WHERE UserID = " . $refID;

            $data = $db->get_row($sql);
            $ret = false;
            if ($data) {
                $ret = $data->parent;
            }
            return $ret;
        } else {
            return false;
        }
    }

    static public function getChildrenOf($refID = false)
    {
        $db = new DB();
        if ($refID) {
            $sql = "SELECT GetChildrenByID(UserID) as child FROM users WHERE UserID = " . $refID;
            $data = $db->get_row($sql);
            return $data->child;
        } else {
            return false;
        }
    }

    public static function getChildren($ID)
    {
        $data = array();
        $childs = self::getChildrenRecursive($ID);

        foreach ($childs as $child) {
            $data[$child->UserID]['data'] = $child;
            $data[$child->UserID]['children'] = self::getChildren($child->UserID);
        }

        $idArr = self::getChildrenIds($ID);

        return implode(',', (array)$idArr);
    }

    public static function getChildrensData($ID)
    {
        $data = array();
        $childs = self::getChildrenRecursive($ID);

        foreach ($childs as $child) {
            $data[$child->UserID]['data'] = $child;
            $data[$child->UserID]['children'] = self::getChildren($child->UserID);
        }

        return (count($data)) ? $data : '';
    }

    public static function getChildrenIds($ID)
    {
        $data = array();
        $childs = self::getChildrenRecursive($ID);

        foreach ($childs as $child) {
            $ch = self::getChildrenIds($child->UserID);
            $data[] = $child->UserID;
            if (is_array($ch)) {
                $data[] = implode(',', $ch);
            }
        }

        return (count($data)) ? $data : '';
    }

    public static function getChildrenIdArray($ID)
    {
        $data = array();
        $childs = self::getChildrenRecursive($ID);

        foreach ($childs as $child) {
            $data[$child->UserID] = self::getChildrenIdArray($child->UserID);
        }

        return (count($data)) ? $data : '';
    }

    public static function getClientCategories()
    {
        $db = new DB();
        $sql = "SELECT cc.*
            FROM client_categories cc";
        $query = &$db->prepare($sql);
        $query->execute();
        $data = array();
        while ($row = $query->fetch(PDO::FETCH_CLASS)) {
            $data[] = $row;
        }
        unset($query);

        return $data;
    }

    public static function getAgencyInfo($ID = false)
    {
        $data = false;
        $db = new DB();
        if ($ID) {
            $sql = "SELECT GroupLanguage, GroupProducts FROM accounts WHERE UserID = $ID LIMIT 1";
            $data = $db->get_row($sql);
        }

        return $data;
    }

    public static function outputFileLinks($thefs, $user, $sfv = '', $v = '', $showupload = false)
    {
        $status = array(
            '<h6 class="text-warning push-5-t"><b>PENDING</b></h6>',
            '<h6 class="text-success push-5-t"><b>APPROVED</b></h6>',
            '<h6 class="text-danger push-5-t"><b>REJECTED</b></h6>'
        );

        $sfvout = strlen($sfv) > 0 ? $sfv . ': ' : '';

        $output = '';
        foreach ($thefs as $bf) {
            $ext = pathinfo($bf->FileSlug, PATHINFO_EXTENSION);
            switch ($ext) {
                case 'pdf':
                    $fileSlugUrl = view::asset("images/pdf.png");
                    break;
                case 'docx':
                    $fileSlugUrl = view::asset("images/word.jpg");
                    break;
                case 'doc':
                    $fileSlugUrl = view::asset("images/word.jpg");
                    break;
                default:
                    $fileSlugUrl = View::asset('files' . $bf->FileSlug);
                    break;
            }
            $output .= '<li class="list-group-item">';
            $output .= '<div class="row">';
            $output .= '<div class="col-md-12 col-sm-12 col-xs-12">';
            $output .= '<div class="row">';
            $output .= '<div class="col-md-2 col-sm-12 col-xs-12">';
            $output .= $status[$bf->Active];
            $output .= '</div>';
            $output .= '<div class="col-md-6 col-sm-12 col-xs-12">';
            $output .= '<h5><a href="' . View::url(($bf) ? 'assets/files' . $bf->FileSlug : '#') . '" class="html5lightbox" data-group="set1" data-thumbnail="' . $fileSlugUrl . '" title="' . $sfvout .  $bf->FileDescription . '">' . $sfvout .  $bf->FileName . '<br><small> ' . date("Y-m-d - g:iA", strtotime($bf->DateAdded)) . '</small></a></h5>';
            $output .= '</div>';

            if (User::is('Administrator') || User::can('Manage Uploaded Documents')) {
                $output .= '<div class="col-md-4 actions-edit col-sm-12 col-xs-12">';
                $output .= '<div class="cf-actions">';
                $output .= '<a href="' . View::url(($bf) ? 'assets/files' . $bf->FileSlug : '#') . '" class="mo-icon text-info" data-toggle="tooltip" title="Download" download><i class="si si-arrow-down"></i></a>&nbsp;';
                $output .= '<a href="' . View::url('bookings/deletefile/' . $bf->FileItemID . '/' . $user->InvestmentBookingID) . '/" data-toggle="tooltip" title="Delete" class="mo-icon text-danger" onclick="return confirm(\'Are you sure you want to delete this file?\');"><i class="si si-trash"></i></a>&nbsp;';
                $output .= '<a href="' . View::url('bookings/rejectfile/' . $bf->FileItemID . '/' . $user->InvestmentBookingID) . '/" data-toggle="tooltip" title="Reject" rel="' . $bf->FileName . '" file-name="' . $v . '" class="mo-icon text-warning rejectuploadedfile"><i class="si si-dislike"></i></a>&nbsp;';
                $output .= '<a href="' . View::url('bookings/approvefile/' . $bf->FileItemID . '/' . $user->InvestmentBookingID) . '/" data-toggle="tooltip" title="Approved" class="mo-icon text-success"><i class="si si-like"></i></a>';
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            if ($showupload) {
                $output .= '<br><hr><br><input id="file-0a" class="file form-control" type="file" data-min-file-count="0" name="' . $bf->FileDescription . '" data-show-upload="false" data-allowed-file-extensions=\'["pdf","jpeg","png","jpg"]\'><span class="text-muted">Allowed file types: pdf, jpeg, jpg, png</span>';
            }
            $output .= '</li>';
        }

        return $output;
    }

    public static function outputFileNoData($k = '', $showupload = false, $close = false, $remove = false)
    {
        $nodata = '';
        $nodata = '<li class="list-group-item">';
        $nodata .= '<div class="row">';
        $nodata .= '<div class="col-md-12">';
        $nodata .= '<div class="row">';
        $nodata .= '<div class="col-md-12 col-sm-12 col-xs-12 no-file">';
        //$nodata .= '<span class="font-10 red bold">NO UPLOADED FILE</span>';
        $nodata .= '<div class="col-lg-6"><span class="font-10 red bold">NO UPLOADED FILE</span></div>';
        $nodata .= '<div class="col-lg-6"><div class="close remove-form hidden" data-id="' . $k . '" title="Remove this form!"><i class="fa fa-trash"></i> REMOVE</div></div>';
        $nodata .= '</div>';
        $nodata .= '<div class="col-md-12 col-sm-12 col-xs-12"></div>';
        $nodata .= '<div class="col-md-12 actions-edit col-sm-12 col-xs-12">';
        $nodata .= '<div class="cf-actions"></div>';
        $nodata .= '</div>';
        $nodata .= '</div>';
        $nodata .= '</div>';
        $nodata .= '</div>';
        if ($showupload) {
            $nodata .= '<br><hr><br><input id="file-0a" class="file form-control file-input" type="file" data-min-file-count="0" name="' . $k . '" data-show-upload="false" data-allowed-file-extensions=\'["pdf","jpeg","png","jpg"]\'><span class="text-muted">Allowed file types: pdf, jpeg, jpg, png</span> | <span class="text-muted">Allowed maximum file size: 8 MB</span>';
        }
        $nodata .= '</li>';

        return $nodata;
    }

    public static function outputFileList($fileArray, $user, $showupload = false)
    {
        $output = '';
        $output .= '<div class="form-group">';
        foreach ($fileArray as $k => $v) {
            if (is_array($v)) {
                $output .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="fname">' . $v[0] . '</label>';
                $output .= '<div class="col-md-8 col-sm-8 col-xs-12">';
                $output .= '<ul class="list-group cf-filelist">';

                foreach ($v[1] as $sfk => $sfv) {
                    if ($user->$sfk) {
                        $fc = 1;
                        $thefs = App::getUploadedFiles($user->$sfk);

                        $output .= '<ul class="list-group cf-filelist">';
                        $output .= self::outputFileLinks($thefs, $user, $sfv, $v[0], $showupload);
                        $output .= '</ul>';
                    } else {
                        $output .= '<ul class="list-group cf-filelist">';
                        $output .= self::outputFileNoData($k, $showupload);
                        $output .= '</ul>';
                    }
                }

                $output .= '</div><div class="clearfix"></div>';
            } else {
                $output .= '<label>' . $v . '</label>';
                $output .= '<ul class="list-group cf-filelist">';

                if ($user->$k) {
                    $fc = 1;
                    $thefs = App::getUploadedFiles($user->$k);
                    if (count($thefs)) {
                        $output .= self::outputFileLinks($thefs, $user, '', $v, $showupload);
                    } else {
                        $output .= self::outputFileNoData($k, $showupload);
                    }
                } else {
                    $output .= self::outputFileNoData($k, $showupload);
                }

                $output .= '</ul>';

                $output .= '<div class="clearfix"></div>';
            }
        }
        $output .= '</div>';

        return $output;
    }

    public static function outputCustomFileList($fileArray, $user, $showupload = false, $remove = false)
    {
        $docname = isset($user->DocumentName) ? $user->DocumentName : '';
        $disabled = isset($user->Disabled) ? $user->Disabled : '';
        // $dnf = explode( '-', $fileArray );
        foreach ($fileArray as $v) {
            $dn = explode('-', $v);
            $output = '<div id="' . $dn[1] . '" class="form-group created-form">';
            $output .= '<input type="hidden" name="docNames[]" value="' . $dn[0] . '|' . $docname . '" id="DN-' . $dn[1] . '"/>';
            $output .= '<div class="remove-ib push-5">';
            $output .= '<label>document name *</label> <b><div class="doc-name"><input type="text" value="' . $docname . '" name="DN-' . $dn[1] . '" class="docs-input ' . $disabled . '" data-placeholder="write document name here"/></div></b>';
            $output .= '</div>';
            $output .= '<ul class="list-group cf-filelist">';
            if (isset($user->FileID) && $user->FileID) {
                $fc = 1;
                $thefs = App::getUploadedFiles($user->FileID);
                if (count($thefs)) {
                    $output .= self::outputUserFileLinks($thefs, $user, '', $dn[1], $showupload);
                } else {
                    $output .= self::outputFileNoData($dn[1], $showupload, $remove);
                }
            } else {
                $output .= self::outputFileNoData($dn[1], $showupload, $remove);
            }
            $output .= '</ul>';
            $output .= '<div class="clearfix"></div>';
            $output .= '</div>';
        }

        return $output;
    }

    public static function outputFileRejectPopup()
    {
        $output = '';

        $output .= '<div class="rejectfileform"><div class="rejectfileform_wrap">';
        $output .= '<form method="post" action="" id="rejectfile">';
        $output .= '<input type="hidden" name="reject[filename]" value="" id="filen">';
        $output .= '<h3>Reject File</h3>';
        $output .= '<p>Rejecting file <span class="rfilename"></span>, please put a reason and note below:</p>';
        $output .= '<div class="form-group">';
        $output .= '<label class="control-label col-md-3 col-sm-3 col-xs-12">Select Reason</label>';
        $output .= '<div class="col-md-8 col-sm-8 col-xs-12">';
        $output .= '<label><input type="checkbox" name="reject[reasons][]" value="Not Clear"> Not Clear</label>';
        $output .= '<label><input type="checkbox" name="reject[reasons][]" value="Not Readable"> Not Readable</label>';
        $output .= '</div>';
        $output .= '</div><div class="clearfix"></div>';
        $output .= '<div class="form-group">';
        $output .= '<label class="control-label col-md-3 col-sm-3 col-xs-12">Notes</label>';
        $output .= '<div class="col-md-8 col-sm-8 col-xs-12">';
        $output .= '<textarea style="height:120px;" name="reject[notes]" class="form-control"></textarea>';
        $output .= '</div>';
        $output .= '</div><div class="clearfix"></div>';
        $output .= '<div class="form-group">';
        $output .= '<label class="control-label col-md-3 col-sm-3 col-xs-12"></label>';
        $output .= '<div class="col-md-8 col-sm-8 col-xs-12">';
        $output .= '<input class="btn btn-danger" type="submit" value="Reject">&nbsp;&nbsp;';
        $output .= '<a href="#" class="btn btn-warning close_rejectfileform">Cancel</a>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</form>';
        $output .= '</div></div>';

        return $output;
    }

    public static function getFileList($user, $showupload = false)
    {
        $fileArray = array(
            'IdPhoto' => Lang::get('USR_PRF_UPLGOVID'),
            'AddressPhoto' => Lang::get('USR_PRF_UPLADDRPRF')
        );

        return self::outputFileList($fileArray, $user, $showupload);
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

    /**
     * Ger user data
     *
     * @package
     * @access public | static
     * @param (int) $ID        : (required)                : User ID
     * @param (boolean) $fetch : (optional) default[false] : type of extracting data
     * @return object
     **/
    public static function getUser($ID, $fetch = false)
    {
        $db = new DB();
        $sql = "SELECT u.*, um.*, ul.`Name`, ul.`Code`,
                ( SELECT CONCAT( uul.`Code`, uu.`UserID` ) as FirstAgentID FROM `users` uu LEFT JOIN `user_levels` uul ON uu.`Level` = uul.`RoleID` WHERE uu.`UserID` = u.`ReferrerUserID` ) as AgentID
            FROM `users` u
            LEFT JOIN `user_meta` um ON um.`UserID` = u.`UserID`
            LEFT JOIN `user_levels` ul ON u.`Level` = ul.`RoleID`
            WHERE u.`UserID` = " . $ID . " LIMIT 1";

        if ($fetch) {
            $query = $db->prepare($sql);
            $query->execute();
            $data[] = $query->fetch(PDO::FETCH_CLASS);
            unset($query);
        } else {
            $data = $db->get_row($sql);
        }

        return $data;
    }

    /**
     * Get user profile row data by User ID
     *
     * @access public | static
     * @param (int) $userID : (required) : specified user id
     * @return object
     */
    public static function getUserProfileInfo($userID)
    {
        $return = false;
        $db = new DB();
        if ($userID) {
            $sql = "SELECT a.IdPhoto,a.AddressPhoto,um.FirstName,um.LastName,u.Email,um.Phone,um.Address,um.City,um.State,um.Country,um.PostalCode FROM users u LEFT JOIN user_meta um ON u.UserID = um.UserID LEFT JOIN accounts a ON u.UserID = a.UserID WHERE u.UserID = '" . $userID . "' LIMIT 1";

            $return = $db->get_row($sql);
        }
        return $return;
    }

    public static function fileHasFileItem($id)
    {
        $return = false;
        $db = new DB();
        if ($id) {
            $sql = "SELECT FileItemID FROM `file_items` WHERE Active = 1 AND FileID = '" . $id . "' LIMIT 1";
            $ret = $db->get_row($sql);
            $return = isset($ret->FileItemID) ? true : false;
        }
        return $return;
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

    public static function getSpainLocations()
    {
        return array(
            array(
                "regionName" => "Andalucía",
                "regionShort" => "AN",
                "regionSlug" => "andalucia",
                "weight" => 1788,
                "cities" => array(
                    "Almería",
                    "Cádiz",
                    "Córdoba",
                    "Granada",
                    "Sevilla",
                    "Huelva",
                    "Jaén",
                    "Málaga",
                    "Jerez de la Frontera",
                    "Marbella",
                    "Dos Hermanas",
                    "Algeciras"
                )
            ),
            array(
                "regionName" => "Aragón",
                "regionShort" => "AR",
                "regionSlug" => "aragon",
                "weight" => 286,
                "cities" => array(
                    "Huesca",
                    "Teruel",
                    "Zaragoza"
                )
            ),
            array(
                "regionName" => "Principado de Asturias",
                "regionShort" => "AS",
                "regionSlug" => "asturias",
                "weight" => 228,
                "cities" => array(
                    "Oviedo",
                    "Gijón"
                )
            ),
            array(
                "regionName" => "Cantabria",
                "regionShort" => "CA",
                "regionSlug" => "cantabria",
                "weight" => 126,
                "cities" => array(
                    "Santander"
                )
            ),
            array(
                "regionName" => "Castilla - La Mancha",
                "regionShort" => "CM",
                "regionSlug" => "clm",
                "weight" => 449,
                "cities" => array(
                    "Ciudad Real",
                    "Albacete",
                    "Cuenca",
                    "Toledo",
                    "Guadalajara"
                )
            ),
            array(
                "regionName" => "Castilla y León",
                "regionShort" => "CL",
                "regionSlug" => "cle",
                "weight" => 539,
                "cities" => array(
                    "Burgos",
                    "León",
                    "Palencia",
                    "Valladolid",
                    "Zamora",
                    "Ávila",
                    "Salamanca",
                    "Segovia",
                    "Soria"
                )
            ),
            array(
                "regionName" => "Catalunya",
                "regionShort" => "CA",
                "regionSlug" => "cataluña",
                "weight" => 1602,
                "cities" => array(
                    "Barcelona",
                    "Tarragona",
                    "Girona",
                    "Lleida",
                    "L'Hospitalet de Llobregat",
                    "Badalona",
                    "Tarrasa",
                    "Sabadell",
                    "Mataró",
                    "Santa Coloma de Gramenet",
                    "Reus"
                )
            ),
            array(
                "regionName" => "Ceuta",
                "regionShort" => "CE",
                "regionSlug" => "ceuta",
                "weight" => 18,
                "cities" => array(
                    "Ceuta"
                )
            ),
            array(
                "regionName" => "Comunitat Valenciana",
                "regionShort" => "CV",
                "regionSlug" => "valencia",
                "weight" => 1085,
                "cities" => array(
                    "Castelló",
                    "Valéncia",
                    "Alacant",
                    "Elx",
                    "Torrevieja"
                )
            ),
            array(
                "regionName" => "Canarias",
                "regionShort" => "CN",
                "regionSlug" => "canarias",
                "weight" => 448,
                "cities" => array(
                    "Santa Cruz de Tenerife",
                    "Las Palmas",
                    "San Cristóbal de la Laguna",
                    "Telde"
                )
            ),
            array(
                "regionName" => "Illes Balears",
                "regionShort" => "BA",
                "regionSlug" => "baleares",
                "weight" => 237,
                "cities" => array(
                    "Palma de Mallorca"
                )
            ),
            array(
                "regionName" => "Extremadura",
                "regionShort" => "EX",
                "regionSlug" => "extremadura",
                "weight" => 234,
                "cities" => array(
                    "Badajoz",
                    "Cáceres"
                )
            ),
            array(
                "regionName" => "Galicia",
                "regionShort" => "GA",
                "regionSlug" => "galicia",
                "weight" => 588,
                "cities" => array(
                    "A Coruña",
                    "Ourense",
                    "Lugo",
                    "Pontevedra",
                    "Vigo"
                )
            ),
            array(
                "regionName" => "Madrid",
                "regionShort" => "MA",
                "regionSlug" => "madrid",
                "weight" => 1375,
                "cities" => array(
                    "Madrid",
                    "Móstoles",
                    "Alcalá de Henares",
                    "Fuenlabrada",
                    "Leganés",
                    "Getafe",
                    "Alcorcón",
                    "Torrejón de Ardoz",
                    "Parla",
                    "Alcobendas"
                )
            ),
            array(
                "regionName" => "Melilla",
                "regionShort" => "ME",
                "regionSlug" => "melilla",
                "weight" => 17,
                "cities" => array(
                    "Melilla"
                )
            ),
            array(
                "regionName" => "Murcia",
                "regionShort" => "MU",
                "regionSlug" => "murcia",
                "weight" => 312,
                "cities" => array(
                    "Murcia",
                    "Cartagena"
                )
            ),
            array(
                "regionName" => "Navarra",
                "regionShort" => "NA",
                "regionSlug" => "navarra",
                "weight" => 136,
                "cities" => array(
                    "Pamplona"
                )
            ),
            array(
                "regionName" => "Euskadi",
                "regionShort" => "PV",
                "regionSlug" => "paisvasco",
                "weight" => 464,
                "cities" => array(
                    "Bilbo",
                    "Donosti",
                    "Gasteiz",
                    "Baracaldo"
                )
            ),
            array(
                "regionName" => "La Rioja",
                "regionShort" => "LR",
                "regionSlug" => "larioja",
                "weight" => 68,
                "cities" => array(
                    "Logroño"
                )
            )
        );
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

    public static function getOptionProducts($sel = '-- select --')
    {
        $sql    = "SELECT DISTINCT * FROM `products` WHERE Active=1 ORDER BY `ProductName`;";
        $db     = new DB();
        $data   = array('0' => $sel);
        $query  = $db->prepare($sql);
        $query->execute();

        while ($row = $query->fetch(PDO::FETCH_CLASS)) {
            $data[$row->ProductID] = $row->ProductName . ' - ' . $row->ProductDescription . ' | ' . PESO . number_format($row->ProductPrice, 2);
        }
        unset($query);

        return $data;
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
        $data   = $db->get_row($sql);

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

    public static function getFilePathBodyClass($path)
    {
        $data = explode('views', $path);

        $filtered = str_replace(array('/', '\\'), array('/', '/'), $data[1]);
        $needle = '/';
        $newstring = $filtered;
        $pos = strpos($filtered, $needle);
        if ($pos !== false) {
            $newstring = substr_replace($filtered, '', $pos, strlen($needle));
        }
        return $newstring;
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

        $country = $db->get_row($sql);

        if (!$country) {

            $sql2 = "SELECT countryCode, countryName
                     FROM geoip
                     WHERE beginIpNum <= " . $ipNumber . " AND endIpNum >= " . $ipNumber;

            $country2 = $db->get_row($sql2);
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

        $country = $db->get_row($sql);

        if (!$country) {
            $sql2 = "SELECT countryCode, countryName
                     FROM geoip
                     WHERE beginIpNum <= " . $ipNumber . " AND endIpNum >= " . $ipNumber;

            $country2 = $db->get_row($sql2);

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

    public static function projectStatus($status)
    {
        $return = '';
        switch ($status) {
            case "NEW":
                $return = "bg-orange-300";
                break;
            case "IN PROGRESS":
                $return = "bg-indigo-300";
                break;
            case "ON HOLD":
                $return = "bg-danger";
                break;
            case "UPDATED":
                $return = "bg-blue-300";
                break;
            case "DONE":
                $return = "bg-teal-300";
                break;
            case "COMPLETED":
                $return = "bg-success";
                break;
        }

        return $return;
    }

    public static function getInitialVideoKey()
    {
        $return = array(
            'Frontpage Promo',
            'Personal and No-Brand',
            'Request Name',
            'Category',
            'Sizes',
            'Design Target Audience',
            'Sample Design URL',
            'Design Request Text'
        );

        return $return;
    }

    public static function getInitialVideoCurrency()
    {
        $return = array(
            "usd" => "General",
            "gbp" => "United Kingdom",
            "eur" => "European Countries",
            "aud" => "Australia",
            "nzd" => "New Zealand"
        );

        return $return;
    }

    public static function getNIEInfo($NIE)
    {
        $endpoint   = 'validate';
        $access_key = '5dc3b7077d9a5c964da8448b036efa8a';

        // Initialize CURL:
        $ch = curl_init('http://apilayer.net/api/' . $endpoint . '?access_key=' . $access_key . '&vat_number=' . $NIE . '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $return = curl_exec($ch);
        curl_close($ch);

        // Access and use your preferred validation result objects
        return json_decode($return);
    }

    public static function validateNIE($NIE = false)
    {
        if ($NIE) {
            $nie = self::getNIEInfo($NIE);
            return isset($nie->valid) ? $nie->valid : false;
        }
    }

    public static function getDiscountAmount($coupon, $amount)
    {
        $return = [
            'percent' => false,
            'amount'  => ($coupon->DiscountAmount) ? $coupon->DiscountAmount : 0
        ];

        if ($coupon->DiscountPercentage) {
            $percent           = $coupon->DiscountPercentage / 100;
            $return['amount']  = $amount * $percent;
            $return['percent'] = $coupon->DiscountPercentage;
        }

        $return['discounted']  = $amount - $return['amount'] ;

        return $return;
    }

    public static function price($amount, $currencySymbol = "", $nofloat = false)
    {
        if($nofloat) {
            $amount = $amount / 100;
        }

        $amt = number_format($amount,2);

        return $currencySymbol . str_replace('.00','', (string) $amt);
    }

    public static function getUserProductInfo()
    {
        $now     = date('Y-m-d');
        $parent  = User::info('ParentUserID');
        $user    = ($parent) ?  User::info(false, $parent) : User::info();
        $product = (array) App::load()->model('products', true)->getOneBy(['StripeProductID' => $user->StripeProductID]);
        $plan    = (array) App::load()->model('productplans', true)->getOneBy(['StripePlanID' => $user->StripePlanID]);
        $return  = (object) array_merge($product, $plan);

        /*if($user->BoostExpiration >= $now) {
            $product = (array) App::load()->model('products',true)->getOneBy(['StripeProductID'=>$user->BoostProduct]);
            $plan    = (array) App::load()->model('productplans',true)->getOneBy(['StripePlanID'=>$user->BoostPlan]);
            $return  = (object) array_merge($product, $plan);
        }*/

        return $return;
    }

    public static function getTheUserProductInfo($ID)
    {
        $now     = date('Y-m-d');
        $parent  = User::info('ParentUserID');
        $user    = ($parent) ?  User::info(false, $parent) : User::info();
        $product = (array) App::load()->model('products', true)->getOneBy(['StripeProductID' => $user->StripeProductID]);
        $plan    = (array) App::load()->model('productplans', true)->getOneBy(['StripePlanID' => $user->StripePlanID]);
        $return  = (object) array_merge($product, $plan);

        /*if($user->BoostExpiration >= $now) {
            $product = (array) App::load()->model('products',true)->getOneBy(['StripeProductID'=>$user->BoostProduct]);
            $plan    = (array) App::load()->model('productplans',true)->getOneBy(['StripePlanID'=>$user->BoostPlan]);
            $return  = (object) array_merge($product, $plan);
        }*/

        return $return;
    }

    public static function getProductInfoByUser($ID)
    {
        $now     = date('Y-m-d');
        $parent  = User::info('ParentUserID', $ID);
        $user    = ($parent) ? User::info(false, $parent) : User::info(false, $ID);
        $product = (array) App::load()->model('products', true)->getOneBy(['StripeProductID' => $user->StripeProductID]);
        $plan    = (array) App::load()->model('productplans', true)->getOneBy(['StripePlanID' => $user->StripePlanID]);
        $return  = (object) array_merge($product, $plan);

        return $return;
    }

    public static function userBoosted()
    {
        $now     = date('Y-m-d');
        $parent  = User::info('ParentUserID');
        $user    = ($parent) ?  User::info(false, $parent) : User::info();
        $return  = false;
        if ($user->BoostExpiration >= $now) {
            $product = (array) App::load()->model('products', true)->getOneBy(['StripeProductID' => $user->BoostProduct]);
            $plan    = (array) App::load()->model('productplans', true)->getOneBy(['StripePlanID' => $user->BoostPlan]);
            $return  = (object) array_merge($product, $plan);
        }

        return $return;
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

    public static function projectActivities($data = array())
    {
        $db    = new DB();
        $data['UserID'] = User::info('UserID');
        $actId = $db->insert('project_activities', $data);
    }

    public static function imageExtensions()
    {
        return ['png','jpeg','jpg','ico','gif'];
    }

    public static function getDesignerShortname($designerLastName)
    {
        return substr($designerLastName, 0, 1);
    }

    public static function haveChatAlert()
    {
        $UserID     = User::info('UserID');
        $latestChat = App::load()->model('chats', true)->getClientLatest($UserID);
        
        return (($latestChat) && $latestChat->ReplyToID == $UserID && $latestChat->UserID != $UserID && $latestChat->Seen == 0) ? true : false;
    }

    public static function getRevisionCount($RevisionID)
    {
        $db    = new DB();
        $sql = "SELECT RevisionCount FROM project_revisions WHERE ProjectRevisionID = " . $RevisionID;

        $data = $db->get_row($sql);
        $ret = false;
        if ($data) {
            $ret = $data->RevisionCount;
        }
        return $ret;
    }

    public static function isRevisionHidden($RevisionID)
    {
        $db    = new DB();
        $sql = "SELECT IsHidden FROM project_revisions WHERE ProjectRevisionID = " . $RevisionID;

        $data = $db->get_row($sql);
        $ret = false;
        if ($data) {
            if($data->IsHidden > 0){
              $ret = $data->IsHidden;
            }
        }
        return $ret;
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
