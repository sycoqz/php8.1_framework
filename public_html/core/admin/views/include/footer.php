    </div><!--.vg-main.vg-right-->
</div><!--.vg-carcass-->
        <div class="vg-modal vg-center">
                <?php
                    if (isset($_SESSION['result']['answer'])) {

                        echo $_SESSION['result']['answer'];
                        unset($_SESSION['result']);
                    }
                ?>
        </div>
        <script>
                const PATH = '<?=PATH?>';
                const ADMIN_MODE = 1;
                const tinyMceDefaultAreas = '<?=isset($this->blocks['vg-content']) ? implode(',', $this->blocks['vg-content']) : ''?>';
        </script>
        <?php $this->getScripts();?>
</body>
</html>