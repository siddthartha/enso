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
        <div class="markdown-body box">
            <h4>Donate my work (USDT / TRC20)</h4>
            <img alt="USDT/TRC20" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWAQMAAAAGz+OhAAAABlBMVEX///8AAABVwtN+AAABL0lEQVR42uzUMYoDMQwFUIEKX0rga6kIyDCFr2XQpVwI/qLZzO6mjWa7KG7yGnukj+hT/1EGbCUlxupAlE3zqFL3ECKpm21tux3wkB73mA216Os+m4iOm4xItU04Xvvyrp0zUn2IyOvc3rTzHqUH//lfMc0u5JszQkvKZthkg4jhHj3KRqo2d8sJ+XVHxdoc1OYcHXBfVDZtADAPd1/8xIo1jAxRCK8eHGWjtlXb4GzCT19utgzBIDUPBp59qZjmlxiC3f3CkrWdykHswXWjNrfNjLz0RVK3fDRwZAzgUTbDzN+xOPO1yva9K2cICQnX7dxrdmZI+pKy6TkmtdyR1x0ls61qCOHfrVu1BoyeuuQWI7IjSDKVdctjm+FYd9g5o9wRIRx9le1ThfoKAAD//yQvhLTrCA8oAAAAAElFTkSuQmCC" />
        </div>
    </div>
</div>
</body>
</html>
