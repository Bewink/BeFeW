<?php use vendors\BeFeW\Utils as Utils; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />

        <title>BeFeW <?php if(Utils::getVar($befewHeadTitle) != null) echo $befewHeadTitle ?></title>

        <?php
            if(Utils::getVar($befewHeadTags) != null) {
                foreach($befewHeadTags as $befewHeadTag) {
                    echo $befewHeadTag;
                }
            }

            if(Utils::getVar($befewHeadStyles) != null) {
                foreach($befewHeadStyles as $befewHeadStyle) {
                    ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $befewHeadStyle; ?>" />
                    <?php
                }
            }

            if(Utils::getVar($befewHeadJavascripts) != null) {
                foreach($befewHeadJavascripts as $befewHeadJavascript) {
                    ?>
        <script type="text/javascript" src="<?php echo $befewHeadJavascript; ?>"></script>
                    <?php
                }
            }
        ?>
    </head>
    <body>