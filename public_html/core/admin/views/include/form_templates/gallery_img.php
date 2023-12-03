<div class="vg-element vg-full vg-box-shadow img_wrapper">
    <div class="vg-wrap vg-element vg-full">
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-full vg-left">
                <span class="vg-header"><?=$this->warningUser[$row][0] ?: $row?></span>
            </div>
            <div class="vg-element vg-full vg-left">
                <span class="vg-text vg-firm-color5"></span><span class="vg_subheader"><?=$this->warningUser[$row][1]?></span>
            </div>
        </div>
        <div class="vg-wrap vg-element vg-full gallery_container">
            <label class="vg-dotted-square vg-center" draggable="false">
                <img src="<?=PATH . ADMIN_TEMPLATE?>img/plus.png" alt="plus" draggable="false">
                <input class="gallery_img" style="display: none;" type="file" name="<?=$row?>[]" multiple="" accept="image/*,image/jpeg,image/png,image/gif" draggable="false">
            </label>
            <?php if ($this->data[$row]):?>
                <?php $this->data[$row] = json_decode($this->data[$row]);?>
                    <?php foreach ($this->data[$row] as $item):?>
                        <a href="/admin/delete/goods/53/gallery_img/ODQwLTg0MDMxNjlfZG93bmxvYWQtc3ZnLWRvd25sb2FkLXBuZy1kb2N0b3ItZW1vamlfNjcyMGQyMTkucG5n" class="vg-dotted-square vg-center" draggable="true">
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

<div class="vg-element vg-full vg-box-shadow img_wrapper">
    <div class="vg-wrap vg-element vg-full">
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-full vg-left">
                <span class="vg-header"><?=$this->warningUser[$row][0] ?: $row?></span>
            </div>
            <div class="vg-element vg-full vg-left">
                <span class="vg-text vg-firm-color5"></span><span class="vg_subheader"><?=$this->warningUser[$row][1]?>NEW</span>
            </div>
        </div>
        <div class="vg-wrap vg-element vg-full gallery_container">
            <label class="vg-dotted-square vg-center" draggable="false">
                <img src="<?=PATH . ADMIN_TEMPLATE?>img/plus.png" alt="plus" draggable="false">
                <input class="gallery_img" style="display: none;" type="file" name="<?=$row?>[]" multiple="" accept="image/*,image/jpeg,image/png,image/gif" draggable="false">
            </label>
            <a href="/admin/delete/goods/53/new_gallery_img/ODQwLTg0MDMxNjlfZG93bmxvYWQtc3ZnLWRvd25sb2FkLXBuZy1kb2N0b3ItZW1vamlfMDNjYjAwNmQucG5n" class="vg-dotted-square vg-center" draggable="true">
                <img class="vg_delete" src="/userfiles/840-8403169_download-svg-download-png-doctor-emoji_03cb006d.png" draggable="false" alt="">
            </a>
            <a href="/admin/delete/goods/53/new_gallery_img/a2lzc3BuZy1lYXJyaW5nLWpld2VsbGVyeS1nZW1zdG9uZS1kaWFtb25kLWdvbGQtcmluZ3MtcG5nLWNsaXBhcnQtNWE3ODIzOTU0NGM0YjMyODg0NTUxMjE1MTc4MjI4NjkyODE3LnBuZw==" class="vg-dotted-square vg-center" draggable="true">
                <img class="vg_delete" src="/userfiles/kisspng-earring-jewellery-gemstone-diamond-gold-rings-png-clipart-5a78239544c4b32884551215178228692817.png" draggable="false" alt="">
            </a>
            <div class="vg-dotted-square vg-center empty_container" draggable="false"></div><div class="vg-dotted-square vg-center empty_container" draggable="false"></div>                    </div>
    </div>
</div>