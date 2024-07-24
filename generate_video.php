<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resolution = $_POST['resolution'];
    $duration = $_POST['duration'];
    $includeTimecode = isset($_POST['timecode']);
    $includeAttribution = isset($_POST['attribution']);

    $width = ($resolution === '720') ? 1280 : 1080;
    $height = ($resolution === '720') ? 720 : 1920;

    $ffmpegPath = '/opt/homebrew/bin/ffmpeg';
    $publicPath = 'videos'; // Directory to store generated videos

    if (!is_dir($publicPath)) {
        mkdir($publicPath, 0755, true);
    }

    // Create the filter complex string
    $filterComplex = "scale={$width}:{$height},fps=59.94";
    if ($includeTimecode) {
        $filterComplex .= ",drawtext=text='%{pts\\:hms}':fontcolor=white:fontsize=50:x=100:y=100";
    }
    if ($includeAttribution) {
        $filterComplex .= ",drawtext=text='github: gitblair':fontcolor=white:fontsize=25:x=100:y=200";
    }

    $outputFile = "$publicPath/output_{$resolution}_{$duration}.mp4";

    // Generate the test pattern video with FFmpeg
    $command = "$ffmpegPath -f lavfi -i testsrc=duration=$duration:size={$width}x{$height}:rate=59.94 -f lavfi -i sine=frequency=1000:sample_rate=48000 -shortest -vf \"$filterComplex\" -t $duration -c:v libx264 -pix_fmt yuv420p -c:a aac -strict -2 $outputFile 2>&1";

    exec($command, $output, $returnVar);

    if ($returnVar === 0) {
        // Return the relative path to the generated video
        echo json_encode(['success' => true, 'file' => $outputFile]);
    } else {
        echo json_encode(['success' => false, 'message' => implode("\n", $output)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
