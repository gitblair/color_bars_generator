document.getElementById('videoForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const resolution = document.querySelector('input[name="resolution"]:checked').value;
    const duration = document.querySelector('input[name="duration"]:checked').value;
    const includeTimecode = document.getElementById('timecode').checked;
    const includeAttribution = document.getElementById('attribution').checked;

    let width, height;
    if (resolution === '720') {
        width = 1280;
        height = 720;
    } else {
        width = 1080;
        height = 1920;
    }

    // Set the path to ffmpeg
    const ffmpegPath = '/opt/homebrew/bin/ffmpeg';

    // Create the filter complex string
    let filterComplex = `scale=${width}:${height},fps=59.94`;
    if (includeTimecode) {
        filterComplex += `,drawtext=text='%{pts\\:hms}':fontcolor=white:fontsize=50:x=100:y=100`;
    }
    if (includeAttribution) {
        filterComplex += `,drawtext=text='github: gitblair':fontcolor=white:fontsize=25:x=100:y=200`;
    }

    const outputPath = `output_${resolution}_${duration}.mp4`;

    // Generate the SMPTE color bar video with FFmpeg
    const command = `
        ${ffmpegPath} -f lavfi -i smpteh -f lavfi -i sine=frequency=1000:sample_rate=48000 -shortest
        -vf "${filterComplex}" -t ${duration} ${outputPath}`;

    const { exec } = require('child_process');
    exec(command, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`stderr: ${stderr}`);
            return;
        }
        // Display the video in the player and provide a download link
        const videoPlayer = document.getElementById('videoPlayer');
        const downloadLink = document.getElementById('downloadLink');
        videoPlayer.src = outputPath;
        videoPlayer.style.display = 'block';
        downloadLink.href = outputPath;
        downloadLink.style.display = 'block';
    });
});
