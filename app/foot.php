<?php
    use vendor\Befew\Utils as Utils;
    if(Utils::getVar($befewFootJavascripts) != null) {
        foreach($befewFootJavascripts as $befewFootJavascript) {
            ?>
        <script type="text/javascript" src="<?php echo $befewFootJavascript; ?>"></script>
            <?php
        }
    }
?>
    </body>
</html>