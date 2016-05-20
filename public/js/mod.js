EC.addMods({
    "search-module": function () {
        var self = this, params = this.urlParams;
        this.init = function () {
        };
        this.events = {
            "click>.submitToSearch": function () {
                try {
                    submitSearch();
                } catch (s) {
                    alertTip(s);
                }
            },
            "click>#createButton": function () {
                EZ.editDialog.EzWmsEditDataDialog({
                    editUrl: EZ.url + "edit"
                });
            }
        };
    },
    "module-table": function () {
        var self = this, params = this.urlParams;
        this.init = function () {
        };
        this.events = {
            "click>#createButton": function () {
                EZ.editDialog.EzWmsEditDataDialog({
                    editUrl: EZ.url + "edit"
                });
            }
        };
    },
    "menu-module": function () {
        var self = this;
        var $this= this.$element;
        this.init = function () {};
        this.events = {
            "click>.sub-menu": function () {
                var state=$(this).attr('state');
                $this.children('dl').children('dd').css('display','none');
                $(".sub-menu").attr('state',1);
                if (state == '0') {
                    $(this).attr('state', 1);
                    $(this).parent().children('dd').hide();//slideToggle
                } else {
                    $(this).attr('state', 0);
                    $(this).parent().children('dd').show();//slideDown
                }
            }
        };
    },
    "main-container": function () {
        var self = this;
        this.init = function () {
        };
        this.events = {
            "click>#folding": function () {
                var state = $(this).attr("state");
                var sidebar_w = $("#sidebar").width();
                var sidebarIcon_H =  $("#sidebarIcon").height();
                //var main_right_w = $("#main-right").width();
                var tempH=$("#sidebar").height();
                $(this).attr('class', 'shrink_bg2');
                if (state == '1') {
                    $(this).attr("state", 0);
                    $(this).attr('class', 'shrink_bg1');
                    $("#sidebar").css("left", '').css('position', '').css('height',sidebarIcon_H);
                    $("#sidebarIcon").css("display","none");
                    $("#main-right").css("width",'86%');
                } else {
                    $(this).attr("state", 1);
                    $(this).attr('class', 'shrink_bg2');
                    $("#sidebar").css("left", sidebar_w * -1).css('position', 'absolute').css('height',1);
                    $("#sidebarIcon").css("display","block");
                    $("#sidebarIcon").css("height",tempH);
                    $("#main-right").css("width", '95%');
                }
            }
        };
    },
    "main-right": function () {
        var self = this;
        this.init = function () {
            var currentPage = '';
            if (currentPage = $.cookie('currentPage')) {
                var page = currentPage.split('{|}');
               // alert();
               // if (page[0] == '0' || page[1] == 'undefined' || !page[1])return;
                leftMenu(page[0], page[2], page[1]);
            }
        };
    },
    "table-module-list-data": function () {
        var self = this;
        this.init = function () {
            var localData = '';
            var getLangJson = function () {
                $.ajax({
                    type: "get",
                    async: false,
                    dataType: "json",
                    url: '/js/json/languages.json?d='+Math.random(),
                    success: function (data) {
                        if (isJson(data)) localData = data;
                    }
                });
            }

            var setLang = function () {
                if (localStorage && typeof JSON !== 'undefined') {
                    var localStorageData = localStorage;
                    // localStorage.removeItem('LANGUAGE');
                    if (localStorageData.getItem('LANGUAGE') && localStorageData.getItem('LANGUAGE') != "undefined" && localStorage.getItem('version') && localStorage.getItem('version') == EZ.version) {
                        localData = JSON.parse(localStorage.getItem('LANGUAGE'));
                    } else {
                        getLangJson();
                        localStorageData.setItem('version', EZ.version);
                        localStorageData.setItem('LANGUAGE', JSON.stringify(localData));
                    }
                } else {
                    getLangJson();
                }
                var lang = $.cookie('LANGUAGE');
                for (var j in localData) {
                    EZ[j] = localData[j][lang];
                }
            }
            setLang();
            EZ.listDate.EzWmsSetSearchData();
        };
    }
});