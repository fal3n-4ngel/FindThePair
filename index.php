<?php
$selectedColor = isset($_POST['color']) ? $_POST['color'] : '#3498db';
$textColor = getContrastColor($selectedColor);

function getContrastColor($hexcolor) {
    $r = hexdec(substr($hexcolor, 1, 2));
    $g = hexdec(substr($hexcolor, 3, 2));
    $b = hexdec(substr($hexcolor, 5, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 128) ? '#000000' : '#ffffff';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Color Picker</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            transition: background-color 0.3s ease;
            background-color: <?php echo $selectedColor; ?>;
            color: <?php echo $textColor; ?>;
        }
        .color-picker {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            text-align: center;
        }
        .color-display {
            width: 150px;
            height: 150px;
            margin: 20px auto;
            border: 2px solid <?php echo $textColor; ?>;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }
        input[type="color"] {
            -webkit-appearance: none;
            border: none;
            width: 80px;
            height: 80px;
            border-radius: 40px;
            overflow: hidden;
            cursor: pointer;
        }
        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }
        input[type="color"]::-webkit-color-swatch {
            border: none;
        }
        h2 {
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="color-picker">
        <h2>Color Picker</h2>
        <form method="post">
            <input type="color" name="color" value="<?php echo $selectedColor; ?>" onchange="this.form.submit()">
        </form>
        <div class="color-display" style="background-color: <?php echo $selectedColor; ?>"></div>
        <p>Selected color: <?php echo $selectedColor; ?></p>
    </div>
    <script>
        document.querySelector('input[type="color"]').addEventListener('input', function(e) {
            document.body.style.backgroundColor = e.target.value;
            document.body.style.color = getContrastColor(e.target.value);
        });

        function getContrastColor(hexcolor) {
            hexcolor = hexcolor.replace("#", "");
            var r = parseInt(hexcolor.substr(0,2),16);
            var g = parseInt(hexcolor.substr(2,2),16);
            var b = parseInt(hexcolor.substr(4,2),16);
            var yiq = ((r*299)+(g*587)+(b*114))/1000;
            return (yiq >= 128) ? '#000000' : '#ffffff';
        }
    </script>
</body>
</html>
