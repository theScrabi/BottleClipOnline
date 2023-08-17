<!DOCTYPE html>
<html>

<head>
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

        #stl_cont {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        form {
            margin: 10px auto;
            text-align: center;
        }

        input[type="text"] {
            margin: 5px;
            padding: 10px 20px;
            background-color: white;
            color: black;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button {
            margin: 5px;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        input[type="text"] {
            width: 200px;
        }

        #downloadButton {
            margin: 10px auto;
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
            display: block;
            margin: auto;
        }
    </style>
</head>

<body>
    <div id="stl_cont">
        <!--<img src="hamsterdance.gif" id="danceImg"> -->
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

        window.stl_viewer = stl_viewer;
        window.upload = upload;
        window.download = download;

    </script>

    <?php
        if(isset($_POST['renderButton'])) {
            $clipText = $_POST['clipText'];

            $safeClipText = escapeshellarg($clipText);

            $command = "openscad -D'name=\"$safeClipText\"' --export-format stl -o - clip/bottle-clip.scad";
            $stl = shell_exec($command);

            echo "<script>displayStl(" . json_encode($stl) . ")</script>";
        }
    ?>
</body>

</html>
