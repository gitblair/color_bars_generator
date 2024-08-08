<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Bars & Tone Generator</title>

    <!-- Bootstrap Style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  </head>
  <body>
    <div class="container-lg mb-5">
      <div class="row">
        <div class="col-12">

          <?php include 'config.php'; ?>
          <h1 class="my-4">Color Bars & Tone Generator</h1>

          <form id="video-form">
<div class="mb-3">
    <label for="size" class="form-label">Resolution</label>
    <select class="form-select" id="size" name="size" required>
      <option value="1920x1080">1080p Full HD 16:9 1920 x 1080</option>
        <option value="1280x720">720p HD 1280 x 720</option>
        <option value="1080x1920">1080p Full HD Vertical 9:16 1080 x 1920</option>
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
<div class="mb-3">
    <label for="text" class="form-label">Text Overlay Option</label>
    <select class="form-select" id="text" name="text" required>
        <option value="TEST">TEST</option>
        <option value="START">START</option>
        <option value="END">END</option>
        <option value="">None</option>
    </select>
</div>
<button type="submit" class="btn btn-primary">Generate Video</button>
</form>


          </div>

        </div> <!-- end col-12 -->
      </div> <!-- end row -->
    </div> <!-- end container -->

    <div class="row mb-5">
      <div class="col-12 mb-5">
        <nav class="text-center">
          <ul class="nav justify-content-center">
            <li class="nav-item">
              <a class="nav-link" href="instructions.html">instructions</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="log.txt">log</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="phpinfo.php">phpinfo</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="https://en.wikipedia.org/wiki/SMPTE_color_bars">wiki: SMPTE_color_bars</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="javascript:history.back()">return</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <script>
    document.getElementById('video-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('generate_video.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            // Extract filename from Content-Disposition header
            const disposition = response.headers.get('Content-Disposition');
            let filename = 'SMPTE_color_bars.mp4'; // Fallback filename

            if (disposition && disposition.indexOf('attachment') !== -1) {
                const matches = /filename="(.+?)"/.exec(disposition);
                if (matches && matches[1]) {
                    filename = matches[1];
                }
            }
            return response.blob().then(blob => ({ blob, filename }));
        })
        .then(({ blob, filename }) => {
            // Create a URL for the blob and simulate a click to trigger the download
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename; // Use the dynamic filename
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url); // Clean up
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('An error occurred: ' + error.message);
        });
    });
  </script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</body>
</html>
