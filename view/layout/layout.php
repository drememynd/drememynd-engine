<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>DremeMynd: <?php echo $view->pageName; ?></title>

        <link rel="stylesheet" href="css/vendor/foundation.css">
        <link rel="stylesheet" href="css/vendor/framework.css">
    </head>
    <body>
        <div class="grid-container">
            <div class="grid-y grid-frame">

                <div class="cell shrink" >
                    <div class="grid-x">
                        <div class="large-12 cell">
                            <?php echo $header; ?>
                        </div>
                    </div>
                </div>
                <div class="cell auto" >
                    <div class="grid-x grid-ht-full">
                        <div id="left-menu" class="cell large-3 callout">
                            <div class="grid-y grid-ht-full">
                                <?php echo $leftMenu; ?>
                            </div>
                        </div>
                        <div id="content-area" class="cell large-9 callout">
                            <?php echo $content; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="js/vendor/jquery.js"></script>
        <script src="js/vendor/what-input.js"></script>
        <script src="js/vendor/foundation.js"></script>
    </body>
</html>