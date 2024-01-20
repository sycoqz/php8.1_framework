<?php if (!empty($data)):?>
    <?php $date = $this->dateFormat($data['date'])?>
    <div class="news__item">
        <div class="news__item_date">
            <span class="bigtext"><?=$date['day']?></span>
            <span><?=$date['monthFormat']?><br><?=$date['year']?></span>
        </div>
        <div class="news__item_main">
            <div class="news__item_header"><?=$data['name']?></div>
            <div class="news__item_text"><?=$data['short_content']?></div>
            <div class="news__item_readmore readmore-underline"><a href="<?=$this->alias(['news' => $data['alias'] ?? ''])?>">Читать подробнее</a></div>
        </div>
    </div>
<?php endif;?>
