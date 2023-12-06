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

    </body>
</html>