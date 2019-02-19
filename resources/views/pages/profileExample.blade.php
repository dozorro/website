@extends('layouts.app')

@section('content')

    <div class="block_profile1 bg_grey">
        <div class="container">
            <div class="bg_white">
                <div class="row">
                    <div class="col-md-8">
                        <h1>Державне підприємтсво “Адміністрація морських портів України”</h1>
                    </div>
                    <div class="col-md-4 text-right">
                        <a class="link_print" href="#">Версія для друку</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-7">

                        <div class="list_info_profile inline-layout">
                            <div class="item user-info">
                                <div class="title">
                                    ТИП ЗАМОВНИКА
                                </div>
                                <div class="value">Замовник у розумінні закону (і)</div>
                            </div>
                            <div class="item tel-info">
                                <div class="title">
                                    ТЕЛЕФОН
                                </div>
                                <div class="value"><a href="tel:044-380-380-3">044-380-380-3</a></div>
                            </div>
                            <div class="item address-info">
                                <div class="title">
                                    ПОШТОВА АДРЕСА
                                </div>
                                <div class="value"><address>00001, м. Київ, вул. Пушкіна, буд. Колотушкіна 1а
                                    </address></div>
                            </div>
                            <div class="item email-info">
                                <div class="title">
                                    ЕЛЕКТРОННА АДРЕСА
                                </div>
                                <div class="value"><a href="mailto:ampu@gmail.com">ampu@gmail.com</a></div>
                            </div>


                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="block_rating_profile inline-layout">
                            <div class="rating_info">
                                <h4>Індекс DOZORRO
                                    <span class="info">
                                        <span class="info_icon2">?</span>
                                        <div class="info_text">
                                            <div>
                                                <p>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor</p>
                                                <p>
                                                    <a href="#">Детальніше</a>
                                                </p>
                                            </div>
                                        </div>
                                    </span>
                                </h4>

                                <ul class="rating_star inline-layout">
                                    <li class="active"></li>
                                    <li class="active"></li>
                                    <li class="active"></li>
                                    <li class="active"></li>
                                    <li class=""></li>
                                </ul>
                            </div>


                            <div class="rating">96%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="bg_grey block_statistic">
        <div class="container">
            <div class="bg_white">
                <form class="block_profile_tabs">
                    <div class="inline-layout">
                        <ul class="nav inline-layout">
                            <li class="active">
                                <div>Замовник<span>100500 тендерів</span></div>
                            </li>

                            <li>
                                <a href="/">Учасники<span>10 тендерів</span></a>
                            </li>

                        </ul>

                        <div class="form-holder">
                            <select>
                                <option>Выберите из списка</option>
                                <option>Выберите из списка</option>
                                <option>Выберите из списка</option>
                            </select>
                        </div>
                    </div>
                    <div class="tender-items">
                        <div class="inline-layout list_item_statistic">
                            <div class="item-container">
                                <div class="item item-dropdown">
                                    <span class="icon">
                                        <img src="/assets/images/item-dropdown.svg">
                                    </span>
                                    <div class="number text-center">4.1</div>
                                    <div class="title">Конкуренція</div>
                                    <div class="additional-metrics">
                                        <div class="metric">
                                            <span class="metric-title">Вартість пропозицій:</span>
                                            <span class="metric-value">100 тис</span>
                                            </div>
                                        <div class="metric">
                                            <span class="metric-title">Конверсія:</span>
                                            <span class="metric-value">31%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item-container">
                                <div class="item">
                                    <div class="number text-center">3</div>
                                    <div class="title">Конкуренція</div>
                                </div>
                            </div>
                            <div class="item-container">
                                <div class="item">
                                    <span class="icon">
                                        <img src="/assets/images/check.png" >
                                    </span>
                                    <div class="number text-center">3</div>
                                    <div class="title">Конкуренція</div>
                                </div>
                            </div>
                            <div class="item-container">
                                <div class="item">
                                    <span class="icon">
                                        <img src="/assets/images/setting.png" >
                                    </span>
                                    <div class="number text-center">3</div>
                                    <div class="title">Конкуренція</div>
                                </div>
                            </div>
                            <div class="item-container">
                                <div class="item item-dropdown">
                                    <span class="icon">
                                        <img src="/assets/images/item-dropdown.svg" >
                                    </span>
                                    <div class="number text-center">5</div>
                                    <div class="title">Проваджень ДАСУ</div>
                                    <div class="additional-metrics">
                                        <div class="metric">
                                            <span class="metric-title">Вартість пропозицій:</span>
                                            <span class="metric-value">100 тис</span>
                                            </div>
                                        <div class="metric">
                                            <span class="metric-title">Конверсія:</span>
                                            <span class="metric-value">31%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="#" class="link_pagination">Розгорнути всі 20</a>
                    </div>
                </form>




                <div class="row list_graph">
                    <div class="col-md-6">
                        <h3>НАЗВА графіку</h3>
                        <img src="/assets/images/grafik/grafik1.png" />
                    </div>

                    <div class="col-md-6">
                        <h3>рейтинги</h3>
                        <img src="/assets/images/grafik/grafik2.png" />
                    </div>
                </div>


                <div class="row list_graph block_table">
                    <div class="col-md-6">
                        <h3>Постачальники</h3>
                        <form>
                            <div class="inline-layout">
                                <div class="form-holder">
                                    <label>ВИД:</label>
                                    <select>
                                        <option>Список</option>
                                        <option>Діаграма</option>
                                    </select>
                                </div>

                                <div class="form-holder">
                                    <label>КІЛЬКІСТЬ</label>
                                    <select>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>75</option>
                                    </select>
                                </div>
                            </div>

                            <div class="overflow-table">
                                <table>
                                    <tbody>
                                        <tr>
                                            <th width="45%">
                                                Постачальник
                                            </th>
                                            <th width="16%">
                                                <a href="#" class="order_up">Кількість</a>
                                            </th>
                                            <th width="25%">Сумма контракта</th>
                                            <th width="14%">Доля</th>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">Назва постачальника послуг</a></td>
                                            <td>251</td>
                                            <td>1 355 690 грн.</td>
                                            <td>25%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </form>
                    </div>

                    <div class="col-md-6">
                        <h3>Предмети закупівлі</h3>
                        <form>
                            <div class="inline-layout">
                                <div class="form-holder">
                                    <label>ВИД:</label>
                                    <select>
                                        <option>Список</option>
                                        <option>Діаграма</option>
                                    </select>
                                </div>

                                <div class="form-holder">
                                    <label>КІЛЬКІСТЬ</label>
                                    <select>
                                        <option>25</option>
                                        <option>50</option>
                                        <option>75</option>
                                    </select>
                                </div>
                            </div>
                            <img src="/assets/images/grafik/grafik3.png" />
                        </form>

                    </div>
                </div>



                <div class="block_table">
                    <h3>ТЕНДЕРИ</h3>
                    <div class="overflow-table">
                        <table>
                            <tbody>
                            <tr>
                                <th width="14%">Замовник</th>
                                <th width="14%">
                                    <a href="#" class="order_down">Предмет закупівлі</a>
                                </th>
                                <th width="12%">Статус тендеру</th>
                                <th width="12%">Статус учасника</th>
                                <th width="12%">Очікувана вартість</th>
                                <th width="14%">Пропозиція переможця учасника</th>
                                <th width="12%"></th>
                                <th width="10%">Учасників</th>
                            </tr>
                            <tr>

                                <td><a href="#">Львівська філія</a></td>
                                <td>Футболки та сорочки
                                    (футболки, свитшоти)
                                    (для торгівлі).</td>
                                <td>Завершений</td>
                                <td>Дисквал</td>
                                <td>100 500 грн.</td>
                                <td>1 050</td>
                                <td>Вимог: 1<br>
                                    Скарг: 2</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <td><a href="#">Львівська філія</a></td>
                                <td>Футболки та сорочки
                                    (футболки, свитшоти)
                                    (для торгівлі).</td>
                                <td>Завершений</td>
                                <td>Дисквал</td>
                                <td>100 500 грн.</td>
                                <td>1 050</td>
                                <td>Вимог: 1<br>
                                    Скарг: 2</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <td><a href="#">Львівська філія</a></td>
                                <td>Футболки та сорочки
                                    (футболки, свитшоти)
                                    (для торгівлі).</td>
                                <td>Завершений</td>
                                <td>Дисквал</td>
                                <td>100 500 грн.</td>
                                <td>1 050</td>
                                <td>Вимог: 1<br>
                                    Скарг: 2</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <td><a href="#">Львівська філія</a></td>
                                <td>Футболки та сорочки
                                    (футболки, свитшоти)
                                    (для торгівлі).</td>
                                <td>Завершений</td>
                                <td>Дисквал</td>
                                <td>100 500 грн.</td>
                                <td>1 050</td>
                                <td>Вимог: 1<br>
                                    Скарг: 2</td>
                                <td>3</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <ul class="pagination pull-left">
                        <li class="disabled prev"><a href="#">Предидущая</a></li>
                        <li class="active"><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">5</a></li>
                        <li class="next"><a href="#">Следующая</a></li>
                    </ul>

                    <div class="block_download pull-right">
                        <a href="#" class="link_download">Завантажити таблицю</a>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="block_table">
                    <h3>Скарги</h3>
                    <div class="overflow-table">
                        <table>
                            <tbody>
                            <tr>
                                <th width="15%">
                                    Замовник
                                </th>
                                <th width="15%">
                                    <a href="#" class="order_up">Що оскаржувалось</a>
                                </th>
                                <th width="55%">Тендер</th>
                                <th width="15%">Результат</th>
                            </tr>
                            <tr>
                                <td><a href="#">ПАТ “Укрпошта”</a></td>
                                <td>Умови</td>
                                <td>UA-2017-12-12-002666-a - Футболки та сорочки (футболки, свитшоти) (для торгівлі).</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <td><a href="#">ПАТ “Укрпошта”</a></td>
                                <td>Умови</td>
                                <td>UA-2017-12-12-002666-a - Футболки та сорочки (футболки, свитшоти) (для торгівлі).</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <td><a href="#">ПАТ “Укрпошта”</a></td>
                                <td>Умови</td>
                                <td>UA-2017-12-12-002666-a - Футболки та сорочки (футболки, свитшоти) (для торгівлі).</td>
                                <td>3</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="block_table">
                    <h3>Контактні особи</h3>
                    <div class="overflow-table">
                        <table>
                            <tbody>
                            <tr>
                                <th width="33%">
                                    ПІБ
                                </th>
                                <th width="33%">
                                    <a href="#" class="order_up">Телефон</a>
                                </th>
                                <th width="33%">Email</th>

                            </tr>
                            <tr>
                                <td>Іваненко Іван Іванович</td>
                                <td>067 100 5000, 067 100 5000</td>
                                <td>ivanov@gmail.com</td>
                            </tr>
                            <tr>
                                <td>Іваненко Іван Іванович</td>
                                <td>067 100 5000, 067 100 5000</td>
                                <td>ivanov@gmail.com</td>
                            </tr>
                            <tr>
                                <td>Іваненко Іван Іванович</td>
                                <td>067 100 5000, 067 100 5000</td>
                                <td>ivanov@gmail.com</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="block_address">
                    <h3>Адреси</h3>
                    <address>м. Київ, вул Хрещатик 1, оф 25  <br>067 100 5001</address>
                </div>



            </div>
        </div>
    </div>

@endsection
