/**
 * ------------------------------------------------------------------------
 * JA ACM Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
(function ($){
    var JAList = function (element) {
        var $element = this.$element = $(element);

        // bind click event for button
        $element.bindActions ('.action', this);

        // make textarea auto height
        $element.elasticTextarea('delete_row clone_row updated');

        // make all field as ignore save
        $element.find ('input, textarea, select').not('.acm-object').data('ignoresave', 1);

        // reset index
        $element.data('index', 0);

        // build Form
        this.bindData();

        // store
        $element.find ('.acm-object').data('acm-object', this);

        // trigger updated event for element after built
        setTimeout(function(){$element.trigger('updated')}, 100);
    };

    JAList.prototype.getData = function () {
        // get first row
        var $element = this.$element,
            $items = $element.find('table.jalist > tbody > tr:first').find ('input, textarea, select'),
            result = {};

        $items.each (function () {
            var $this = $(this),
                name = $this.data('name');
            result[name] = jaTools.getVal (this, $element);
        });

        result['rows'] = $element.find('table.jalist > tbody > tr').length;
        result['cols'] = $element.find('table.jalist > tbody > tr:first > td').length;
        result['type'] = 'list';

        return result;
    };

    JAList.prototype.bindData = function (fieldname, alldata) {
        // delete added cols/rows
        var jalist = this;
        this.$element.find('table.jalist > tbody .btn-delete').slice(1).each (function() {
            jalist.delete_row (this);
        });

        if (!alldata) return ;

        var rows = 1,
            names = [],
            $items = this.$element.find('table.jalist > tbody > tr:first > td').slice(0, -1);

        $items.each (function (i, cell) {
            var $cell = $(cell),
                $field = $cell.find ('input, textarea, select'),
                name = $field.data('name');
            if (!name) return;
            names[i] = name;
        });

        var data = alldata[fieldname] ? alldata[fieldname] : {};

        // compatible with old version
        // try to detect old data - compatible with old version
        if ($.isEmptyObject(data)) {
            var group = fieldname.replace(/^[^\[]*\[/, '[');
            names.each (function (name, row) {
                var fname = name.replace (group, '');
                if (alldata[fname]) data[name] = alldata[fname];
            });
        }
        // end compatible

        // find number cols/rows
        names.each (function (name, row) {
            if (data[name] && data[name].length > rows) rows = data[name].length;
        });

        // blank data, just quit
        if ($.isEmptyObject(data)) return ;

        // add rows
        var btn = this.$element.find('table.jalist > tbody .btn-clone')[0];
        for (var i=0; i<rows-1; i++) {
            this.clone_row (btn);
        }

        var $rows = this.$element.find('table.jalist > tbody > tr');
        names.each (function (name, col) {
            if (data[name] && data[name].length) {
                data[name].each (function (val, row){
                    var $cell = $rows.eq(row).children().eq(col);
                    jaTools.setVal($cell.find('input, textarea, select'), val);
                    // set up multi cal for first load.
                    var calname = $cell.find('input, textarea, select').attr('id');
                    if (/timeline_data__date/.test(calname)) {
						Calendar.setup({
							inputField: calname,
							ifFormat: "%Y-%m-%d",
							button: calname+"_img",
							align: "Tl",
							singleClick: true,
							firstDay: 0
						});
					}
                });
            }
        });
    };

    // Actions
    JAList.prototype.delete_row = function (btn) {
        var $btn = $(btn),
            $row = $btn.parents('tr').first();
        if (!$row.hasClass('first')) {
            $row.remove();
        }
    };

    JAList.prototype.clone_row = function (btn) {
        var $btn = $(btn),
            $row = $btn.parents('tr').first(),
            idx = this.$element.data('index');
        this.$element.data('index', ++idx);
        jaTools.fixCloneObject($row.jaclone(idx).removeClass ('first').insertAfter ($row), true);
        // set up multi cal for click button add.
		if (/timeline_data__date/.test($row.jaclone(idx)[0].innerHTML)) {
       		var calname = jQuery($row.jaclone(idx)[0]).find('.icon-calendar').parents('.input-append').find('input').attr('id');
       		if (/timeline_data__date/.test(calname)) {
				Calendar.setup({
					inputField: calname,
					ifFormat: "%Y-%m-%d",
					button: calname+"_img",
					align: "Tl",
					singleClick: true,
					firstDay: 0
				});
			}
		}
    };

    function Plugin() {
        return new JAList(this);
    }

    $.fn.jalist             = Plugin;
    $.fn.jalist.Constructor = JAList;

})(jQuery);


