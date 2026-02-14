<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = "";  // Replace with your actual API Key //AIzaSyDWUQDQa5r // PcqArTxF // 23fA8T0a // 5hujYXgA // ( 6a9b2c8e-1f0c-4d5b-9e7a-8f3c4d2e1b0f )

header('Content-Type: application/json');

// List of country codes mapped to full country names
$countryCodes = [
    "AFG" => "Afghanistan", "ALB" => "Albania", "DZA" => "Algeria", "AND" => "Andorra", "AGO" => "Angola",
    "ARG" => "Argentina", "ARM" => "Armenia", "AUS" => "Australia", "AUT" => "Austria", "AZE" => "Azerbaijan",
    "BHS" => "Bahamas", "BHR" => "Bahrain", "BGD" => "Bangladesh", "BRB" => "Barbados", "BLR" => "Belarus",
    "BEL" => "Belgium", "BLZ" => "Belize", "BEN" => "Benin", "BTN" => "Bhutan", "BOL" => "Bolivia",
    "BIH" => "Bosnia and Herzegovina", "BWA" => "Botswana", "BRA" => "Brazil", "BRN" => "Brunei", "BGR" => "Bulgaria",
    "BFA" => "Burkina Faso", "BDI" => "Burundi", "CPV" => "Cape Verde", "KHM" => "Cambodia", "CMR" => "Cameroon",
    "CAN" => "Canada", "CAF" => "Central African Republic", "TCD" => "Chad", "CHL" => "Chile", "CHN" => "China",
    "COL" => "Colombia", "COM" => "Comoros", "COG" => "Congo", "CRI" => "Costa Rica", "HRV" => "Croatia",
    "CUB" => "Cuba", "CYP" => "Cyprus", "CZE" => "Czech Republic", "DNK" => "Denmark", "DJI" => "Djibouti",
    "DOM" => "Dominican Republic", "ECU" => "Ecuador", "EGY" => "Egypt", "SLV" => "El Salvador", "GNQ" => "Equatorial Guinea",
    "ERI" => "Eritrea", "EST" => "Estonia", "ETH" => "Ethiopia", "FJI" => "Fiji", "FIN" => "Finland",
    "FRA" => "France", "GAB" => "Gabon", "GMB" => "Gambia", "GEO" => "Georgia", "DEU" => "Germany",
    "GHA" => "Ghana", "GRC" => "Greece", "GRD" => "Grenada", "GTM" => "Guatemala", "GIN" => "Guinea",
    "HTI" => "Haiti", "HND" => "Honduras", "HUN" => "Hungary", "ISL" => "Iceland", "IND" => "India",
    "IDN" => "Indonesia", "IRN" => "Iran", "IRQ" => "Iraq", "IRL" => "Ireland", "ISR" => "Israel",
    "ITA" => "Italy", "JAM" => "Jamaica", "JPN" => "Japan", "JOR" => "Jordan", "KAZ" => "Kazakhstan",
    "KEN" => "Kenya", "KWT" => "Kuwait", "LBN" => "Lebanon", "LBR" => "Liberia", "LBY" => "Libya",
    "LTU" => "Lithuania", "LUX" => "Luxembourg", "MDG" => "Madagascar", "MWI" => "Malawi", "MYS" => "Malaysia",
    "MLI" => "Mali", "MLT" => "Malta", "MEX" => "Mexico", "MDA" => "Moldova", "MNG" => "Mongolia",
    "MNE" => "Montenegro", "MAR" => "Morocco", "MOZ" => "Mozambique", "MMR" => "Myanmar", "NAM" => "Namibia",
    "NPL" => "Nepal", "NLD" => "Netherlands", "NZL" => "New Zealand", "NGA" => "Nigeria", "NOR" => "Norway",
    "OMN" => "Oman", "PAK" => "Pakistan", "PAN" => "Panama", "PNG" => "Papua New Guinea", "PRY" => "Paraguay",
    "PER" => "Peru", "PHL" => "Philippines", "POL" => "Poland", "PRT" => "Portugal", "QAT" => "Qatar",
    "ROU" => "Romania", "RUS" => "Russia", "RWA" => "Rwanda", "SAU" => "Saudi Arabia", "SEN" => "Senegal",
    "SRB" => "Serbia", "SGP" => "Singapore", "SVK" => "Slovakia", "SVN" => "Slovenia", "ZAF" => "South Africa",
    "KOR" => "South Korea", "ESP" => "Spain", "LKA" => "Sri Lanka", "SDN" => "Sudan", "SWE" => "Sweden",
    "CHE" => "Switzerland", "SYR" => "Syria", "TWN" => "Taiwan", "TZA" => "Tanzania", "THA" => "Thailand",
    "TUN" => "Tunisia", "TUR" => "Turkey", "UKR" => "Ukraine", "ARE" => "United Arab Emirates", "GBR" => "United Kingdom",
    "USA" => "United States", "URY" => "Uruguay", "UZB" => "Uzbekistan", "VEN" => "Venezuela", "VNM" => "Vietnam",
    "YEM" => "Yemen", "ZMB" => "Zambia", "ZWE" => "Zimbabwe"
];

if ($_FILES['image']['error'] == 0) {
    $imagePath = $_FILES['image']['tmp_name'];
    $imageData = base64_encode(file_get_contents($imagePath));

    $url = "https://vision.googleapis.com/v1/images:annotate?key=$apiKey";

    $requestData = [
        "requests" => [
            [
                "image" => ["content" => $imageData],
                "features" => [["type" => "TEXT_DETECTION"]]
            ]
        ]
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json",
            "method"  => "POST",
            "content" => json_encode($requestData),
        ],
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response, true);

    // Extract text from Google Vision API response
    $ocrText = $result["responses"][0]["textAnnotations"][0]["description"] ?? "No text found";

    // Parse MRZ (Machine Readable Zone)
    $lines = explode("\n", $ocrText);
    $lines = array_filter($lines, fn($line) => strlen(trim($line)) > 20); // Remove short/noisy lines

    // Ensure we capture exactly two MRZ lines
if (count($lines) >= 2) { 
    $mrz_line_1 = trim($lines[0]);
    $mrz_line_2 = trim($lines[1]);

    // Debugging: Log the extracted lines
    error_log("MRZ Line 1: " . $mrz_line_1);
    error_log("MRZ Line 2: " . $mrz_line_2);

    // Ensure the second line is exactly 44 characters long
    if (strlen($mrz_line_2) < 44) {  
        error_log("Warning: MRZ second line too short, possible OCR error!");
    }

    // Extract passport number correctly
    $passport_number = substr($mrz_line_2, 0, 10);  // Change from 9 to 10 to include the full passport number
    $passport_number = preg_replace('/[^A-Z0-9]/', '', $passport_number); // Remove unexpected spaces or symbols

    error_log("Extracted Passport Number Before Cleanup: " . substr($mrz_line_2, 0, 10));
    error_log("Extracted Passport Number After Cleanup: " . $passport_number);

}


    // Extract names from first MRZ line
    $firstLine = $lines[0];
    preg_match('/P<([A-Z]{3})([A-Z]+)<<([A-Z]+)<<*/', $firstLine, $nameMatch);
    $countryCode = $nameMatch[1] ?? "Unknown";
    $lastName = str_replace("<", " ", $nameMatch[2] ?? "Unknown");
    $firstName = str_replace("<", " ", $nameMatch[3] ?? "Unknown");

    // Extract other details from second MRZ line
    $secondLine = $lines[1];
    $passportNumber = substr($secondLine, 0, 9);
    $nationalityCode = substr($secondLine, 10, 3);
    $dob = substr($secondLine, 13, 6);
    $gender = substr($secondLine, 20, 1);
    $expiry = substr($secondLine, 21, 6);

    $nationality = $countryCodes[$nationalityCode] ?? "Unknown";

    // Smart Date Formatting for DOB and Expiry
    function formatDate($date, $isDob = true) {
        $currentYear = date("Y"); // e.g., 2024
        $currentCentury = substr($currentYear, 0, 2); // "20"
        $year = substr($date, 0, 2);
        $month = substr($date, 2, 2);
        $day = substr($date, 4, 2);

        if ($isDob) {
            // DOB: If year is greater than current year, assume 1900s
            $fullYear = ($year > date("y")) ? "19$year" : "20$year";
        } else {
            // Expiry Date: If year is LESS than current year, assume 2000s
           $fullYear = "20$year";
        }

        return "$fullYear-$month-$day";
    }

    $dobFormatted = formatDate($dob, true);
    $expiryFormatted = formatDate($expiry, false);

    // Convert Gender
    $genderFull = ($gender == "M") ? "Male" : "Female";

    // Return structured response
    echo json_encode([
        "First Name" => $firstName,
        "Last Name" => $lastName,
        "Nationality" => $nationality,
        "Passport Number" => $passport_number,
        "Date of Birth" => $dobFormatted,
        "Gender" => $genderFull,
        "Expiry Date" => $expiryFormatted
    ]);
} else {
    echo json_encode(["error" => "Image upload failed"]);
}
error_log("Raw OCR Response: " . print_r($result, true));  // ADD THIS LINE


?>
