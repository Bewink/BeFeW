<?php
    use vendors\BeFeW\Utils as Utils;
    if(Utils::getVar($footJavascripts) != null) {
        foreach($footJavascripts as $footJavascript) {
            ?>
        <script type="text/javascript" src="<?php echo $footJavascript; ?>"></script>
            <?php
        }
    }
?>
    </body>
</html>