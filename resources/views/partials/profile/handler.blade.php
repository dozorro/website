<script type="text/javascript">
    $(function() {

        var login = {{ !empty($user) ? 1 : 0 }};

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var opts = {
            lines: 13 // The number of lines to draw
            , length: 50 // The length of each line
            , width: 4 // The line thickness
            , radius: 3 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: 'red' // #rgb or #rrggbb or array of colors
            , opacity: 0.25 // Opacity of the lines
            , rotate: 0 // The rotation offset
            , direction: 1 // 1: clockwise, -1: counterclockwise
            , speed: 1 // Rounds per second
            , trail: 60 // Afterglow percentage
            , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            , zIndex: 2e9 // The z-index (defaults to 2000000000)
            , className: 'spinner' // The CSS class to assign to the spinner
            , top: '0' // Top position relative to parent
            , left: '50%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'absolute' // Element positioning
        }

        var target = document.getElementById('profile-spinner');
        var _spinner = new Spinner(opts).spin(target);
        var spinner = $('#profile-spinner .spinner');
        spinner.hide();

        $('.show-query').on('click', 'a', function(e) {
            e.preventDefault();

            var th = $(this);
            var url = th.attr('href');
            var tableBlock = th.closest('.block_table').find('.overflow-table');

            if(th.hasClass('active')) {
                return false;
            }

            spinner.show();

            th.parent().find('a').removeClass('active');
            th.addClass('active');

            $.ajax({
                method: 'POST',
                url: window.location.href,
                data: {
                    table: th.data('table'),
                    q: th.data('q'),
                },
                dataType: 'json',
                success: function (response) {
                    tableBlock.html(response.table);
                    spinner.hide();
                }
            });

            return false;
        });

        // $('.list_item_statistic .settings').on('click', 'img', function() {
        //     $('.list_item_statistic .item .form-holder').hide();
        //     //$('.list_item_statistic .item .settings').removeClass('open');

        //     if($(this).closest('span').hasClass('open')) {
        //         $(this).closest('span').removeClass('open');
        //         $(this).closest('.item').find('.form-holder').hide();
        //     } else {
        //         $(this).closest('span').addClass('open');
        //         $(this).closest('.item').find('.form-holder').show();
        //     }
        // });

        $('.block_profile_tabs ul.nav').on('click', 'a', function() {
            var href = $(this).attr('data-href');
            spinner.show();
            window.History.pushState(null, document.title, '{{ $profileRoute }}' + '/' + href);
            window.location.reload(true);
        });

        $('.metricsTplNew').on('click', 'a', function(e) {
            e.preventDefault();

            if(!login && $(this).attr('data-type') == 'custom') {
                //$('[data-formjs="open_login"]').trigger('click');
                return false;
            }

            var option = $(this).attr('data-tpl');
            var role = $(this).attr('data-role');
            var href = option+'/'+role;

            if(option == '' || option === undefined || option === null) {
                return false;
            }

            spinner.show();

            $.ajax({
                method: 'POST',
                data: {
                    tpl: option,
                    role: role,
                },
                url: '{{ $saveTemplateRoute }}',
                dataType: 'json',
                success: function (response) {
                    //console.log(response);
                    window.History.pushState(null, document.title, '{{ $profileRoute }}' + '/' + href);
                    window.location.reload(true);
                }
            });
        });

        $('.metricsTpl').on('change', function() {

            if(!login && $(this).find('option:selected').attr('data-type') == 'custom') {
                //$('[data-formjs="open_login"]').trigger('click');
                return false;
            }

            var option = $(this).find('option:selected').val();

            if(option === '' || option === undefined || option === null) {
                return false;
            }

            spinner.show();

            var tab = $(this).closest('.tab-pane');
            var role = tab.attr('id');
            var href = option+'/'+role;

            $.ajax({
                method: 'POST',
                data: {
                    tpl: option,
                    role: role,
                },
                url: '{{ $saveTemplateRoute }}',
                dataType: 'json',
                success: function (response) {
                    //console.log(response);
                    window.History.pushState(null, document.title, '{{ $profileRoute }}' + '/' + href);
                    //tab.attr('data-href', href);
                    window.location.reload(true);
                }
            });
        });


        $('.profile-save').on('click', function(e) {
            e.preventDefault();

            if(!login) {
                $('[data-formjs="open_login"]').trigger('click');
                $('[data-profile]').show();
                //return false;
            }

            spinner.show();

            var role;
            var tab;
            var metrics = [];

            $('.metrics').each(function(index,element) {
                var th = $(this);
                var item = th.closest('.item');
                var option = th.find('option:selected');
                var column = item.data('column');
                var row = item.closest('.groupMetricsData').data('row');

                var metric = {
                    column: column,
                    row: row,
                    code: option.val()
                }

                tab = th.closest('.tab-pane');
                role = tab.attr('id');

                metrics.push(metric);
            });

                $.ajax({
                    method: 'POST',
                    data: {
                        metrics: metrics,
                        role: role
                    },
                    url: '{{ $saveCustomTemplateRoute }}',
                    dataType: 'json',
                    success: function (response) {
                        //console.log(response);
                        if(response.data) {
                            var href = response.data + '/' + role;
                            window.History.pushState(null, document.title, '{{ $profileRoute }}' + '/' + href);
                            tab.closest('.block_profile_tabs').find('.nav.inline-layout li.active a').attr('data-href', href);

                            /*if (!item.hasClass('item-dropdown')) {
                                item.addClass('item-dropdown');
                            }*/
                        }

                        spinner.hide();
                    }
                });
        });

        $('.metrics').on('change', function() {
            //$('.metrics').removeClass('to-save');
            //$(this).addClass('to-save');
            $('.profile-save').show();

            var th = $(this);
            var option = th.find('option:selected');
            var item = th.closest('.item');
            var column = item.data('column');
            var row = item.closest('.groupMetricsData').data('row');
            var tab = th.closest('.tab-pane');
            var tpl = tab.find('.metricsTpl option:selected').val();

            item.find('.second-metric .metric-title').html(option.data('second-label'));
            item.find('.second-metric .metric-value').html(option.data('second'));
            item.find('.third-metric .metric-title').html(option.data('third-label'));
            item.find('.third-metric .metric-value').html(option.data('third'));
            item.find('.number').html(option.data('value'));
            item.find('.title').html(option.text());

            // $(this).closest('.item').find('.settings').removeClass('open');
            // $(this).closest('.form-holder').hide();

            tab.find('.metricsTpl option[data-type="custom"]').attr('selected','selected');

            if($('.metricsTplNew a[data-type="custom"]').html() !== undefined) {
                $('.metricsTplNew li').removeClass('active');
                $('.metricsTplNew a[data-type="custom"]').parent().addClass('active');
            }

            if($('.metricsTplNew a[data-type="custom"]').html() !== undefined) {
                $('.metricsTplNew li').removeClass('active');
                $('.metricsTplNew a[data-type="custom"]').parent().addClass('active');
            }

            $('.show-feedback-row').show();
        });

        $('.overflow-table').on('click', '.order_up, .order_down', function(e) {
            e.preventDefault();

            var th = $(this);
            var col = th.closest('th');

            sortTable(th.closest('table').get(0), col.index(), th.hasClass('order_up'));

            th.toggleClass('order_up');
            th.toggleClass('order_down');
        });

        function sortTable(table, col, reverse) {
            var tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
                    tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
                    i;
            reverse = -((+reverse) || -1);
            tr = tr.sort(function (a, b) { // sort rows

                //console.log(parseInt(a.cells[col].textContent.trim()*0));

                if(isNaN(parseInt(a.cells[col].textContent.trim()*0))) {
                    return reverse // `-1 *` if want opposite order
                            * (a.cells[col].textContent.trim() // using `.textContent.trim()` for test
                                            .localeCompare(b.cells[col].textContent.trim())
                            );
                } else {

                    if(isNaN(parseInt(b.cells[col].textContent.trim()*0))) {
                        b.cells[col].textContent = 0;
                    }

                    return reverse // `-1 *` if want opposite order
                            * ((parseInt(a.cells[col].textContent.trim()) // using `.textContent.trim()` for test
                                    <= parseInt(b.cells[col].textContent.trim()) ? 1 : -1)
                            );
                }
            });

            for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]); // append each row in order
        }

    })
</script>