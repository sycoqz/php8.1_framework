<div class="vg-element vg-full vg-box-shadow img_wrapper">
    <div class="vg-wrap vg-element vg-full">
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-full vg-left">
                <span class="vg-header"><?=$this->warningUser[$row][0] ?: $row?></span>
            </div>
            <div class="vg-element vg-full vg-left">
                <span class="vg-text vg-firm-color5"></span><span class="vg_subheader"><?=$this->warningUser[$row][1] ?? ''?></span>
            </div>
        </div>
        <div class="vg-wrap vg-element vg-full gallery_container">
            <label class="vg-dotted-square vg-center" draggable="false">
                <img src="<?=PATH . ADMIN_TEMPLATE?>img/plus.png" alt="plus" draggable="false">
                <input class="gallery_img" style="display: none;" type="file" name="<?=$row?>[]" multiple="" accept="image/*,image/jpeg,image/png,image/gif" draggable="false">
            </label>
            <?php if (isset($this->data[$row])):?>
                <?php $this->data[$row] = json_decode($this->data[$row]);?>
                    <?php foreach ($this->data[$row] as $item):?>
                        <a href="<?=$this->adminPath . 'delete/' . $this->table . '/' . $this->data[$this->columns['id_row']] . '/' . $row . '/' . base64_encode($item)?>" class="vg-dotted-square vg-center" draggable="true">
                            <img class="vg_delete" src="<?=PATH . UPLOAD_DIR . $item?>" draggable="false" alt="">
                        </a>
                    <?php endforeach;?>
                    <?php
                        for ($i = 0; $i < 2; $i++) {

                            echo '<div class="vg-dotted-square vg-center empty_container" draggable="false"></div>';

                        } else:

                            for ($i = 0; $i < 11; $i++) {

                                echo '<div class="vg-dotted-square vg-center empty_container" draggable="false"></div>';

                            }
                    ?>
            <?php endif;?>
            <div class="vg-dotted-square vg-center empty_container" draggable="false"></div>
            <div class="vg-dotted-square vg-center empty_container" draggable="false"></div>
        </div>
    </div>
</div>

<!--<div class="vg-element vg-full vg-box-shadow img_wrapper">-->
<!--    <div class="vg-wrap vg-element vg-full">-->
<!--        <div class="vg-wrap vg-element vg-full">-->
<!--            <div class="vg-element vg-full vg-left">-->
<!--                <span class="vg-header">--><?php //=$this->warningUser[$row][0] ?: $row?><!--</span>-->
<!--            </div>-->
<!--            <div class="vg-element vg-full vg-left">-->
<!--                <span class="vg-text vg-firm-color5"></span><span class="vg_subheader">--><?php //=$this->warningUser[$row][1] ?? ''?><!--NEW</span>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="vg-wrap vg-element vg-full gallery_container">-->
<!--            <label class="vg-dotted-square vg-center" draggable="false">-->
<!--                <img src="--><?php //=PATH . ADMIN_TEMPLATE?><!--img/plus.png" alt="plus" draggable="false">-->
<!--                <input class="gallery_img" style="display: none;" type="file" name="--><?php //=$row?><!--[]" multiple="" accept="image/*,image/jpeg,image/png,image/gif" draggable="false">-->
<!--            </label>-->
<!--            <a href="" class="vg-dotted-square vg-center" draggable="true">-->
<!--                <img class="vg_delete" src="" draggable="false" alt="">-->
<!--            </a>-->
<!--            <a href="" class="vg-dotted-square vg-center" draggable="true">-->
<!--                <img class="vg_delete" src="" draggable="false" alt="">-->
<!--            </a>-->
<!--            <div class="vg-dotted-square vg-center empty_container" draggable="false"></div>-->
<!--            <div class="vg-dotted-square vg-center empty_container" draggable="false"></div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->