<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/imgareaselect-default.css"/>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.imgareaselect.pack.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var selection = $('#photo').imgAreaSelect({
                handles: true,
                fadeSpeed: 200,
                instance: true
            });
            $("#crop").click(function () {
                $.ajax({
                    url: "crop.php",
                    type: "GET",
                    data: {
                        x: selection.getSelection().x1,
                        y: selection.getSelection().y1,
                        width: selection.getSelection().x2,
                        height: selection.getSelection().y2,
                        image: $("#photo").attr("src")
                    },
                    dataType: "html"
                }).done(function (data) {
                        $('#image_content').html(data);
                        $('#image_content').attr('disabled', false);
                    }
                );
            });
        });
    </script>
</head>
<body>
<h1>Use mouse =)</h1>
<img id="photo" src="bee.jpg" alt="bee"/><br/>
<input type="button" id="crop" value="Crop">
<img src="img/ajax-loader.gif" id="ajax-loader" style="display:none">
<div id="image_content">
</div>
</body>
</html>