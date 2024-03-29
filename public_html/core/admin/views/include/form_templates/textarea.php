<div class="vg-wrap vg-element vg-full vg-box-shadow">
    <div class="vg-wrap vg-element vg-full">
        <div class="vg-element vg-full vg-left">
            <?php if (isset($row)):?>
                <span class="vg-header"><?=($this->warningUser[$row][0] ?? '') ?: $row?></span>
            <?php endif;?>
        </div>
        <div class="vg-element vg-full vg-left">
            <span class="vg-text vg-firm-color5"><?=$this->warningUser[$row][1] ?? ''?></span><span class="vg_subheader"></span>
        </div>
    </div>
    <div class="vg-element vg-full">
        <div class="vg-element vg-full vg-left" style="flex-wrap: wrap">
            <div style="width: 100%; margin-bottom: 10px">
                <?php if (isset($class)):?>
                    <label>
                        <input type="checkbox" class="tinyMceInit" style="display: inline" autocomplete="on" <?=$class === 'vg-content' ? 'checked' : ''?>>Визуальный режим
                    </label>
                <?php endif;?>
                <label>
                    <textarea name="<?=$row?>" class="vg-input vg-text vg-full vg-firm-color1" style="width: 100%; margin-bottom: 10px"><?= isset($_SESSION['result'][$row]) ? htmlspecialchars($_SESSION['result'][$row]) : htmlspecialchars($this->data[$row] ?? '')?></textarea>
                </label>
            </div>
        </div>
    </div>
</div>

