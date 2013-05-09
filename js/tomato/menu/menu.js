/**
 * TomatoCMS
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tomatocms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 TIG Corporation (http://www.tig.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: menu.js 1288 2010-02-24 04:02:40Z huuphuoc $
 * @since		2.0.1
 */

/* ========== Registry namespace ============================================ */
'Tomato.Menu.Menu'.namespace();
'Tomato.Menu.Item'.namespace();

/* ========== Tomato.Menu.Menu ============================================== */
Tomato.Menu.Menu = function(id) {
	this._id = id;
	this._name = 'menu';
	this._dir = 'hor';
	this._items = new Array();
	this._itemsArray = {};
	this._parentMenuItem = null;
	this._enableHoverHandle = true;
};

Tomato.Menu.Menu.VER_DIR = 'ver';
Tomato.Menu.Menu.HOR_DIR = 'hor';

Tomato.Menu.Menu.prototype.getId = function() { return this._id; };
Tomato.Menu.Menu.prototype.setId = function(id) { this._id = id; };
Tomato.Menu.Menu.prototype.setDirection = function(dir) { this._dir = dir; };
Tomato.Menu.Menu.prototype.setName = function(name) { this._name = name; };
Tomato.Menu.Menu.prototype.setEnableHoverHandle = function(value) { this._enableHoverHandle = value; };

Tomato.Menu.Menu.prototype.render = function() {
	var ul = document.createElement('ul');
	$(ul).attr('id', this._id);
	
	$(ul).addClass('menu_' + this._dir);	
	for (var i in this._items) {
		var li = this._items[i].render(this._enableHoverHandle);
		$(ul).append(li);
	}
	return ul;
};

Tomato.Menu.Menu.prototype.addItem = function(item) {
	var id = this._id + '_' + this._items.length;
	if (item.getId() != null) {
		id = item.getId();
	} 
	item.setId(id);
	this._itemsArray[id + ''] = item;
	this._items[this._items.length] = item;
	return id;
};

Tomato.Menu.Menu.prototype.removeItem = function(id) {
	this._itemsArray[id] = null;
	for (var i in this._items) {
		if (this._items[i].getId() == id) {
			break;
		}
	}
	this._items.splice(i, 1);
}

Tomato.Menu.Menu.prototype.getItem = function(id) {
	return this._itemsArray[id + ''];
};

Tomato.Menu.Menu.prototype.show = function() {
	$('#' + this._id).show();
};
Tomato.Menu.Menu.prototype.hide = function() {
	$('#' + this._id).hide();
};

Tomato.Menu.Menu.prototype.detectActivate = function() {
	var url = window.location.href;
	for (var i in this._items) {
		//if (url == this._items[i].getLink() || url.indexOf(this._items[i].getLink()) > -1) {
		if (url == this._items[i].getLink() || this._items[i].getLink().indexOf(url) > -1) {
			this._items[i].activate();
			break;
		} else {
			var subMenu = this._items[i].getSubMenu();
			if (subMenu != null) {
				subMenu.detectActivate();
			}
		}
	}
};

Tomato.Menu.Menu.prototype.buildMenu = function(html) {
	var ul = $('#' + this._id);
	
	// Build sub items
	var self = this;
	$(ul).children('li').each(function() {
		if ($(this).children('a').length > 0) {
			var a = $(this).find('a:first');
			var item = new Tomato.Menu.Item($(a).html(), $(a).attr('href'));
			self.addItem(item);
			
			$(this).attr('id', item.getId());
			
			// Build sub menu if have
			if ($(this).find('ul').length > 0) {
				var ul = $(this).find('ul:first');
				var subMenu = new Tomato.Menu.Menu();
				item.setSubMenu(subMenu);
				$(ul).attr('id', subMenu.getId());
				subMenu.buildMenu($(ul).html());
			}
			
			// Render item
			item.buildItem();
		}
	});
};

/* ========== Tomato.Menu.Item ============================================== */
Tomato.Menu.Item = function(label, link) {
	this._label = label;
	this._link = link;
	this._subMenu = null;
	this._id = null;
};
Tomato.Menu.Item.activatedItem = null;
Tomato.Menu.Item.overItem = null;

Tomato.Menu.Item.prototype.getId = function() { return this._id; }; 
Tomato.Menu.Item.prototype.setId = function(id) { this._id = id; };
Tomato.Menu.Item.prototype.getLabel = function() { return this._label; };
Tomato.Menu.Item.prototype.setLabel = function(label) { this._label = label; };
Tomato.Menu.Item.prototype.getLink = function() { return this._link; };
Tomato.Menu.Item.prototype.setLink = function(link) { this._link = link; };

Tomato.Menu.Item.prototype.buildItem = function() {
	this._onHoverHandler($('#' + this._id));
};

Tomato.Menu.Item.prototype._onHoverHandler = function(li) {
	var subMenu = this._subMenu;
	var self = this;
	/**
	 * TODO: Add timeout for hover event
	 * http://cherne.net/brian/resources/jquery.hoverIntent.html
	 * @since 2.0.2
	 */
	$(li).hoverIntent({
		sensitivity: 7,
		interval: 200,
		timeout: 500,
		over: function() {
			if (Tomato.Menu.Item.overItem == null) {
				if (Tomato.Menu.Item.activatedItem != null) {
					Tomato.Menu.Item.activatedItem.deactivate();			
				}
				Tomato.Menu.Item.overItem = self;
			}
			else {
				if (Tomato.Menu.Item.activatedItem != null && $('#' + self._id).find('li[id="' + Tomato.Menu.Item.activatedItem.getId() + '"]').length > 0) {
				} else {
					if (Tomato.Menu.Item.activatedItem != null) {
						Tomato.Menu.Item.activatedItem.deactivate();			
					}
					Tomato.Menu.Item.overItem = self;
				}
			}
			if (subMenu != null) {
				subMenu.show();
			}
		},
		out: function() {
			Tomato.Menu.Item.overItem = null;
			if (subMenu != null) {
				subMenu.hide();
			}
//			if (Tomato.Menu.Item.activatedItem != null) {
//				Tomato.Menu.Item.activatedItem.activate();				
//			}
		}
	});
};

Tomato.Menu.Item.prototype.render = function(hoverHandle) {
	var li = document.createElement('li');
	var a = document.createElement('a');
	$(a).attr('href', this._link).html(this._label);
	$(li).addClass('t_a_menu_item').append(a);
	$(li).attr('id', this._id);
		
	// Render sub-menu
	if (this._subMenu != null) {
		var ul = this._subMenu.render();
		$(li).append(ul);
	}
	if (hoverHandle == true) {
		this._onHoverHandler(li);
	}
		
	return li;
};

Tomato.Menu.Item.prototype.getSubMenu = function() {
	return this._subMenu;
};
Tomato.Menu.Item.prototype.setSubMenu = function(menu) {
	menu.setId(this._id + "_1");
	this._subMenu = menu;
};

Tomato.Menu.Item.prototype.activate = function() {
	Tomato.Menu.Item.activatedItem = this;
	$('#' + this._id).addClass('current').children('ul').show();
	$('#' + this._id).parents('ul').show();
	$('#' + this._id).parents('li').addClass('current');
};
Tomato.Menu.Item.prototype.deactivate = function() {
	$('#' + this._id).removeClass('current').children('ul').hide();
	var ulArray = $('#' + this._id).parents('ul');
	if (ulArray != undefined) {
		for (var i = 0; i < ulArray.length - 1; i++) {
			$(ulArray[i]).hide();			
		}
	}
	$('#' + this._id).parents('li').removeClass('current');
};
