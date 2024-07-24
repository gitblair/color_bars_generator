<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMPTE Color Bar Video Generator</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #videoOptions label {
            margin-right: 10px;
        }
        .form-check-inline {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">SMPTE Color Bar Video Generator</h1>
        <form id="videoForm" action="generate_video.php" method="post">
            <div class="form-group">
                <label for="resolution">Select Resolution:</label>
                <div id="resolutionOptions">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="resolution" id="res720" value="720" checked>
                        <label class="form-check-label" for="res720">1280x720</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="resolution" id="res1080" value="1080">
                        <label class="form-check-label" for="res1080">1080x1920</label>
                    </div>
                </div>
            </div>
            <div class="form-group mt-3">
                <label for="duration">Select Duration:</label>
                <div id="durationOptions">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="duration" id="duration10" value="10" checked>
                        <label class="form-check-label" for="duration10">10 seconds</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="duration" id="duration30" value="30">
                        <label class="form-check-label" for="duration30">30 seconds</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="duration" id="duration60" value="60">
                        <label class="form-check-label" for="duration60">1 minute</label>
                    </div>
                </div>
            </div>
            <div class="form-group mt-3">
                <label for="options">Additional Options:</label>
                <div id="additionalOptions">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="timecode" id="timecode" checked>
                        <label class="form-check-label" for="timecode">Include Timecode</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="attribution" id="attribution" checked>
                        <label class="form-check-label" for="attribution">Include Attribution (github: gitblair)</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Generate Video</button>
        </form>
        <div id="result" class="mt-5">
            <video id="videoPlayer" controls class="w-100 mt-3" style="display: none;"></video>
            <a id="downloadLink" href="#" class="btn btn-success mt-3" style="display: none;">Download Video</a>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('videoForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('generate_video.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const videoPlayer = document.getElementById('videoPlayer');
                    const downloadLink = document.getElementById('downloadLink');
                    videoPlayer.src = data.file;
                    videoPlayer.style.display = 'block';
                    downloadLink.href = data.file;
                    downloadLink.style.display = 'block';
                } else {
                    alert('Video generation failed: ' + data.message);
                }
            });
        });
    </script>
</body>
</html>
