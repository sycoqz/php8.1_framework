<?php if (!empty($data)):?>
    <div class="container">

        <?php echo $this->breadcrumbs?>

        <h1 class="page-title h1"><?=$data['name']?></h1>
    </div>

    <section class="catalog-internal">
        <div class="container">
            <div class="catalog-internal-wrap">
                <?php if (empty($goods)):?>
                    <h2>По вашему запросу ничего не найдено.</h2>
                <?php else:?>
                    <aside class="catalog-aside">
                        <?php if (!empty($catalogFilters) || !empty($catalogPrices)):?>
                            <div class="catalog-aside__wrap">
                            <div class="catalog-aside-block">
                                <div class="catalog-aside-block__top">
                                    <div class="catalog-aside-block__title h2">
                                        Фильтры
                                    </div>
                                    <div class="catalog-aside-sort-mobile">
                                        <div class="catalog-aside-sort-mobile__button h2">
                                            сортировка
                                        </div>
                                    </div>
                                    <button class="catalog-filter-wrap__remove" onclick="location.href = location.pathname">Очистить все</button>
                                </div>
                                <div class="catalog-aside-block__content catalog-aside-block__drop">
                                    <div class="catalog-aside-block__drop-close">
                                        <svg viewBox="0 0 27.33 27.01" width="100%" height="100%">
                                            <path d="M26.69.32a1.08 1.08 0 0 0-1.54 0L.32 25.15a1.08 1.08 0 0 0 0 1.54 1.09 1.09 0 0 0 1.54 0L26.69 1.86a1.08 1.08 0 0 0 0-1.54z"></path>
                                            <path d="M27 25.15L1.88.32a1.1 1.1 0 0 0-1.56 0 1.08 1.08 0 0 0 0 1.54l25.12 24.83a1.13 1.13 0 0 0 .78.32 1.11 1.11 0 0 0 .78-.32 1.08 1.08 0 0 0 0-1.54z"></path>
                                        </svg>
                                    </div>
                                    <form action="<?=$this->alias('catalog' . (!empty($this->parameters['alias']) ? '/' . $this->parameters['alias'] : ''))?>" class="catalog-filter">
                                        <?php if (!empty($catalogPrices)):?>
                                            <div class="catalog-filter-item catalog-filter-item_open">
                                                <div class="catalog-filter-item__title">Цена<span class="catalog-filter-item__toggle"></span>
                                                </div>
                                                <div class="catalog-filter-item__content">
                                                    <div class="catalog-range-slider" style="margin-bottom: 20px">
                                                        <div class="catalog-filter-range__inputs">
                                                            <div class="catalog-filter-range__limit">
                                                                <div class="catalog-filter-range__text">От</div>
                                                                <label>
                                                                    <input name="min_price" type="text" value="<?php echo $catalogPrices['min_price']?>" class="catalog-filter-range__input js-rangeSliderMinimal">
                                                                </label>
                                                            </div>
                                                            <div class="catalog-filter-range__limit">
                                                                <div class="catalog-filter-range__text">До</div>
                                                                <label>
                                                                    <input name="max_price" value="<?php echo $catalogPrices['max_price']?>" type="text" class="catalog-filter-range__input js-rangeSliderMaximal">
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif;?>
                                        <?php if (!empty($catalogFilters)):?>
                                            <?php foreach ($catalogFilters as $item):?>
                                                <div class="catalog-filter-item">
                                                    <div class="catalog-filter-item__title"><?=$item['name']?><span class="catalog-filter-item__toggle"></span></div>
                                                    <div class="catalog-filter-item__content">
                                                        <ul class="catalog-filter-item__list">
                                                            <?php foreach ($item['values'] as $value):?>
                                                                <li class="catalog-filter-item__unit">
                                                                    <label class="catalog-filter-item__check checkbox">
                                                                        <input type="checkbox" name="filters[]" value="<?=$value['id']?>" <?=!empty($_GET['filters']) && in_array($value['id'], $_GET['filters']) ? 'checked' : ''?> class="checkbox__box visually-hidden">
                                                                        <span class="checkbox__span"></span>
                                                                        <span class="checkbox__text"><?=$value['name']?></span>
                                                                        <span class="checkbox__type">
                                                                        (<?=$value['count'] ?? 0?>)
                                                                    </span>
                                                                    </label>
                                                                </li>
                                                            <?php endforeach;?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                        <button class="card-item__btn">Применить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif;?>
                    </aside>
                    <section class="catalog-section catalog-section__four">
                        <div class="catalog-section-top">
                            <div class="catalog-section-top-items">
                                <?php if (!empty($order)):?>
                                    <div class="catalog-section-top-items__title catalog-section-top-items__unit">
                                        Сортировать по:
                                    </div>
                                    <?php $GET = $_GET ?? []?>
                                    <?php foreach ($order as $name => $item):?>
                                        <a href="<?=$this->alias('catalog/' . ($this->parameters['alias'] ?? '') . '/', array_merge($GET, ['order' => $item]))?>"
                                           class="catalog-section-top-items__unit catalog-section-top-items__toggle <?= str_ends_with($item, '_desc') ? 'order-desc' : ''?>">
                                            <?=$name?>
                                        </a>
                                    <?php endforeach;?>
                                <?php endif;?>
                                <div class="catalog-section-top-items__unit catalog-section-top-items__toggle">
                                    Показывать по:
                                </div>
                            </div>
                        </div>
                        <div class="catalog-section__wrapper">
                            <div class="catalog-section-items">
                                <div class="catalog-section-items__wrapper">
                                    <?php foreach ($goods as $item) {
                                        $this->showGoods($item, ['mainClass' => 'card-item card-item__internal', 'prefix' => 'card-item']);
                                    }?>
                                </div>
                            </div>
                        </div>
                        <div class="catalog-section-pagination">
                            <a href="catalog.html#" class="catalog-section-pagination__item catalog-section-pagination__prev">

                            </a>
                            <a href="catalog.html#" class="catalog-section-pagination__item">
                                1
                            </a>
                            <a href="catalog.html#" class="catalog-section-pagination__item">
                                2
                            </a>
                            <a href="catalog.html#" class="catalog-section-pagination__item">
                                3
                            </a>
                            <a href="catalog.html#" class="catalog-section-pagination__item">
                                4
                            </a>
                            <a href="catalog.html#" class="catalog-section-pagination__item">
                                5
                            </a>
                            <a href="catalog.html#" class="catalog-section-pagination__item catalog-section-pagination__next">

                            </a>
                        </div>
                    </section>
                <?php endif;?>
            </div>
        </div>
    </section>
<?php endif;?>