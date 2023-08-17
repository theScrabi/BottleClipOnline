<!DOCTYPE html>
<html>

<head>
    <script src="stl_viewer.min.js"></script>
    <title>Bottleclip Online</title>
</head>

<body style="height:100%;">
    <div id="stl_cont" style="width:100%;height:100%;"></div>
    <form method="POST">
        <input type="text" name="clipText" placeholder="Enter your Name">
        <button type="submit" name="renderButton">Render !!!</Button>
    </form>
    <div class="button-container" style="display: flex;">
        <button id="downloadButton" onclick="download('slice_me.stl');" disabled>Download</button>
</div>

    <script>
        const stl_viewer = new StlViewer(document.getElementById("stl_cont"));
        const downloadButton = document.getElementById('downloadButton');
        const renderButton = document.getElementById('renderButton');

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
