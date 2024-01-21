<div class="vg-element vg-full vg-box-shadow">
    <div class="vg-wrap vg-element vg-full vg-box-shadow">
        <div class="vg-element vg-full vg-left">
            <?php if (isset($row)):?>
                <span class="vg-header"><?=$this->warningUser[$row][0] ?: $row?></span>
            <?php endif;?>
        </div>
        <div class="vg-element vg-full vg-left">
            <span class="vg-text vg-firm-color5"></span><span class="vg_subheader"><?=$this->warningUser[$row][1] ?? ''?></span>
        </div>
        <div class="select-wrapper vg-element vg-full vg-left vg-no-offset">
            <div class="select-arrow-3 select-arrow-31"></div>
            <label>
                <select name="<?=$row?>" class="vg-input vg-text vg-full vg-firm-color1">
                    <?php foreach ($this->foreignData[$row] as $item):?>
                        <option value="<?=$item['id']?>" <?=($this->data[$row] ?? '') == $item['id'] ? 'selected' : ''?>>
                            <?=$item['name']?>
                        </option>
                    <?php endforeach;?>
                </select>
            </label>
        </div>
    </div>
</div>
