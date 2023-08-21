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

        #forkMebanner {
            position: absolute;
            top: 0;
            right: 0;
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
    <form method="POST">
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
        const renderButton = document.getElementById('renderButton');

        function onRenderButtonClicked() {
            const imgTag = document.createElement("img");
            imgTag.src = "hamsterdance.gif";
            imgTag.id = "danceImg";
            stl_cont.innerHTML = "";
            stl_cont.appendChild(imgTag);
        }

        function invalidInput(message) {
            stl_cont.innerHTML = "<h1>" + message + "</h1>";
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

        function display(stl) {
            document.write("<pre>" + stl + "</pre>");
        }

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

        window.stl_viewer = stl_viewer;
        window.upload = upload;
        window.download = download;

    </script>

    <?php
        if(isset($_POST['renderButton'])) {
            $clipText = $_POST['clipText'];
            if(mb_strlen($clipText, 'UTF-8') <= 16) {
                if(preg_match('/^[a-zA-Y0-9\-#!.\?]*$/', $clipText)) {
                    $safeClipText = escapeshellarg($clipText);
                    $command = "openscad -D'name=\"$safeClipText\"' --export-format stl -o - clip/bottle-clip.scad";
                    $stl = shell_exec($command);

                    echo "<script>displayStl(" . json_encode($stl) . ");</script>";
                } else {
                    echo "<script>invalidInput(".json_encode("Only letters, digits, -, #, !, ., and ? are allowed.)") .");</script>";
                }
            } else {
                echo "<script>invalidInput(".json_encode("Your name may only be 16 characters long.") .");</script>";
            }
        }
    ?>
</body>

</html>
