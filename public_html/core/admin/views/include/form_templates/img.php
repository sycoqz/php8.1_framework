<div class="vg-wrap vg-element vg-full vg-box-shadow img_container img_wrapper">
    <div class="vg-wrap vg-element vg-half">
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-full vg-left">
                <span class="vg-header"><?=$this->warningUser[$row][0] ?: $row?></span>
            </div>
            <div class="vg-element vg-full vg-left">
                <span class="vg-text vg-firm-color5"></span><span class="vg_subheader"><?=$this->warningUser[$row][1]?></span>
            </div>
        </div>
        <div class="vg-wrap vg-element vg-full">
            <label for="<?=$row?>" class="vg-wrap vg-full file_upload vg-left">
                <span class="vg-element vg-full vg-input vg-text vg-left vg-button" style="float: left; margin-right: 10px">Выбрать</span>
                <input id="<?=$row?>" type="file" name="<?=$row?>" class="single_img" accept="image/*,image/jpeg,image/png,image/gif">
            </label>
        </div>
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-left img_show main_img_show">
            </div>
        </div>
    </div>
</div>
<div class="vg-wrap vg-element vg-full vg-box-shadow img_container img_wrapper">
    <div class="vg-wrap vg-element vg-half">
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-full vg-left">
                <span class="vg-header">main_img</span>
            </div>
            <div class="vg-element vg-full vg-left">
                <span class="vg-text vg-firm-color5"></span><span class="vg_subheader"></span>
            </div>
        </div>
        <div class="vg-wrap vg-element vg-full">
            <label for="<?=$row?>" class="vg-wrap vg-full file_upload vg-left">
                <span class="vg-element vg-full vg-input vg-text vg-left vg-button" style="float: left; margin-right: 10px">Выбрать</span>
                <a style="color:black" href="" class="vg-element vg-full vg-input vg-text vg-left vg-button vg_delete">
                    <span>Удалить</span>
                </a>
                <input id="<?=$row?>" type="file" name="<?=$row?>" class="single_img" accept="image/*,image/jpeg,image/png,image/gif">
            </label>
        </div>
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-left img_show main_img_show">
                <?php if (isset($this->data[$row])):?>
                    <img src="<?=PATH . UPLOAD_DIR . $this->data[$row]?>" alt="">
                <?php endif;?>
            </div>
        </div>
    </div>
</div>