(function(){
    var el=document.getElementById('vue-indicators');
    
    if(el) {
        Vue.config.debug = true;

        var app = new Vue({
            components: {
                'spinner': VueSimpleSpinner
            },
            directives: {
                'sticky': VueSticky.default
            },
            el: '#vue-indicators',
            data: {
                total: 0,
                isMobile: false,
                filters: null,
                apiUrl: null,
                tenders: [],
                lots: [],
                tender: null,
                tenderKey: null,
                page: {
                    current: 1,
                    onPage: 10
                },
                toggledLots: {},
                remoteLots: {},
                loadingLots: {},
                toggled: {
                    common: true,
                    procedure: true,
                    dates: true,
                    risks: true,
                    customer: true,
                    documents: true,
                    items: true,
                    qualifications: true,
                    contracts: true,
                    questions: true,
                    complaints: true,
                    ngo: true,
                    prequalifications: true,
                    reviews: true,
                    features: true,
                    ngo_forms: true
                },
                remote: {
                    documents: [],
                    qualifications: [],
                    contracts: [],
                    questions: [],
                    complaints: [],
                    ngo: [],
                    prequalifications: [],
                    reviews: [],
                    features: [],
                },
                loading: {
                    documents: false,
                    qualifications: false,
                    contracts: false,
                    questions: false,
                    complaints: false,
                    ngo: false,
                    prequalifications: false,
                    reviews: false,
                    features: false,
                },
                nextPageLoading: false,
                sendingData: false,
                pagination: false,
                riskKey: null
            },
            methods: {
                exampleRequest: function(uri) {
                    if(typeof window.History.pushState === 'function') {
                        window.History.pushState(null, document.title, '/search' + uri);
                        document.getElementById('total-tenders-span').classList.remove('hide');
                        this.search(uri);
                    }
                },
                showInfoBox: function(rKey) {
                    if(this.riskKey == rKey) {
                        this.riskKey = null;
                    } else {
                        this.riskKey = rKey;
                    }
                },
                showSidebar: function(key, click) {
                    this.tenderKey=key;
                    this.tender=this.tenders[key];

                    this.$nextTick(function () {
                        //console.log(this.$refs.sidebarScroll);
                        this.$refs.sidebarScroll.scrollTop=0;
                    });                    
                    
                    this.resetRemoteToggle();

                    var tenderPage = false;

                    if(window.location.pathname.indexOf('/tender/') > -1) {
                        tenderPage = true;
                    }

                    if(!tenderPage && this.apiUrl.indexOf('/indicators') > -1) {
                        window.History.pushState(null, document.title, '/indicators/' + this.tender.tenderID);
                    }

                    if(this.isMobile && click !== undefined) {
                        document.getElementById('tenders-table').classList.add('hide');
                    }

                    document.getElementsByTagName('body')[0].classList.remove('loading');
                },
                closeSidebar: function(id) {
                    this.tender=null;

                    if(this.isMobile) {
                        document.getElementById('tenders-table').classList.remove('hide');
                    }
                },
                getQueries: function() {
                    var params = {};
                    var url = '';

                    if(this.filters == 'true') {
                        var contractsActive = document.getElementsByClassName('contract_active-data-selected')[0].getElementsByClassName('selected-tag');
                        var suppliersActive = document.getElementsByClassName('supplier_active-data-selected')[0].getElementsByClassName('selected-tag');
                        var regions = document.getElementsByClassName('region-selected')[0].getElementsByClassName('selected-tag');
                        var cpv = document.getElementsByClassName('cpv-data-selected')[0].getElementsByClassName('selected-tag');
                        var customers = document.getElementsByClassName('edrpou-data-selected')[0].getElementsByClassName('selected-tag');
                        var tenderers = document.getElementsByClassName('tenderer_edrpou-data-selected')[0].getElementsByClassName('selected-tag');
                        var suppliers = document.getElementsByClassName('supplier_edrpou-data-selected')[0].getElementsByClassName('selected-tag');
                        var risks = document.getElementsByName('risks');
                        var statuses = document.getElementsByName('status');
                        var forms = document.getElementsByName('forms');
                        var procs = document.getElementsByName('proc_type');
                        var formsChecked = [];
                        var cpvChecked = [];
                        var cpvLikeChecked = [];
                        var regionsChecked = [];
                        var statusesChecked = [];
                        var risksChecked = [];
                        var procsChecked = [];
                        var customersChecked = [];
                        var tenderersChecked = [];
                        var suppliersChecked = [];
                        var suppliersActiveChecked = [];
                        var contractsActiveChecked = [];
                        var q = '';
                        var sort = '';
                        var order = '';
                        var tid = '';

                        for(var i = 0; i < contractsActive.length;i++) {
                            contractsActiveChecked.push(contractsActive[i].getAttribute('data-id'));
                        }
                        for(var i = 0; i < regions.length;i++) {
                            regionsChecked.push(regions[i].getAttribute('data-id'));
                        }
                        for(var i = 0; i < suppliersActive.length;i++) {
                            suppliersActiveChecked.push(suppliersActive[i].getAttribute('data-id'));
                        }
                        for(var i = 0; i < suppliers.length;i++) {
                            suppliersChecked.push(suppliers[i].getAttribute('data-id'));
                        }
                        for(var i = 0; i < tenderers.length;i++) {
                            tenderersChecked.push(tenderers[i].getAttribute('data-id'));
                        }
                        for(var i = 0; i < cpv.length;i++) {
                            var cpvItem = cpv[i].getAttribute('data-id');

                            if(cpvItem.length == 3 && !isNaN(parseInt(cpvItem))) {
                                cpvLikeChecked.push(cpvItem);
                            } else {
                                cpvChecked.push(cpvItem);
                            }
                        }
                        for(var i = 0; i < customers.length;i++) {
                            customersChecked.push(customers[i].getAttribute('data-id'));
                        }
                        for(var i = 0; i < forms.length;i++) {
                            if(forms[i].checked) {
                                formsChecked.push(forms[i].value);
                            }
                        }
                        for(var i = 0; i < statuses.length;i++) {
                            if(statuses[i].checked) {
                                statusesChecked.push(statuses[i].value);
                            }
                        }
                        for(var i = 0; i < risks.length;i++) {
                            if(risks[i].checked) {
                                risksChecked.push(risks[i].value);
                            }
                        }
                        for(var i = 0; i < procs.length;i++) {
                            if(procs[i].checked) {
                                procsChecked.push(procs[i].value);
                            }
                        }

                        if(tenderersChecked.length == 1) {
                            params.tenderer_edrpou = tenderersChecked.join('');
                        }
                        else if(tenderersChecked.length > 1) {
                            params.tenderer_edrpou_all = tenderersChecked.join('+');
                        }
                        if(suppliersChecked.length == 1) {
                            params.supplier_edrpou = suppliersChecked.join('');
                        }
                        else if(suppliersChecked.length > 1) {
                            params.supplier_edrpou_all = suppliersChecked.join('+');
                        }
                        if(suppliersChecked.length) {
                            if(procsChecked.indexOf('negotiation') <= -1) {
                                procsChecked.push('negotiation');
                            }
                            if(procsChecked.indexOf('negotiation.quick') <= -1) {
                                procsChecked.push('negotiation.quick');
                            }
                            if(procsChecked.indexOf('reporting') <= -1) {
                                procsChecked.push('reporting"');
                            }
                        }
                        if(suppliersActiveChecked.length > 0) {
                            params.supplier_active = suppliersActiveChecked;
                        }
                        if(contractsActiveChecked.length > 0) {
                            params.contract_active = contractsActiveChecked;
                        }
                        if(customersChecked.length > 0) {
                            params.edrpou = customersChecked;
                        }
                        if(cpvChecked.length > 0) {
                            params.cpv = cpvChecked;
                        }
                        if(cpvLikeChecked.length > 0) {
                            params.cpv_like = cpvLikeChecked;
                        }
                        if(regionsChecked.length > 0) {
                            params.region = regionsChecked;
                        }
                        if(statusesChecked.length > 0) {
                            params.status = statusesChecked;
                        }
                        if(procsChecked.length > 0) {
                            params.proc_type = procsChecked;
                        }

                        if(document.getElementById('any-risks').checked) {
                            params.risk_code_like = 'R';
                        } else {
                            if (risksChecked.length == 1) {
                                params.risk_code = risksChecked.join('');
                            }
                            else if (risksChecked.length > 1) {
                                params.risk_code_all = risksChecked.join('+');
                            }
                        }

                        if(formsChecked.length > 0) {
                            params.form_code = formsChecked;
                        }/*
                        else if(formsChecked.length > 1) {
                            params.form_code_all = formsChecked.join('+');
                        }*/
                        if(document.getElementById('searchByTid').value) {
                            tid = document.getElementById('searchByTid').value.trim();

                            if(tid.indexOf('UA-') > -1 && tid.indexOf(' ') > -1) {
                                params.tid = tid.split(' ').join('+');
                            }
                            else if(tid.indexOf('UA-') > -1) {
                                params.tid = tid;
                                window.location.href = '/tender/'+tid;
                                return false;
                            } else {
                                params.query = tid;
                            }
                        }

                        if(document.getElementById('date1').value) {
                            params.tender_start = document.getElementById('date1').value;
                        }
                        if(document.getElementById('date2').value) {
                            params.tender_end = document.getElementById('date2').value;
                        }

                        if(document.getElementById('price1').value) {
                            params.value = document.getElementById('price1').value;
                        }

                        if(document.getElementById('price2').value) {
                            if(params.value === undefined) {
                                params.value = '0-';
                            } else {
                                params.value += '-';
                            }

                            params.value += document.getElementById('price2').value;
                        }

                        var isEmpty = true;

                        for(var prop in params) {
                            if(params.hasOwnProperty(prop)) {
                                isEmpty = false;
                                break;
                            }
                        }

                        if(!isEmpty && document.getElementsByName('sort')[0] !== null) {
                            var t = document.getElementsByName('sort')[0].value.split('-');
                            sort = t[0];
                            order = t[1];

                            params.sort = sort;
                            params.order = order;
                        }

                        url = Object.keys(params).map(function(k) {
                            if( k == 'contract_active' || k == 'supplier_active' || k == 'proc_type' || k == 'status' ||
                                k == 'cpv_like' || k == 'cpv' || k == 'region' || k == 'edrpou' || k == 'form_code' ||
                                (k == 'risk_code' && document.getElementById('any-risks').checked)) {
                                var q = '';

                                for(var i in params[k]) {

                                    if(q != '') { q += '&'; }

                                    if(params[k][i].indexOf(',') > -1) {
                                        var data = params[k][i].split(',');

                                        for(var j in data) {

                                            if(q != '') { q += '&'; }

                                            q += k + '=' + data[j];
                                        }
                                    } else {
                                        q += k + '=' + params[k][i];
                                    }
                                }

                                return q;
                            } else {
                                return k + '=' + params[k];
                            }
                        }).join('&').replace('&&', '&');

                        if(typeof window.History.pushState === 'function') {
                            window.History.pushState(null, document.title, '/search/?' + url);
                            document.getElementById('total-tenders-span').classList.remove('hide');
                        }
                    }

                    return url;
                },
                searchFilters: function(type, tenderer) {

                    var q = document.getElementsByName(type)[0].value;
                    var postType = type;

                    if(type.indexOf('edrpou') > -1 || type.indexOf('supplier') > -1 || type.indexOf('contract') > -1) {
                        postType = 'edrpou';
                    }

                    axios.post('/form/autocomplete/'+postType, {
                        query: q,
                        tenderer: tenderer
                    }).then(response => {
                        if(response.data && response.data.length) {

                            var html = '';
                            var cpv_group = postType == 'cpv' && q.length == 3 && !isNaN(parseInt(q));

                            for(var i in response.data) {

                                var name = response.data[i].name;

                                if(postType == 'cpv') {
                                    name = response.data[i].id+' '+name;
                                }

                                if(!cpv_group || i > 0) {
                                    html += '<div data-id="' + response.data[i].id + '">' + name + '</div>';
                                } else {
                                    html += '<div data-id="' + q + '">' + name + '</div>';
                                }
                            }

                            document.getElementById(type+'-data').innerHTML = html;
                            document.getElementById(type+'-data-selectize').classList.remove('hide');
                        } else {
                            document.getElementById(type+'-data-selectize').classList.add('hide');
                        }
                    });
                },
                search: function(url) {
                    if(!this.sendingData) {
                        if(!url) {
                            url = this.getQueries();

                            if(!url) {
                                this.closeSidebar();
                                document.getElementById('closeFilters').click();
                                document.getElementsByTagName('body')[0].classList.remove('loading');
                                this.sendingData=false;
                                this.tenders = [];
                                return;
                            }
                        }

                        this.closeSidebar();
                        document.getElementById('closeFilters').click();
                        document.getElementsByTagName('body')[0].classList.add('loading');

                        axios.post(this.apiUrl, {
                            query: url
                        }).then(response => {

                            document.getElementsByTagName('body')[0].classList.remove('loading');
                            this.sendingData=false;
                            this.tenders = [];

                            if(response.data.tenders) {
                                this.total = response.data.total;
                            } else {
                                this.total = 0;
                            }

                            if(response.data.tenders && response.data.tenders.length){
                                var data=this.prependRemoteData(response.data.tenders);

                                for(var i=0;i<data.length;i++){
                                    this.tenders.push(data[i]);

                                    if(data[i].__isMultiLot) {
                                        for(var j=0;j<data[i].lots.length;j++) {
                                            var lot = data[i].lots[j];

                                            Vue.set(this.lots, lot.id, {});

                                            this.lots[lot.id] = lot;
                                        }
                                    }
                                }

                                this.showSidebar(0);

                                this.pagination = true;
                            } else {
                                this.pagination = false;
                            }
                        });
                    }
                },
                nextPage: function() {
                    if(!this.sendingData) {
                        this.nextPageLoading=true;

                        axios.post(this.apiUrl, {
                            start: this.page.current*this.page.onPage,
                            query: this.getQueries()
                        }).then(response => {
                            this.nextPageLoading=false;
                            this.sendingData=false;

                            if(response.data.tenders && response.data.tenders.length){
                                var data=this.prependRemoteData(response.data.tenders);

                                for(var i=0;i<data.length;i++){
                                    this.tenders.push(data[i]);

                                    if(data[i].__isMultiLot) {
                                        for(var j=0;j<data[i].lots.length;j++) {
                                            var lot = data[i].lots[j];

                                            Vue.set(this.lots, lot.id, {});

                                            this.lots[lot.id] = lot;
                                        }
                                    }
                                }

                                this.page.current++;
                                this.pagination = true;
                            } else {
                                this.pagination = false;
                            }
                        });
                    }
                },
                toggle: function(block, lotId) {
                    if(lotId) {
                        this.toggledLots[lotId][block] = !this.toggledLots[lotId][block];
                    } else {
                        this.toggled[block] = !this.toggled[block];
                    }
                },
                toggleRemote: function(block, lotId){
                    //console.log(this.tenders[this.tenderKey].remote[block]);
                    //console.log(this.tenders[this.tenderKey].remote[block].length);

                    if (window.getSelection && window.getSelection().toString()) {
                        return window.getSelection().toString();
                    } else if (document.selection && document.selection.createRange().text) {
                        return document.selection.createRange().text;
                    }

                    if(
                        (!lotId && this.tenders[this.tenderKey].remote[block].length === 0)
                        ||
                        (lotId && this.lots[lotId].remote[block].length === 0)
                    ) {
                        if(!this.sendingData){

                            if(lotId) {
                                this.loadingLots[lotId][block] = true;
                            } else {
                                this.loading[block] = true;
                            }

                            this.sendingData=true;
                            this.toggle(block, lotId);

                            axios.post('/api/sidebar/'+block, {
                                id: this.tender.tenderID,
                                lot_id: lotId
                            }).then(response => {
                                this.sendingData=false;

                                if(lotId) {
                                    this.loadingLots[lotId][block] = false;
                                    this.lots[lotId].remote[block] = response.data;
                                    //console.log(this.tenders[this.tenderKey].remoteLots);
                                } else {
                                    this.loading[block] = false;

                                    //Vue.set(this.tenders[this.tenderKey].remote, block, response.data);

                                    this.tenders[this.tenderKey].remote[block] = response.data;
                                }
                            });
                        }
                    }else{
                        this.toggle(block, lotId);
                    }
                },
                resetRemoteToggle: function() {
                    for(var k in this.remote) {
                        if (!this.tender.remote[k].length) {
                            this.toggled[k] = true;
                        }
                    }
                    if(this.remoteLots.length) {
                        for (var id in this.remoteLots) {
                            for (var key in this.remoteLots[id]) {
                                if (!this.remoteLots[id][key]) {
                                    this.toggledLots[id][key] = true;
                                }
                            }
                        }
                    }
                },
                prependRemoteData: function(data) {
                    for(var k in data) {
                        data[k].remote={};

                        if(data[k].__isMultiLot) {
                            for(var key in data[k].lots) {

                                var lot = data[k].lots[key];

                                Vue.set(this.toggledLots, lot.id, {});
                                Vue.set(this.loadingLots, lot.id, {});
                                Vue.set(this.remoteLots, lot.id, {});

                                this.toggledLots[lot.id] = {
                                    documents: true,
                                    items: true,
                                    qualifications: true,
                                    contracts: true,
                                    questions: true,
                                    complaints: true,
                                    prequalifications: true,
                                    dates: true,
                                    features: true,
                                };

                                this.loadingLots[lot.id] = {
                                    documents: false,
                                    items: false,
                                    qualifications: false,
                                    contracts: false,
                                    questions: false,
                                    complaints: false,
                                    prequalifications: false,
                                    features: false,
                                };

                                this.remoteLots[lot.id] = {
                                    documents: [],
                                    items: [],
                                    qualifications: [],
                                    contracts: [],
                                    questions: [],
                                    complaints: [],
                                    prequalifications: [],
                                    features: [],
                                };

                                data[k].lots[key].remote={};

                                for(var id in this.remoteLots) {
                                    for(var _key in this.remoteLots[id]) {
                                        data[k].lots[key].remote[_key] = this.remoteLots[id][_key];
                                    }
                                }

                                Vue.set(this.lots, lot.id, {});

                                this.lots[lot.id] = lot;
                            }
                        }

                        for(var r in this.remote) {
                            data[k].remote[r]=this.remote[r];
                        }
                    }

                    //console.log(this.lots);
                    //console.log(data[k].remote);

                    return data;
                },
                keyNavigation: function(e) {
                    if (e.keyCode == '38') {
                        document.getElementsByTagName('body')[0].classList.add('loading');
                        e.preventDefault();

                        var prevKey=this.tenderKey!==null && this.tenderKey>0 ? this.tenderKey-1 : 0;
                        
                        this.showSidebar(prevKey);
                        
                    }
                    else if (e.keyCode == '40') {
                        document.getElementsByTagName('body')[0].classList.add('loading');
                        e.preventDefault();
                        
                        var nextKey=this.tenderKey!==null ? this.tenderKey+1 : 0;

                        if(nextKey<this.tenders.length){
                            this.showSidebar(nextKey);
                        }else{
                            this.nextPage();
                            document.getElementsByTagName('body')[0].classList.remove('loading');
                        }
                    }
                },
                showTender: function (type) {
                    if (type == 'prev') {
                        document.getElementsByTagName('body')[0].classList.add('loading');
                        var prevKey=this.tenderKey!==null && this.tenderKey>0 ? this.tenderKey-1 : 0;

                        this.showSidebar(prevKey);
                    }
                    else if (type == 'next') {
                        document.getElementsByTagName('body')[0].classList.add('loading');
                        var nextKey=this.tenderKey!==null ? this.tenderKey+1 : 0;

                        if(nextKey<this.tenders.length){
                            this.showSidebar(nextKey);
                        }else{
                            this.nextPage();
                            document.getElementsByTagName('body')[0].classList.remove('loading');
                        }
                    }
                }
            },
            mounted() {
                var tenderID = '';
                var tenderPage = false;

                this.isMobile = document.getElementById('vue-indicators').getAttribute('data-is-mobile');
                this.apiUrl = document.getElementById('vue-indicators').getAttribute('data-api-url');
                this.filters = document.getElementById('vue-indicators').getAttribute('data-filters');

                if(this.filters == 'true') {
                    document.addEventListener("keydown", this.keyNavigation, false);
                    this.search();
                    return;
                }

                if(window.location.pathname.indexOf('UA-') > -1) {
                    tenderID = window.location.pathname.split('/')[2];
                }

                if(window.location.pathname.indexOf('tender/') > -1) {
                    tenderPage = true;
                }

                axios.post(this.apiUrl, {
                    tender_id: tenderID
                }).then(response => {
                    document.getElementsByTagName('body')[0].classList.remove('loading');
                    document.addEventListener("keydown", this.keyNavigation, false);

                    this.tenders = this.prependRemoteData(response.data.tenders);

                    if(tenderPage) {
                        this.showSidebar(0);
                    }

                    if(response.data.tenders) {
                        this.pagination = true;
                    } else {
                        this.pagination = false;
                    }
                });
            },
            created () {

            },
            destroyed () {
                document.removeEventListener("keydown", this.keyNavigation);
            }
        });
    }
})();