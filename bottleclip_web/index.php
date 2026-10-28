<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="stl_viewer.min.js"></script>
    <title>Bottleclip Online</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
       }

       #topScreenContent {
            flex: 1;
            overflow: hidden;
            position: relative;
            flex-direction: column;
            height: 100vh;
            align-items: center;
            justify-content: center;
       }
      
        #stl_cont {
            bottom: 0;
            top: 0;
            right: 0;
            left: 0;
            overflow: hidden;
            position: absolute;
            align-items: center;
            justify-content: center;
        }

        form {
            margin: 10px auto;
            text-align: center;
        }

        input[type="text"] {
            margin: 0.5rem;
            padding: 1rem 2rem;
            background-color: white;
            color: black;
            cursor: pointer;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }

        button {
            margin: 0.5rem;
            padding: 1rem 1rem;
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }

        input[type="text"] {
            width: 10rem;
        }

        #downloadButton {
            margin: 0.5rem auto;
        }

        button:hover,
        input[type="text"]:hover {
            background-color: #aaaaaa;
        }

        button:disabled {
            background-color: #aaaaaa;
            cursor: not-allowed;
        }

        #danceImg {
            max-width: 100%;
            max-height: 100%;
            display: flex;
            margin: auto;
        }

        #forkMeBanner {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 9999;
        }
        
        #bannerImg {
            width: 149px;
            height: auto;
        }
    </style>
</head>

<body>
    <div id='topScreenContent'>
        <a id='forkMeBanner' href="https://github.com/theScrabi/BottleClipOnline">
            <img id='bannerImg' decoding="async" src="https://github.blog/wp-content/uploads/2008/12/forkme_left_red_aa0000.png?resize=149%2C149" class="attachment-full size-full" alt="Fork me on GitHub" loading="lazy" data-recalc-dims="1">
        </a>
        <div id="stl_cont">
            <!--<img src="hamsterdance.gif" id="danceImg"> -->
        </div>
    </div>
    <form id="reqForm" method="POST">
        <input id="clipText" type="text" name="clipText" placeholder="Enter your Name">
        <button onClick="onRenderButtonClicked()" id="renderButton" type="submit" name="renderButton">Render !!!</Button>
    </form>
    <div class="button-container" style="display: flex;">
        <button id="downloadButton" onclick="download('slice_me.stl');" disabled>Download</button>
    </div>

    <script>
        const stl_cont = document.getElementById("stl_cont");

        const stl_viewer = new StlViewer(document.getElementById("stl_cont"));
        const downloadButton = document.getElementById('downloadButton');
        const clipTextInput = document.getElementById('clipText');
        const renderButton = document.getElementById('renderButton');

        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('text')) {
            clipTextInput.value = urlParams.get('text');
        }

        function onRenderButtonClicked() {
            const imgTag = document.createElement("img");
            imgTag.src = "hamsterdance.gif";
            imgTag.id = "danceImg";
            stl_cont.innerHTML = "";
            stl_cont.appendChild(imgTag);
        }

        function invalidInput(message) {
            const heading = document.createElement("h1");
            heading.textContent = message;
            stl_cont.replaceChildren(heading);
        }

        function upload(file) {
            stl_viewer.add_model({ local_file: file });
            window.preload = file;
        }

        function download(filename) {
            // Create a new link
            const anchor = document.createElement('a');
            anchor.href = URL.createObjectURL(window.out_file);
            anchor.download = filename;

            // Append to the DOM
            document.body.appendChild(anchor);

            // Trigger `click` event
            anchor.click();

            // Remove element from DOM
            document.body.removeChild(anchor);
        };

        function displayStl(stl) {
            console.log("render done");
            const output = stl;

            window.out_file = new File([output], {
                type: "application/octet-stream"
            });
            downloadButton.disabled = false;
            stl_viewer.add_model({ local_file: out_file, rotationz:0.7854, rotationx: -1.571, color:"#ffa500", animation:{delta:{rotationz:0.1, msec:1000, loop:true}} });


        };

        function adjustBodyHeight() {
            const viewportHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
            document.body.style.height = viewportHeight + 'px';
        }

        adjustBodyHeight();
        window.addEventListener('resize', adjustBodyHeight);

        document.getElementById('reqForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            window.location.search = `?text=${encodeURIComponent(clipTextInput.value)}`;
        });

        window.stl_viewer = stl_viewer;
        window.upload = upload;
        window.download = download;

    </script>

    <?php
        // Renders the clip with OpenSCAD and caches the result per name.
        // Uses an argv array so no shell is involved, and `timeout` so a
        // render can't block a php-fpm worker forever.
        function renderClip($clipText) {
            $cacheDir = sys_get_temp_dir() . '/bottleclip-cache';
            $cacheFile = $cacheDir . '/' . hash('sha256', $clipText) . '.stl';
            if(is_file($cacheFile)) {
                return file_get_contents($cacheFile);
            }

            $cmd = ['timeout', '30', 'openscad', '-Dname="' . $clipText . '"',
                    '--export-format', 'stl', '-o', '-', 'clip/bottle-clip.scad'];
            $proc = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['file', '/dev/null', 'w']], $pipes, __DIR__);
            if(!is_resource($proc)) {
                return null;
            }
            $stl = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            if(proc_close($proc) !== 0 || $stl === '') {
                return null;
            }

            if(is_dir($cacheDir) || @mkdir($cacheDir, 0700)) {
                file_put_contents($cacheFile, $stl, LOCK_EX);
            }
            return $stl;
        }

        if(isset($_GET['text'])) {
            $clipText = $_GET['text'];
            if(mb_strlen($clipText, 'UTF-8') <= 16) {
                if(preg_match('/^[a-zA-Z0-9\-#!.?üäöÜÄÖß]*$/u', $clipText)) {
                    $stl = renderClip($clipText);
                    if($stl !== null) {
                        echo "<script>displayStl(" . json_encode($stl) . ");</script>";
                    } else {
                        echo "<script>invalidInput(".json_encode("Rendering failed, please try again later.") .");</script>";
                    }
                } else {
                    echo "<script>invalidInput(".json_encode("Only letters, digits, -, #, !, ., and ? are allowed.") .");</script>";
                }
            } else {
                echo "<script>invalidInput(".json_encode("Your name may only be 16 characters long.") .");</script>";
            }
        }
    ?>
</body>

</html>
