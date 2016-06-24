/**
 * ------------------------------------------------------------------------
 * JA System Google Map plugin for Joomla 2.5 & J3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

JAElementGenCode = new Class({
	initialize: function () {
		this.code = '{jamap ';
		this.prefix = 'jform[params]';
		this.objText = this.prefix + '[code_container]';
		this.objCheckboxes = this.prefix + '[list_params][]';
		this.mapPreviewId = 'jaMapPreview';
		this.form = document.adminForm;
		
		this.mapHolder = 'map-preview-container';
		this.mapId = 'ja-widget-map';
		this.objMap = null;
		this.aUserSetting = {};
		//
		this.scanItem();
		this.getUserSetting();
	},
	
	getUserSetting: function() {
		this.aUserSetting = {};
		
		//get user setting
		var sConfig = $(this.objText).value;
		settings = sConfig.trim();

		settings = settings.replace('{jamap ', '{');
		settings = settings.replace('{/jamap}', '');
		//settings = settings.replace(/'/g, '"');
		settings = settings.replace(/([a-z0-9_]+)=/g, ', "$1":');
		settings = settings.replace(/^\{,/, '{');

		this.aUserSetting = JSON.decode(settings);;
	},

	getFormData: function() {
		var frmData = this.form.toQueryString().parseQueryString();

		var data = {};
		for(var property in frmData) {
			var prop = property;
			if(prop.indexOf(this.prefix) == 0) {
				prop = prop.substr(this.prefix.length);
				prop = prop.split(/\]\[/i);//E.g:jform[params][locations][location][0]

				var cdata = data;
				for(var i=0; i<prop.length; i++) {
					var sp = prop[i].replace(/[\[\]]+/g, '');

					if(i<prop.length - 1) {
						if(typeof(cdata[sp]) == 'undefined') {
							cdata[sp] = {};
						}

						cdata = cdata[sp];
					} else {
						cdata[sp] = frmData[property];
					}
				}

			}
		}
		return data;
	},
	
	genCode: function() {
		this.scanItem();
		this.getUserSetting();
		//
		var str = this.code,
			data = this.getFormData();
		for(var i=0; i < this.form.elements[this.objCheckboxes].length; i++) {
			var item = this.form.elements[this.objCheckboxes][i];
			if(item.checked && !item.disabled) {
				var e = item.value,
					value = '';

				if(typeof(data[e]) != 'undefined') {
					value = data[e];
					if(typeof(value) == 'object') {
						value = JSON.encode(value);
					}
				}

				//check user setting
				if(this.aUserSetting[item.value]) {
					value = this.aUserSetting[item.value];
				}
				
				str += item.value + "='" + this.addslashes(value.toString()) + "' ";
			}
		}
		str += '}{/jamap}';
		
		$(this.objText).value = str;
		
		//reset user setting
		this.getUserSetting();
	},
	/**
	 * Scan for check item is enable or diabled
	*/
	scanItem: function() {
		var i;
		for(i=0; i < this.form.elements[this.objCheckboxes].length; i++) {
			var item = this.form.elements[this.objCheckboxes][i];
			if(item.alt) {
				var disabled = (!item.checked || item.disabled) ? true : false;
				this.setChildren(item.alt, disabled);
			}
		}
	},
	
	setChildren: function(children, disabled) {
		aChild = children.split(',');
		var i;
		var j;
		for(j=0; j<aChild.length; j++) {
			for(i=0; i < this.form.elements[this.objCheckboxes].length; i++) {
				var item = this.form.elements[this.objCheckboxes][i];
				if(item.value == aChild[j]) {
					item.disabled = disabled;
					var label = item.id + '-label';
					if($(label)) {
						if(disabled)
							$(label).addClass('item_disable');
						else
							$(label).removeClass('item_disable');
					}
					break;
				}
			}
			
		}
	},
	
	previewMap: function() {
		var aParams = this.getFormData();
		this.getUserSetting();
		
		for(key in this.aUserSetting) {
			aParams[key] = this.aUserSetting[key];
		}
		
		aParams['context_menu'] = 0;
		aParams["map_width"] = aParams["map_width"].toInt();
		aParams["map_height"] = aParams["map_height"].toInt();
		aParams["maptype_control_display"] = aParams["maptype_control_display"].toInt();
		aParams["toolbar_control_display"] = aParams["toolbar_control_display"].toInt();
		aParams["display_scale"] = aParams["display_scale"].toInt();
		aParams["display_overview"] = aParams["display_overview"].toInt();
		aParams["zoom"] = aParams["zoom"].toInt();
	
		this.createMap(aParams);
		//
		if(this.objMap == null) {
			this.objMap = new JAWidgetMap(this.mapId, aParams);
			this.objMap.displayMap();
		} else {
			this.objMap.setMap(aParams);
			this.objMap.displayMap();
		}
	},
	
	
	createMap: function(aParams){
		/**
			<div id="ja-widget-map-container" class="map-container" style="overflow:hidden;">
				<div id="ja-widget-map" style="width:420px; height:300px;"></div>
				<div id="ja-widget-route" class="map-route"></div>
			</div>
		*/
		var map_container = this.mapId + '-container';
		
		if(!$(this.mapId)) {
			var container = new Element('div', {id: map_container, class: 'map-container'}),
				map = new Element('div', {id: this.mapId, styles: { 'width': aParams.map_width, height:  aParams.map_height }}),
				route = new Element('div', {id: this.mapId + '-route', class: 'map-route'});
			
			SqueezeBox.applyContent('', {x: aParams.map_width + 20, y: aParams.map_height + 40});
			
			container.inject($('sbox-content'));
			map.inject($(map_container));
			route.inject($(map_container));
		} else {
			$(this.mapId).setStyles({ width: aParams.map_width, height:  aParams.map_height });
			SqueezeBox.applyContent('', {x: aParams.map_width + 20, y: aParams.map_height + 40});
			$(map_container).inject($('sbox-content'));
		}
		
		if(aParams.display_popup == 1) {
			var a = new Element('a', {
				id: 'open_new_window',
				events: {
					'click': function(){
						alert('Only work on Front-End!');
					}
				},
				href: '#mapPreview'
			});
			a.appendText('OPEN IN NEW WINDOW');
			
			a.inject($('sbox-content'), 'top');
		} else {
			if($('open_new_window')) $('open_new_window').dispose();
		}
	},
	
	addslashes: function(str) {
		//str=str.replace(/\\/g,'\\\\');
		str=str.replace(/\'/g,'\\\'');
		//str=str.replace(/\"/g,'\\"');
		//str=str.replace(/\0/g,'\\0');
		return str;
	},
	
	stripslashes: function(str) {
		str=str.replace(/\\'/g,'\'');
		//str=str.replace(/\\"/g,'"');
		//str=str.replace(/\\0/g,'\0');
		//str=str.replace(/\\\\/g,'\\');
		return str;
	}
});


function CopyToClipboard(obj)
{
	$(obj).focus();
	$(obj).select();
	var CopiedTxt = '';
	if(document.selection) {
		CopiedTxt = document.selection.createRange();
		CopiedTxt.execCommand("Copy");
	}
}

window.addEvent('domready', function(){
	var objGencode = new JAElementGenCode();
	var i;
	for(i=0; i < objGencode.form.elements[objGencode.objCheckboxes].length; i++) {
		$(objGencode.form.elements[objGencode.objCheckboxes][i]).addEvent('click', function() {
			objGencode.genCode();
		});
	}
		
	SqueezeBox.initialize({'string': 'Preview Map'});

	$(objGencode.mapPreviewId).addEvent('click', function(e) {
		//
		if(e) e.stop();
		
		if($(objGencode.mapId)) {
			$(objGencode.mapId + '-container').inject($('map-preview-container'));
		}
		SqueezeBox.fromElement('map-preview-container');
		
		objGencode.previewMap();
	});
});