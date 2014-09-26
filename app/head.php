<?php use vendors\BeFeW\Utils as Utils; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />

        <title>BeFeW <?php if(Utils::getVar($title) != null) echo $title ?></title>

        <?php
            if(Utils::getVar($headTags) != null) {
                foreach($headTags as $headTag) {
                    echo $headTag;
                }
            }

            if(Utils::getVar($styles) != null) {
                foreach($styles as $style) {
                    ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $style; ?>" />
                    <?php
                }
            }

            if(Utils::getVar($headJavascripts) != null) {
                foreach($headJavascripts as $headJavascript) {
                    ?>
        <script type="text/javascript" src="<?php echo $headJavascript; ?>"></script>
                    <?php
                }
            }
        ?>
    </head>
    <body>