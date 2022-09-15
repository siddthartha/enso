<?php
/** @var string $html */
?>
<html lang="en">
<head><title>Docs</title>
    <meta name="color-scheme" content="light dark" />
<!--    <link rel="stylesheet" href="http://markdowncss.github.io/retro/css/retro.css" type="text/css" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexboxgrid/6.3.1/flexboxgrid.min.css" type="text/css" />
    <link rel="stylesheet" href="https://sindresorhus.com/github-markdown-css/github-markdown.css" type="text/css" />
    <style>
        body {
            font-family: monospace;
            margin: 1rem 0 1rem 0;
            padding: 18px;
            max-width: fit-content;
        }

        code {
            padding: .2em .4em;
            margin: 0;
            font-size: 85%;
            background-color: #333;
            border-radius: 5px;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<div class="row around-xs">
    <div class="col-xs-10">
        <div class="markdown-body box"><?= $html; ?></div>
    </div>
</div>
</body>
</html>
