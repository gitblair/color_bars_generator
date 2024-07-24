<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colorbars Video Generator</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Colorbars Video Generator</h1>
        <form id="video-form">
            <div class="mb-3">
                <label for="resolution" class="form-label">Resolution</label>
                <select class="form-select" id="resolution" name="resolution" required>
                    <option value="720">1280x720</option>
                    <option value="1080">1920x1080</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration</label>
                <select class="form-select" id="duration" name="duration" required>
                    <option value="10">10 seconds</option>
                    <option value="30">30 seconds</option>
                    <option value="60">1 minute</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="timecode" name="timecode">
                <label class="form-check-label" for="timecode">Include Timecode</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="attribution" name="attribution">
                <label class="form-check-label" for="attribution">Include Attribution (github: gitblair)</label>
            </div>
            <button type="submit" class="btn btn-primary">Generate Video</button>
        </form>
        <div id="video-container" class="mt-5" style="display: none;">
            <h3>Generated Video</h3>
            <video id="generated-video" controls style="width: 100%; height: auto;"></video>
            <a id="download-link" class="btn btn-success mt-3" href="#" download>Download Video</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#video-form').on('submit', function(event) {
                event.preventDefault();
                $('#video-container').hide();

                var formData = new FormData(this);

                $.ajax({
                    url: 'generate_video.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            var videoUrl = data.file;

                            $('#generated-video').attr('src', videoUrl);
                            $('#download-link').attr('href', videoUrl);
                            $('#video-container').show();
                        } else {
                            alert('Video generation failed: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while generating the video.');
                    }
                });
            });
        });
    </script>
</body>
</html>
