@extends('layouts/app')

@section('content')

<div homepage>
    @foreach($blocks as $block)
        @include('partials.longread.' . $block->alias, [
            'data' => $block->value
        ])
    @endforeach

        <div class="bg_grey page-post">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">
                        <div class="page-post__info">
                            <h1 class="tender-header__h1 maxheight">
                                Реактиви та контрастні речовини для лабораторії діагностики
                            </h1>
                            <div class="link-more js-more">
                                <span class="show_more">Показати більше</span>
                                <span class="hide_more">Згорнути</span>
                            </div>
                            <ul class="tender_info_header inline-layout">
                                <li>ID тендера - UA-2017-01-03-000231-b</li>
                                <li>ДК:2015 - 33141420-0 — Основні неорганічні хімічні речовини</li>
                            </ul>
                            <div class="list_info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="list_info__item">
                                            <h4>Замовник:</h4>
                                            <p>Київська міська туберкульозна лікарня №1 з
                                                диспансерним відділенням</p>
                                        </div>
                                        <div class="list_info__item">
                                            <h4>ЄДРПОУ:</h4>
                                            <p>12345678</p>
                                        </div>


                                    </div>

                                    <div class="col-md-6">
                                        <div class="list_info__item">
                                            <h4>Контактна особа замовника:</h4>
                                            <p>Невдоба Тетяна Василівна, 380445607817, kmtl_t@ukr.net</p>
                                        </div>
                                        <div class="list_info__item">
                                            <h4>ТИп закупівлі:</h4>
                                            <p>Допорогові закупівлі</p>
                                        </div>


                                    </div>


                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="col-md-3">
                        <div class="page-post__sidebar">
                            <h4>Адреса замовника</h4>
                            <form class="list_item inline-layout">
                                <div class="item width100">
                                    <h5>Индекс:</h5>
                                    <input type="text" value="02094" disabled >
                                    <button class="edit_index"></button>
                                    <button class="hide">Зберегти</button>
                                </div>

                                <div class="item width50">
                                    <h5>Місто:</h5>
                                    <p>Київ</p>
                                </div>
                                <div class="item width50">
                                    <h5>Область:</h5>
                                    <p>Київська</p>
                                </div>
                                <div class="item width100">
                                    <h5>Адреса з тендеру:</h5>
                                    <p>Україна, Київська область, Київ,
                                        Харківське шосе 121/3</p>
                                </div>
                                <div class="item width100 checkbox">
                                    <input type="checkbox" value="" name="" id="checkbox">
                                    <label for="checkbox">Все вірно</label>
                                </div>
                            </form>

                        </div>




                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <div class="page-post__price ">
                            <div class="row">
                                <div class="item col-sm-6">
                                    <p>сума договору:</p>
                                    <price>59 996 uah</price>
                                </div>
                                <div class="item col-sm-6">
                                    <p>не розподілено:</p>
                                    <price class="color_main">59 996 uah</price>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="page-post__link_doc">
                            <a href="#" class="link_post_doc">Документи контракта</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form class="form_post">
                            <div class="overflow-table">
                                <table>
                                    <tbody>
                                    <tr>
                                        <th>Назва препарату</th>
                                        <th>Форма препарату</th>
                                        <th>Одиниця виміру</th>
                                        <th>Кількість</th>
                                        <th>Ціна за одиницю<br>
                                            (без ПДВ)</th>
                                        <th>Ціна за одиницю<br>
                                            (з ПДВ)</th>
                                        <th>Гранична ціна<br>
                                            (з ПДВ)</th>
                                        <th>Сумма<br>
                                            (без ПДВ)</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>
                                            Виявлені порушення
                                            <select class="hide">
                                                <option></option>
                                                <option>Флакон</option>
                                            </select>
                                        </td>
                                        <td>

                                            Розчин для інфузій, 9 мг на
                                            мл по 200 мл у пляшках № 1
                                            розчин для інфузій, 9 мг на
                                            мл по 200 мл у пляшках...
                                            <input type="text" class="hide max-width" value="Розчин для інфузій, 9 мг на">
                                        </td>
                                        <td>
                                            Флакон
                                            <select class="hide">
                                                <option></option>
                                                <option>Флакон</option>
                                            </select>
                                        </td>
                                        <td>
                                            100
                                            <input type="text" class="hide" value="100">
                                        </td>
                                        <td>
                                            <price>142 000 909.00</price>
                                            <input type="text" class="hide" value="142 000 909.00">
                                        </td>
                                        <td>
                                            <input type="text" class="hide" value="">
                                        </td>
                                        <td>
                                            10.00
                                            <input type="text" class="hide" value="10.00">
                                        </td>
                                        <td>
                                            <price>142 000 909.00</price>
                                            <input type="text" class="hide" value="142 000 909.00">
                                        </td>
                                        <td>
                                            <button class="edit-row js-edit-row"></button>
                                            <a href="#" class="delete-row"></a>
                                        </td>


                                    </tr>


                                    <tr>
                                        <td>
                                            Виявлені порушення
                                            <select class="hide">
                                                <option></option>
                                                <option>Флакон</option>
                                            </select>
                                        </td>
                                        <td>

                                            Розчин для інфузій, 9 мг на
                                            мл по 200 мл у пляшках № 1
                                            розчин для інфузій, 9 мг на
                                            мл по 200 мл у пляшках...
                                            <input type="text" class="hide max-width" value="Розчин для інфузій, 9 мг на">
                                        </td>
                                        <td>
                                            Флакон
                                            <select class="hide">
                                                <option></option>
                                                <option>Флакон</option>
                                            </select>
                                        </td>
                                        <td>
                                            100
                                            <input type="text" class="hide" value="100">
                                        </td>
                                        <td>
                                            <price>142 000 909.00</price>
                                            <input type="text" class="hide" value="142 000 909.00">
                                        </td>
                                        <td>
                                            <input type="text" class="hide" value="">
                                        </td>
                                        <td>
                                            10.00
                                            <input type="text" class="hide" value="10.00">
                                        </td>
                                        <td>
                                            <price>142 000 909.00</price>
                                            <input type="text" class="hide" value="142 000 909.00">
                                        </td>
                                        <td>
                                            <button class="edit-row js-edit-row"></button>
                                            <a href="#" class="delete-row"></a>
                                        </td>


                                    </tr>




                                    <tr>
                                        <td>
                                            <select>
                                                <option></option>
                                                <option>Флакон</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="max-width" value="" name="">
                                        </td>
                                        <td>
                                            <select>
                                                <option>

                                                </option>
                                                <option value="Флакон">Флакон</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" value="" name="">
                                        </td>
                                        <td>
                                            <input type="text" value="" name="">
                                        </td>
                                        <td>
                                            <input type="text" value="" name="">
                                        </td>
                                        <td>
                                            <input type="text" value="" name="">
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    </tbody>
                                </table>
                                <div class="list_button_add_row">
                                    <a href="#" class="">Додати рядок</a>
                                    <a href="#" class="reset">Скасувати</a>
                                </div>
                                <div class="list_button_form inline-layout">

                                    <div class="list_button_form__button">
                                        <button>Опублікувати</button>
                                        <a href="#" class="reset">Скасувати</a>
                                    </div>

                                    <div class="page-post__price ">
                                        <div class="item">
                                            <p>Не розподілено:</p>
                                            <price class="color_main">59 996 uah</price>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>

@endsection