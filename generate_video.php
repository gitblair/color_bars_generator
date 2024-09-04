<?php
header('Content-Type: application/json');
require "config.php";

// Function to log messages to a custom log file
date_default_timezone_set('America/New_York'); // Adjust based on your desired timezone
function custom_log($message) {
    $logfile = 'log.txt';
    file_put_contents($logfile, date("Y-m-d H:i:s") . " - " . $message . "\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : '2';
    $text = isset($_POST['text']) ? htmlspecialchars($_POST['text']) : 'TEST';

    // Default values
    $audiocommand = '';
    $stylecommand = '';
    $resolutioncommand = '';

$audio = isset($_POST['audio']) ? $_POST['audio'] : 'tone';
if ($audio === "tone") // Use === for a strict comparison
{
  $audiocommand = '-f lavfi -i sine=frequency=1000:r=48000';
}
else
{
  $audiocommand = '-f lavfi -i anullsrc=r=48000';
}

$style = isset($_POST['style']) ? $_POST['style'] : 'SMPTE';
if ($style === "SMPTE") // Use === for a strict comparison
{
  $stylecommand = '-f lavfi -i smptebars=';
}
elseif ($style === "BLACK")
{
  $stylecommand = '-f lavfi -i color=color=black:';
}
elseif ($style === "BLUE") // Changed else to elseif
{
  $stylecommand = '-f lavfi -i color=color=blue:';
}

$resolution = isset($_POST['resolution']) ? $_POST['resolution'] : '1920x1080';
if ($resolution === "1920x1080") // Use === for a strict comparison
{
  $resolutioncommand = 'size=1920x1080:rate=59.94';
}
elseif ($resolution === "1280x720")
{
  $resolutioncommand = 'size=1280x720:rate=59.94';
}
elseif ($resolution === "1080x1920") // Changed else to elseif
{
  $resolutioncommand = 'size=1080x1920:rate=59.94';
}


    // Validate parameters
    if (!preg_match('/^\d+x\d+$/', $resolution)) {
        echo json_encode(['success' => false, 'message' => 'Invalid size parameter.']);
        exit;
    }
    if ($duration <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid duration parameter.']);
        exit;
    }

    // Desired filename for the download
    $downloadFilename = "{$style}_{$resolution}_{$duration}secs_{$audio}.mp4";
    $tempdir = sys_get_temp_dir();


    // Create a temporary file with a unique name
    $tempFile = tempnam($tempdir, 'SMPTE_color_bars_') . '.mp4';

    // Prepare the FFmpeg command
    $command = "$ffmpegPath $stylecommand$resolutioncommand $audiocommand -t $duration -vf \"drawtext=text='$text':fontcolor=white:fontsize=72:box=1:boxcolor=black@0.5:boxborderw=10:x=(w-text_w)/2:y=(h-text_h)/3\" -c:v libx264 -pix_fmt yuv420p -c:a aac -ac 2 -strict experimental $tempFile 2>&1";

    // Execute the FFmpeg command
    exec($command, $output, $return_var);

    if ($return_var !== 0 || !file_exists($tempFile)) {
        // Return error details
        $error_message = implode("\n", $output);
        echo json_encode(['success' => false, 'message' => 'Error processing video: ' . $error_message]);
        exit;
    }

    // Log successful video creation
    custom_log("Successfully created video: $downloadFilename");

    // Serve the file directly to the browser
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tempFile));

    // Output the file content
    readfile($tempFile);

    // Clean up the temporary file
    unlink($tempFile);
    exit;
} else {
    // Log unsuccessful attempt
    custom_log("Error failed to create video: $downloadFilename");
    // Return JSON error response
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
?>
