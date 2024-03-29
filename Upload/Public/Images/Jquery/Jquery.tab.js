(function($) {
    $.ui = {
        plugin: {
            add: function(module, option, set) {
                var proto = $.ui[module].prototype;
                for (var i in set) {
                    proto.plugins[i] = proto.plugins[i] || [];
                    proto.plugins[i].push([option, set[i]]);
                }
            },
            call: function(instance, name, args) {
                var set = instance.plugins[name];
                if (!set) {
                    return;
                }
                for (var i = 0; i < set.length; i++) {
                    if (instance.options[set[i][0]]) {
                        set[i][1].apply(instance.element, args);
                    }
                }
            }
        },
        cssCache: {},
        css: function(name) {
            if ($.ui.cssCache[name]) {
                return $.ui.cssCache[name];
            }
            var tmp = $('<div class="ui-resizable-gen">').addClass(name).css({
                position: 'absolute',
                top: '-5000px',
                left: '-5000px',
                display: 'block'
            }).appendTo('body');
            $.ui.cssCache[name] = !!((!(/auto|default/).test(tmp.css('cursor')) || (/^[1-9]/).test(tmp.css('height')) || (/^[1-9]/).test(tmp.css('width')) || !(/none/).test(tmp.css('backgroundImage')) || !(/transparent|rgba\(0, 0, 0, 0\)/).test(tmp.css('backgroundColor'))));
            try {
                $('body').get(0).removeChild(tmp.get(0));
            } catch(e) {}
            return $.ui.cssCache[name];
        },
        disableSelection: function(e) {
            e.unselectable = "on";
            e.onselectstart = function() {
                return false;
            };
            if (e.style) {
                e.style.MozUserSelect = "none";
            }
        },
        enableSelection: function(e) {
            e.unselectable = "off";
            e.onselectstart = function() {
                return true;
            };
            if (e.style) {
                e.style.MozUserSelect = "";
            }
        },
        hasScroll: function(e, a) {
            var scroll = /top/.test(a || "top") ? 'scrollTop': 'scrollLeft',
            has = false;
            if (e[scroll] > 0) return true;
            e[scroll] = 1;
            has = e[scroll] > 0 ? true: false;
            e[scroll] = 0;
            return has;
        }
    };
    var _remove = $.fn.remove;
    $.fn.remove = function() {
        $("*", this).add(this).trigger("remove");
        return _remove.apply(this, arguments);
    };
    function getter(namespace, plugin, method) {
        var methods = $[namespace][plugin].getter || [];
        methods = (typeof methods == "string" ? methods.split(/,?\s+/) : methods);
        return ($.inArray(method, methods) != -1);
    }
    $.widget = function(name, prototype) {
        var namespace = name.split(".")[0];
        name = name.split(".")[1];
        $.fn[name] = function(options) {
            var isMethodCall = (typeof options == 'string'),
            args = Array.prototype.slice.call(arguments, 1);
            if (isMethodCall && getter(namespace, name, options)) {
                var instance = $.data(this[0], name);
                return (instance ? instance[options].apply(instance, args) : undefined);
            }
            return this.each(function() {
                var instance = $.data(this, name);
                if (isMethodCall && instance && $.isFunction(instance[options])) {
                    instance[options].apply(instance, args);
                } else if (!isMethodCall) {
                    $.data(this, name, new $[namespace][name](this, options));
                }
            });
        };
        $[namespace][name] = function(element, options) {
            var self = this;
            this.widgetName = name;
            this.widgetBaseClass = namespace + '-' + name;
            this.options = $.extend({
                disabled: false
            },
            $[namespace][name].defaults, options);
            this.element = $(element).bind('setData.' + name, 
            function(e, key, value) {
                return self.setData(key, value);
            }).bind('getData.' + name, 
            function(e, key) {
                return self.getData(key);
            }).bind('remove', 
            function() {
                return self.destroy();
            });
            this.init();
        };
        $[namespace][name].prototype = $.extend({},
        $.widget.prototype, prototype);
    };
    $.widget.prototype = {
        init: function() {},
        destroy: function() {
            this.element.removeData(this.widgetName);
        },
        getData: function(key) {
            return this.options[key];
        },
        setData: function(key, value) {
            this.options[key] = value;
            if (key == 'disabled') {
                this.element[value ? 'addClass': 'removeClass'](this.widgetBaseClass + '-disabled');
            }
        },
        enable: function() {
            this.setData('disabled', false);
        },
        disable: function() {
            this.setData('disabled', true);
        }
    };
    $.ui.mouse = {
        mouseInit: function() {
            var self = this;
            this.element.bind('mousedown.' + this.widgetName, 
            function(e) {
                return self.mouseDown(e);
            });
            if ($.browser.msie) {
                this._mouseUnselectable = this.element.attr('unselectable');
                this.element.attr('unselectable', 'on');
            }
            this.started = false;
        },
        mouseDestroy: function() {
            this.element.unbind('.' + this.widgetName); ($.browser.msie && this.element.attr('unselectable', this._mouseUnselectable));
        },
        mouseDown: function(e) { (this._mouseStarted && this.mouseUp(e));
            this._mouseDownEvent = e;
            var self = this,
            btnIsLeft = (e.which == 1),
            elIsCancel = (typeof this.options.cancel == "string" ? $(e.target).is(this.options.cancel) : false);
            if (!btnIsLeft || elIsCancel || !this.mouseCapture(e)) {
                return true;
            }
            this._mouseDelayMet = !this.options.delay;
            if (!this._mouseDelayMet) {
                this._mouseDelayTimer = setTimeout(function() {
                    self._mouseDelayMet = true;
                },
                this.options.delay);
            }
            if (this.mouseDistanceMet(e) && this.mouseDelayMet(e)) {
                this._mouseStarted = (this.mouseStart(e) !== false);
                if (!this._mouseStarted) {
                    e.preventDefault();
                    return true;
                }
            }
            this._mouseMoveDelegate = function(e) {
                return self.mouseMove(e);
            };
            this._mouseUpDelegate = function(e) {
                return self.mouseUp(e);
            };
            $(document).bind('mousemove.' + this.widgetName, this._mouseMoveDelegate).bind('mouseup.' + this.widgetName, this._mouseUpDelegate);
            return false;
        },
        mouseMove: function(e) {
            if ($.browser.msie && !e.button) {
                return this.mouseUp(e);
            }
            if (this._mouseStarted) {
                this.mouseDrag(e);
                return false;
            }
            if (this.mouseDistanceMet(e) && this.mouseDelayMet(e)) {
                this._mouseStarted = (this.mouseStart(this._mouseDownEvent, e) !== false); (this._mouseStarted ? this.mouseDrag(e) : this.mouseUp(e));
            }
            return ! this._mouseStarted;
        },
        mouseUp: function(e) {
            $(document).unbind('mousemove.' + this.widgetName, this._mouseMoveDelegate).unbind('mouseup.' + this.widgetName, this._mouseUpDelegate);
            if (this._mouseStarted) {
                this._mouseStarted = false;
                this.mouseStop(e);
            }
            return false;
        },
        mouseDistanceMet: function(e) {
            return (Math.max(Math.abs(this._mouseDownEvent.pageX - e.pageX), Math.abs(this._mouseDownEvent.pageY - e.pageY)) >= this.options.distance);
        },
        mouseDelayMet: function(e) {
            return this._mouseDelayMet;
        },
        mouseStart: function(e) {},
        mouseDrag: function(e) {},
        mouseStop: function(e) {},
        mouseCapture: function(e) {
            return true;
        }
    };
    $.ui.mouse.defaults = {
        cancel: null,
        distance: 1,
        delay: 0
    };
}) (jQuery); 

(function($) {
    $.widget("ui.tabs", {
        init: function() {
            this.options.event += '.tabs';
            this.tabify(true);
        },
        setData: function(key, value) {
            if ((/^selected/).test(key))
            this.select(value);
            else {
                this.options[key] = value;
                this.tabify();
            }
        },
        length: function() {
            return this.$tabs.length;
        },
        tabId: function(a) {
            return a.title && a.title.replace(/\s/g, '_').replace(/[^A-Za-z0-9\-_:\.]/g, '') || this.options.idPrefix + $.data(a);
        },
        ui: function(tab, panel) {
            return {
                options: this.options,
                tab: tab,
                panel: panel
            };
        },
        tabify: function(init) {
            this.$lis = $('li:has(a[href])', this.element);
            this.$tabs = this.$lis.map(function() {
                return $('a', this)[0];
            });
            this.$panels = $([]);
            var self = this,
            o = this.options;
            this.$tabs.each(function(i, a) {
                if (a.hash && a.hash.replace('#', ''))
                self.$panels = self.$panels.add(a.hash);
                else if ($(a).attr('href') != '#') {
                    $.data(a, 'href.tabs', a.href);
                    $.data(a, 'load.tabs', a.href);
                    var id = self.tabId(a);
                    a.href = '#' + id;
                    var $panel = $('#' + id);
                    if (!$panel.length) {
                        $panel = $(o.panelTemplate).attr('id', id).addClass(o.panelClass).insertAfter(self.$panels[i - 1] || self.element);
                        $panel.data('destroy.tabs', true);
                    }
                    self.$panels = self.$panels.add($panel);
                }
                else
                o.disabled.push(i + 1);
            });
            if (init) {
                this.element.addClass(o.navClass);
                this.$panels.each(function() {
                    var $this = $(this);
                    $this.addClass(o.panelClass);
                });
                if (o.selected === undefined) {
                    if (location.hash) {
                        this.$tabs.each(function(i, a) {
                            if (a.hash == location.hash) {
                                o.selected = i;
                                if ($.browser.msie || $.browser.opera) {
                                    var $toShow = $(location.hash),
                                    toShowId = $toShow.attr('id');
                                    $toShow.attr('id', '');
                                    setTimeout(function() {
                                        $toShow.attr('id', toShowId);
                                    },
                                    500);
                                }
                                scrollTo(0, 0);
                                return false;
                            }
                        });
                    }
                    else if (o.cookie) {
                        var index = parseInt($.cookie('ui-tabs' + $.data(self.element)), 10);
                        if (index && self.$tabs[index])
                        o.selected = index;
                    }
                    else if (self.$lis.filter('.' + o.selectedClass).length)
                    o.selected = self.$lis.index(self.$lis.filter('.' + o.selectedClass)[0]);
                }
                o.selected = o.selected === null || o.selected !== undefined ? o.selected: 0;
                o.disabled = $.unique(o.disabled.concat($.map(this.$lis.filter('.' + o.disabledClass), 
                function(n, i) {
                    return self.$lis.index(n);
                }))).sort();
                if ($.inArray(o.selected, o.disabled) != -1)
                o.disabled.splice($.inArray(o.selected, o.disabled), 1);
                this.$panels.addClass(o.hideClass);
                this.$lis.removeClass(o.selectedClass);
                if (o.selected !== null) {
                    this.$panels.eq(o.selected).show().removeClass(o.hideClass);
                    this.$lis.eq(o.selected).addClass(o.selectedClass);
                    var onShow = function() {
                        $(self.element).triggerHandler('tabsshow', [self.ui(self.$tabs[o.selected], self.$panels[o.selected])], o.show);
                    };
                    if ($.data(this.$tabs[o.selected], 'load.tabs'))
                    this.load(o.selected, onShow);
                    else
                    onShow();
                }
                $(window).bind('unload', 
                function() {
                    self.$tabs.unbind('.tabs');
                    self.$lis = self.$tabs = self.$panels = null;
                });
            }
            for (var i = 0, li; li = this.$lis[i]; i++)
            $(li)[$.inArray(i, o.disabled) != -1 && !$(li).hasClass(o.selectedClass) ? 'addClass': 'removeClass'](o.disabledClass);
            if (o.cache === false)
            this.$tabs.removeData('cache.tabs');
            var hideFx,
            showFx,
            baseFx = {
                'min-width': 0,
                duration: 1
            },
            baseDuration = 'normal';
            if (o.fx && o.fx.constructor == Array)
            hideFx = o.fx[0] || baseFx,
            showFx = o.fx[1] || baseFx;
            else
            hideFx = showFx = o.fx || baseFx;
            var resetCSS = {
                display: '',
                overflow: '',
                height: ''
            };
            if (!$.browser.msie)
            resetCSS.opacity = '';
            function hideTab(clicked, $hide, $show) {
                $hide.animate(hideFx, hideFx.duration || baseDuration, 
                function() {
                    $hide.addClass(o.hideClass).css(resetCSS);
                    if ($.browser.msie && hideFx.opacity)
                    $hide[0].style.filter = '';
                    if ($show)
                    showTab(clicked, $show, $hide);
                });
            }
            function showTab(clicked, $show, $hide) {
                if (showFx === baseFx)
                $show.css('display', 'block');
                $show.animate(showFx, showFx.duration || baseDuration, 
                function() {
                    $show.removeClass(o.hideClass).css(resetCSS);
                    if ($.browser.msie && showFx.opacity)
                    $show[0].style.filter = '';
                    $(self.element).triggerHandler('tabsshow', [self.ui(clicked, $show[0])], o.show);
                });
            }
            function switchTab(clicked, $li, $hide, $show) {
                $li.addClass(o.selectedClass).siblings().removeClass(o.selectedClass);
                hideTab(clicked, $hide, $show);
            }
            this.$tabs.unbind('.tabs').bind(o.event, 
            function() {
                var $li = $(this).parents('li:eq(0)'),
                $hide = self.$panels.filter(':visible'),
                $show = $(this.hash);
                if (($li.hasClass(o.selectedClass) && !o.unselect) || $li.hasClass(o.disabledClass) || $(this).hasClass(o.loadingClass) || $(self.element).triggerHandler('tabsselect', [self.ui(this, $show[0])], o.select) === false) {
                    this.blur();
                    return false;
                }
                self.options.selected = self.$tabs.index(this);
                if (o.unselect) {
                    if ($li.hasClass(o.selectedClass)) {
                        self.options.selected = null;
                        $li.removeClass(o.selectedClass);
                        self.$panels.stop();
                        hideTab(this, $hide);
                        this.blur();
                        return false;
                    } else if (!$hide.length) {
                        self.$panels.stop();
                        var a = this;
                        self.load(self.$tabs.index(this), 
                        function() {
                            $li.addClass(o.selectedClass).addClass(o.unselectClass);
                            showTab(a, $show);
                        });
                        this.blur();
                        return false;
                    }
                }
                if (o.cookie)
                $.cookie('ui-tabs' + $.data(self.element), self.options.selected, o.cookie);
                self.$panels.stop();
                if ($show.length) {
                    var a = this;
                    self.load(self.$tabs.index(this), $hide.length ? 
                    function() {
                        switchTab(a, $li, $hide, $show);
                    }: function() {
                        $li.addClass(o.selectedClass);
                        showTab(a, $show);
                    });
                } else
                throw 'jQuery UI Tabs: Mismatching fragment identifier.';
                if ($.browser.msie)
                this.blur();
                return false;
            });
            if (! (/^click/).test(o.event))
            this.$tabs.bind('click.tabs', 
            function() {
                return false;
            });
        },
        add: function(url, label, index) {
            if (index == undefined)
            index = this.$tabs.length;
            var o = this.options;
            var $li = $(o.tabTemplate.replace(/#\{href\}/g, url).replace(/#\{label\}/g, label));
            $li.data('destroy.tabs', true);
            var id = url.indexOf('#') == 0 ? url.replace('#', '') : this.tabId($('a:first-child', $li)[0]);
            var $panel = $('#' + id);
            if (!$panel.length) {
                $panel = $(o.panelTemplate).attr('id', id).addClass(o.hideClass).data('destroy.tabs', true);
            }
            $panel.addClass(o.panelClass);
            if (index >= this.$lis.length) {
                $li.appendTo(this.element);
                $panel.appendTo(this.element[0].parentNode);
            } else {
                $li.insertBefore(this.$lis[index]);
                $panel.insertBefore(this.$panels[index]);
            }
            o.disabled = $.map(o.disabled, 
            function(n, i) {
                return n >= index ? ++n: n
            });
            this.tabify();
            if (this.$tabs.length == 1) {
                $li.addClass(o.selectedClass);
                $panel.removeClass(o.hideClass);
                var href = $.data(this.$tabs[0], 'load.tabs');
                if (href)
                this.load(index, href);
            }
            this.element.triggerHandler('tabsadd', [this.ui(this.$tabs[index], this.$panels[index])], o.add);
        },
        remove: function(index) {
            var o = this.options,
            $li = this.$lis.eq(index).remove(),
            $panel = this.$panels.eq(index).remove();
            if ($li.hasClass(o.selectedClass) && this.$tabs.length > 1)
            this.select(index + (index + 1 < this.$tabs.length ? 1: -1));
            o.disabled = $.map($.grep(o.disabled, 
            function(n, i) {
                return n != index;
            }), 
            function(n, i) {
                return n >= index ? --n: n
            });
            this.tabify();
            this.element.triggerHandler('tabsremove', [this.ui($li.find('a')[0], $panel[0])], o.remove);
        },
        enable: function(index) {
            var o = this.options;
            if ($.inArray(index, o.disabled) == -1)
            return;
            var $li = this.$lis.eq(index).removeClass(o.disabledClass);
            if ($.browser.safari) {
                $li.css('display', 'inline-block');
                setTimeout(function() {
                    $li.css('display', 'block');
                },
                0);
            }
            o.disabled = $.grep(o.disabled, 
            function(n, i) {
                return n != index;
            });
            this.element.triggerHandler('tabsenable', [this.ui(this.$tabs[index], this.$panels[index])], o.enable);
        },
        disable: function(index) {
            var self = this,
            o = this.options;
            if (index != o.selected) {
                this.$lis.eq(index).addClass(o.disabledClass);
                o.disabled.push(index);
                o.disabled.sort();
                this.element.triggerHandler('tabsdisable', [this.ui(this.$tabs[index], this.$panels[index])], o.disable);
            }
        },
        select: function(index) {
            if (typeof index == 'string')
            index = this.$tabs.index(this.$tabs.filter('[href$=' + index + ']')[0]);
            this.$tabs.eq(index).trigger(this.options.event);
        },
        load: function(index, callback) {
            var self = this,
            o = this.options,
            $a = this.$tabs.eq(index),
            a = $a[0],
            bypassCache = callback == undefined || callback === false,
            url = $a.data('load.tabs');
            callback = callback || 
            function() {};
            if (!url || !bypassCache && $.data(a, 'cache.tabs')) {
                callback();
                return;
            }
            var inner = function(parent) {
                var $parent = $(parent),
                $inner = $parent.find('*:last');
                return $inner.length && $inner || $parent;
            };
            var cleanup = function() {
                self.$tabs.filter('.' + o.loadingClass).removeClass(o.loadingClass).each(function() {
                    if (o.spinner)
                    inner(this).parent().html(inner(this).data('label.tabs'));
                });
                self.xhr = null;
            };
            if (o.spinner) {
                var label = inner(a).html();
                inner(a).wrapInner('<em></em>').find('em').data('label.tabs', label).html(o.spinner);
            }
            var ajaxOptions = $.extend({},
            o.ajaxOptions, {
                url: url,
                success: function(r, s) {
                    $(a.hash).html(r);
                    cleanup();
                    if (o.cache)
                    $.data(a, 'cache.tabs', true);
                    $(self.element).triggerHandler('tabsload', [self.ui(self.$tabs[index], self.$panels[index])], o.load);
                    o.ajaxOptions.success && o.ajaxOptions.success(r, s);
                    callback();
                }
            });
            if (this.xhr) {
                this.xhr.abort();
                cleanup();
            }
            $a.addClass(o.loadingClass);
            setTimeout(function() {
                self.xhr = $.ajax(ajaxOptions);
            },
            0);
        },
        url: function(index, url) {
            this.$tabs.eq(index).removeData('cache.tabs').data('load.tabs', url);
        },
        destroy: function() {
            var o = this.options;
            this.element.unbind('.tabs').removeClass(o.navClass).removeData('tabs');
            this.$tabs.each(function() {
                var href = $.data(this, 'href.tabs');
                if (href)
                this.href = href;
                var $this = $(this).unbind('.tabs');
                $.each(['href', 'load', 'cache'], 
                function(i, prefix) {
                    $this.removeData(prefix + '.tabs');
                });
            });
            this.$lis.add(this.$panels).each(function() {
                if ($.data(this, 'destroy.tabs'))
                $(this).remove();
                else
                $(this).removeClass([o.selectedClass, o.unselectClass, o.disabledClass, o.panelClass, o.hideClass].join(' '));
            });
        }
    });
    $.ui.tabs.defaults = {
        unselect: false,
        event: 'click',
        disabled: [],
        cookie: null,
        spinner: 'Loading&#8230;',
        cache: false,
        idPrefix: 'ui-tabs-',
        ajaxOptions: {},
        fx: null,
        tabTemplate: '<li><a href="#{href}"><span>#{label}</span></a></li>',
        panelTemplate: '<div></div>',
        navClass: 'ui-tabs-nav',
        selectedClass: 'ui-tabs-selected',
        unselectClass: 'ui-tabs-unselect',
        disabledClass: 'ui-tabs-disabled',
        panelClass: 'ui-tabs-panel',
        hideClass: 'ui-tabs-hide',
        loadingClass: 'ui-tabs-loading'
    };
    $.ui.tabs.getter = "length";
    $.extend($.ui.tabs.prototype, {
        rotation: null,
        rotate: function(ms, continuing) {
            continuing = continuing || false;
            var self = this,
            t = this.options.selected;
            function start() {
                self.rotation = setInterval(function() {
                    t = ++t < self.$tabs.length ? t: 0;
                    self.select(t);
                },
                ms);
            }
            function stop(e) {
                if (!e || e.clientX) {
                    clearInterval(self.rotation);
                }
            }
            if (ms) {
                start();
                if (!continuing)
                this.$tabs.bind(this.options.event, stop);
                else
                this.$tabs.bind(this.options.event, 
                function() {
                    stop();
                    t = self.options.selected;
                    start();
                });
            }
            else {
                stop();
                this.$tabs.unbind(this.options.event, stop);
            }
        }
    });
}) (jQuery);