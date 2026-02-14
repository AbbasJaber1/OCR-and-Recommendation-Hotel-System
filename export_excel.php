<?php
// Include the necessary libraries
require 'vendor/autoload.php';  // Load PhpSpreadsheet
include 'connect.php';  // Include your database connection

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Create a new spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the title row with the date
$selectedDate = isset($_GET['selected_date']) ? $_GET['selected_date'] : date('Y-m-d');
$sheet->setCellValue('A1', "Daily Report - Date: $selectedDate");

// Add some space by merging cells in the title row
$sheet->mergeCells('A1:E1');

// Set the column headers (move 'الاسم' to first position)
$headers = ['الاسم', 'وقت الدخول', 'وقت الخروج'];
$sheet->fromArray($headers, NULL, 'A2');

// Query to fetch data from the database (same as in daily_report.php)
$sql = "
    SELECT 
        g.guest_name,  -- Move 'guest_name' to the first position in the SELECT
        cl.checkout_time, 
        cl.return_time
        
    FROM 
        check_logs cl 
    JOIN 
        guests g ON cl.guest_id = g.guest_id 
    WHERE 
        DATE(cl.checkout_time) = '$selectedDate'
";
$result = $conn->query($sql);

// Write the data to the spreadsheet starting from row 3
$rowIndex = 3;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowIndex", $row['guest_name']);  // Set guest name in the first column
    $sheet->setCellValue("B$rowIndex", $row['return_time'] ? date('H:i', strtotime($row['return_time'])) : 'لم يعد بعد');
    $sheet->setCellValue("C$rowIndex", date('H:i', strtotime($row['checkout_time'])));
   
    $rowIndex++;
}

// Set the RTL direction for the entire sheet
$sheet->setRightToLeft(true);

// Adjust column widths to add space between cells
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setWidth(20);
}

// Center the content horizontally and vertically
$sheet->getStyle('A1:E' . ($rowIndex - 1))
      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);

// Set page setup to fit all content on one A4 page
$sheet->getPageSetup()->setFitToPage(true);
$sheet->getPageSetup()->setFitToWidth(1);
$sheet->getPageSetup()->setFitToHeight(0);

// Save the file as an Excel .xlsx file
$writer = new Xlsx($spreadsheet);
$filename = "Daily_Report_$selectedDate.xlsx";

// Set headers to trigger the download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=$filename");
header('Cache-Control: max-age=0');

// Output the Excel file
$writer->save('php://output');
exit();
?>
