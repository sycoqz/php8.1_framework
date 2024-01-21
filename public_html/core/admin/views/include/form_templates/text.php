<div class="vg-element vg-full vg-box-shadow">
    <div class="vg-wrap vg-element vg-full vg-box-shadow">
        <div class="vg-wrap vg-element vg-full">
            <div class="vg-element vg-full vg-left">
                <?php if (isset($row)):?>
                    <span class="vg-header"><?=$this->warningUser[$row][0] ?: $row?></span>
                <?php endif;?>
            </div>
            <div class="vg-element vg-full vg-left">
                <span class="vg-text vg-firm-color5"></span><span class="vg_subheader"><?=$this->warningUser[$row][1] ?? ''?></span>
            </div>
        </div>
        <div class="vg-element vg-full">
            <div class="vg-element vg-full vg-left ">
                <label>
                    <input type="text" name="<?=$row?>" class="vg-input vg-text vg-firm-color1" value="<?=isset($_SESSION['result'][$row]) ? htmlspecialchars($_SESSION['result'][$row]) : htmlspecialchars($this->data[$row] ?? '')?>" autocomplete="on">
                </label>
            </div>
        </div>
    </div>
</div>