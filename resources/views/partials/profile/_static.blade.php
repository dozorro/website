

@if($object->setting && $object->setting->is_items)
    <div class="row list_graph block_table">
        <div class="col-md-6">
            <div class="title-container"><h3>Постачальники</h3></div>
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
            <div class="title-container"><h3>Предмети закупівлі</h3></div>
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
@endif

@if($object->setting && $object->setting->is_tenders)
    <div class="block_table">
        <div class="title-container"><h3>ТЕНДЕРИ</h3></div>
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

        @if($settings && $settings->is_export)
            <div class="block_download pull-right">
                <a href="#" class="link_download">Завантажити таблицю</a>
            </div>
        @endif

        <div class="clearfix"></div>
    </div>
@endif

@if($object->setting && $object->setting->is_compliants)
    <div class="block_table">
        <div class="title-container"><h3>Скарги</h3></div>
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
@endif

@if($object->setting && $object->setting->is_contacts)
    <div class="block_table">
        <div class="title-container"><h3>Контактні особи</h3></div>
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
@endif

@if($object->setting && $object->setting->is_addresses)
    <div class="block_address">
        <div class="title-container"><h3>Адреси</h3></div>
        <address>м. Київ, вул Хрещатик 1, оф 25  <br>067 100 5001</address>
    </div>
@endif
