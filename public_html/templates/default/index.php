<?php if (!empty($sales)):?>
<section class="slider">
    <div class="slider__container swiper-container">
        <form class="search search-internal" action="<?=$this->alias('search')?>">
            <button>
                <svg class="inline-svg-icon svg-search">
                    <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#search"></use>
                </svg>
            </button>
            <label class="search search-internal">
                <input type="search" name="search" placeholder="Поиск по каталогу" autocomplete="off" autocapitalize="off" spellcheck="false">
            </label>
        </form>
        <div class="slider__wrapper swiper-wrapper">
            <?php foreach ($sales as $item):?>
                <a href="<?=$this->alias($item['external_alias'])?>" class="slider__item swiper-slide" style="text-decoration: none">
                    <div class="slider__item-description">
                        <div class="slider__item-prev-text"><?=$item['sub_title']?></div>
                        <div class="slider__item-header">
                            <?php foreach (preg_split('/\s+/', $item['name'], 0, PREG_SPLIT_NO_EMPTY) as $value):?>
                                <span><?=$value?></span>
                            <?php endforeach;?>
                        </div>
                        <div class="slider__item-text">
                            <?=$this->clearStr($item['short_content'])?>
                        </div>
                        <div class="slider__item-logos">
                            <?php if (!empty($this->set['img_years']) && !empty($this->set['number_of_years'])):?>
                                <div class="slider__item-15yrs">
                                    <img src="<?=$this->img($this->set['img_years'])?>" alt="">
                                    <p><span><?=$this->wordsForCounter($this->set['number_of_years'])?></span>на рынке</p>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="slider__item-image">
                        <img src="<?=$this->img($item['img'])?>" alt="">
                    </div>
                </a>
            <?php endforeach;?>
        </div>

        <div class="slider__pagination swiper-pagination"></div>
        <div class="slider__controls controls _prev swiper-button-prev">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
        <div class="slider__controls controls _next swiper-button-next">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
</section>
<?php endif;?>

<?php if (!empty($this->menu['catalog'])):?>
<section class="catalog">
    <div class="division-internal__items">
        <?php foreach ($this->menu['catalog'] as $item):?>
            <a href="<?=$this->alias(['catalog' => $item['alias']])?>" class="division-internal-item">
                  <span class="division-internal-item__title">
                    <?=$item['name']?>
                  </span>
                <span class="division-internal-item__arrow-stat">
                <svg>
                  <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow-right"></use>
                </svg>
              </span>
                            <span class="division-internal-item__arrow">
                <img src="<?=PATH . TEMPLATE?>assets/img/divisions/devision-arrow.png" alt="">
              </span>
            </a>
        <?php endforeach;?>
    </div>
</section>
<?php endif;?>
<?php if (!empty($goods) && !empty($arrHits)):?>
<section class="offers">
    <div class="offers__tabs">
        <ul class="offers__tabs_header">
            <?php $activeItem = -1?>
            <?php foreach ($arrHits as $key => $item):?>
                <?php if (!empty($goods[$key])):?>
                    <li class="<?=!++$activeItem ? 'active' : ''?>">
                        <div class="icon-offer"><?=$item['icon']?></div><?=$item['name']?>
                    </li>
                <?php endif;?>
            <?php endforeach;?>
        </ul>
        <?php $activeItem = -1?>
        <?php foreach ($arrHits  as $key => $value):?>
            <?php if (!empty($goods[$key])):?>
                <div class="offers__tabs_content <?=!++$activeItem ? 'active' : ''?>">
                    <div class="offers__tabs_subheader subheader">
                        <?=$value['name']?>
                    </div>
                    <div class="offers__tabs_container swiper-container">
                        <div class="offers__tabs_wrapper swiper-wrapper">
                            <?php foreach ($goods[$key] as $item) {

                                $this->showGoods($item, ['icon' => $value['icon']]);

                            }?>
                        </div>
                    </div>
                    <a href="<?=$this->alias('catalog')?>" class="offers__readmore readmore">Смотреть каталог</a>
                </div>
            <?php endif;?>
        <?php endforeach;?>
        <div class="offers__controls controls _prev">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
        <div class="offers__controls controls _next">
            <svg>
                <use xlink:href="<?=PATH . TEMPLATE?>assets/img/icons.svg#arrow"></use>
            </svg>
        </div>
    </div>
</section>
<?php endif;?>


<div class="horizontal">
    <div class="horizontal__wrapper">
        <section class="about">
            <div class="about__description">
                <div class="about__description_name subheader"><?=$this->set['name']?></div>
                <div class="about__description_text">
                    <?=$this->set['short_content']?>
                </div>
                <a href="<?=$this->alias('about')?>" class="about__description_readmore readmore">Читать подробнее</a>
            </div>
            <div class="about__image">
                <img src="<?=$this->img($this->set['promo_img'])?>" alt="<?=$this->set['name']?>">
            </div>
        </section>
        <?php if (!empty($advantages)):?>
            <section class="advantages">
                <div class="advantages__name subheader">Наши преимущества</div>
                <div class="advantages__wrapper">
                    <?php $counter = 0?>
                    <?php foreach ($advantages as $item):?>
                        <?php if (!($counter % 3)):?>
                            <div class="advantages__row <?=!$counter ? 'advantages__row_left' : 'advantages__row_right'?>">
                        <?php endif;?>
                        <?php $counter++?>
                                <div class="advantages__item">
                                    <div class="advantages__item_header"><?=$item['name']?></div>
                                    <img src="<?=$this->img($item['img'])?>" class="advantages__item_image" alt="">
                                </div>
                        <?php if (!($counter % 3)):?>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                    <?php if ($counter % 3):?>
                        </div>
                    <?php endif;?>
                </div>
            </section>
        <?php endif;?>
    </div>
</div>

<section class="feedback ">
    <div class="feedback__name subheader ">Остались вопросы</div>
    <form method="post" action="<?=$this->alias('feedback')?>" class="feedback__form">
        <div class="feedback__form_left">
            <label>
                <input type="text" class="input-text feedback__input" placeholder="Ваше имя">
            </label>
            <label>
                <input type="email" class="input-text feedback__input" placeholder="E-mail">
            </label>
            <label>
                <input type="text" class="input-text feedback__input js-mask-phone" placeholder="Телефон">
            </label>
        </div>
        <div class="feedback__form_right">
            <label>
                <textarea class="input-textarea feedback__textarea" placeholder="Ваш вопрос"></textarea>
            </label>
        </div>
        <div class="feedback__privacy">
            <label class="checkbox">
                <input type="checkbox" />
                <span class="checkbox__text">Соглашаюсь с правилами обработки персональных данных</span>
            </label>
        </div>
        <button type="submit" class="form-submit feedback__submit">Отправить</button>
    </form>
</section>

<?php if (!empty($news)):?>
    <section class="news">
    <div class="news__name subheader">Новости</div>
    <div class="news__wrapper">

        <?php foreach ($news as $item) {

            $this->showGoods($item, [], 'newsItem');

        }?>
    </div>
    <a href="<?=$this->alias('news')?>" class="news__reasdmore readmore">Смотреть все</a>
</section>
<?php endif;?>

<script>

    document.querySelector('[name="search"]').addEventListener('input', function () {

        let value = this.value.trim()

        $.ajax({
            data: {
                ajax: 'search',
                search: value
            },
            success: result => {
                console.log(result)
            }
        })


    })

</script>