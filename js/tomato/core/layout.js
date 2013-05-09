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
 * @version 	$Id: layout.js 1288 2010-02-24 04:02:40Z huuphuoc $
 */

/* ========== Registry namespace ============================================ */
'Tomato.Core.Layout.Lang'.namespace();
'Tomato.Core.Layout.Container'.namespace();
'Tomato.Core.Layout.Widget'.namespace();
'Tomato.Core.Layout.DefaultOutput'.namespace();

/* ========== Tomato.Core.Layout.Lang ======================================= */

/**
 * This static class store language data for layout package
 */
Tomato.Core.Layout.Lang = function() {};

/** Lang data */
Tomato.Core.Layout.Lang.DATA = {
	CONTAINER_COLS: 			'%s cols',
	CONTAINER_REMOVE_CONFIRM: 	'Do you really want to remove this container and child container/widgets?',
	WIDGET_PREVIEW: 			'Preview',
	WIDGET_BACK: 				'Back',
	WIDGET_REMOVE_CONFIRM: 		'Do you really want to remove this widget?',
	WIDGET_CACHE: 				'Cache (in seconds)',
	WIDGET_LOAD_AJAX: 			'Load by Ajax',
	DEFAULT_OUTPUT: 			'Default Output'
};
Tomato.Core.Layout.Lang.setLang = function(data) {
	for (var p in data) {
		Tomato.Core.Layout.Lang.DATA[p] = data[p];
	}
};
Tomato.Core.Layout.Lang.getLang = function(key) {
	return Tomato.Core.Layout.Lang.DATA[key];
};

/* ========== Tomato.Core.Layout.Container ================================== */

/**
 * This class represent a container.
 * Require following plugins from jQuery:
 * - ajaxq
 * - jquery.json
 * - jquery UI (sortable/draggable/droppable)
 */
Tomato.Core.Layout.Container = function(id, parent) {
	/** Id of DIV container */
	this._id = id;

	/** Parent container */
	this._parent = parent;
	
	/** Child containers */
	this._child = [];
	
	/** Number of child containers */
	this._childCount = 0;
	
	/** Array of child containers */
	this._childContainers = {};
	
	/** Array of widgets */
	this._widgets = {};
	
	/** UUID of widget belong to container */
	this._widgetUuid = 0;
	
	/** Number of grid columns */
	this._numColumns = 12;
	
	/** 
	 * Releative position of container in row with full of 12 columns.
	 * Can take one of two values: 'first' or 'last'
	 */
	this._position = null;
	
	/** Array of tabs */
	this._tabs = [];
	
	/** UUID of tab container */
	this._tabUuid = 0;
	
	/** 
	 * Background color
	 * Hex code is 333333. In HSV format: H=0, S=0, V=20
	 */
	this._bgColor = '#333333';
	
	this._baseUrl = '/';
	
	this._droppable();
};

Tomato.Core.Layout.Container.TOTAL_COLUMNS = 12;
Tomato.Core.Layout.Container.ROW_HEIGHT = 150;

/**
 * This static variable store the current dragged widget
 */
Tomato.Core.Layout.Container.currentDraggedWidget = null;

/**
 * Getters/setters
 */
Tomato.Core.Layout.Container.prototype.getId = function() { return this._id; };
Tomato.Core.Layout.Container.prototype.setParent = function(parent) { this._parent = parent; };
Tomato.Core.Layout.Container.prototype.getNumColumns = function() { return this._numColumns; };
Tomato.Core.Layout.Container.prototype.setNumColumns = function(numColumns) { this._numColumns = numColumns; };
Tomato.Core.Layout.Container.prototype.getPosition = function() { return this._position; };
Tomato.Core.Layout.Container.prototype.setPosition = function(position) { this._position = position; };
Tomato.Core.Layout.Container.prototype.setBgColor = function(color) { this._bgColor = color; };
Tomato.Core.Layout.Container.prototype.getBaseUrl = function() { return this._baseUrl; };
Tomato.Core.Layout.Container.prototype.setBaseUrl = function(url) { this._baseUrl = url; };

/**
 * Determine the container is root or not
 * 
 * @return boolean True if the container is root
 */
Tomato.Core.Layout.Container.prototype.isRoot = function() {
	return (this._parent == null);
};

/**
 * Handle drop event
 */
Tomato.Core.Layout.Container.prototype._droppable = function() {
	var self = this;
	$('#' + self._id).droppable({
		greedy: true,
		over: function(event, ui) {
			// TODO: Disable drop in some case
		},
		drop: function(event, ui) {
			var item = ui.draggable;
			
			/**
			 * Drop container
			 */
			if ($(item).hasClass('t_column_draggable')) {
				var arr = $(item).attr('id').split('_');
				var numColumns = parseInt(arr[arr.length - 1]);
				if ((self.isRoot() && numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) 
					|| (!self.isRoot() && numColumns < Tomato.Core.Layout.Container.TOTAL_COLUMNS)) {
					
					if (numColumns != Tomato.Core.Layout.Container.TOTAL_COLUMNS && self.getChildTotalColumns() > Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
						// Show disable drop cursor
						$(item).addClass('t_a_widget_container_undraggable').draggable('disable');
					} else {
						$(item).removeClass('t_a_widget_container_undraggable').draggable('enable');
						self.append(numColumns);
					}
				}
			}
			/**
			 * Drop tab container
			 */
			else if ($(item).hasClass('t_tab_draggable')) {
				var tabId = self.generateTabId();
				self._tabUuid++;
				var tab = new Tomato.Core.Layout.Tabs(tabId, self);
				tab.render();
				
				self.incHeight(140);
			}
			/**
			 * Drop wigdet
			 */
			else if ($(item).hasClass('t_widget_draggable')) {
				// event.target.id => Id of target container
				// ui.draggable.id => Id of widget
				
				var widgetId = self.generateWidgetId();
				var arr = $(item).attr('id').split('_');
				var widget = new Tomato.Core.Layout.Widget(widgetId, self);
				self.addWidget(widget);
				
				widget.setModule(arr[0]);
				widget.setName(arr[1]);
				widget.setTitle($(item).attr('title'));
				widget.render();
			}
			/**
			 * Drop default output
			 */
			else if ($(item).hasClass('t_g_output_draggable')) {
				var widgetId = self.generateWidgetId();
				var output = new Tomato.Core.Layout.DefaultOutput(widgetId, self);
				self.addWidget(output);
				output.render();
			}
			return true;
		}
	});
};

/**
 * Make this container sortable.
 * User can drag and drop widget from container to other container
 */
Tomato.Core.Layout.Container.prototype.sortable = function() {
	var self = this;
	$('.t_a_widget_container').sortable({
		items: '.t_a_widget',
        connectWith: '.t_a_widget_container',
        handle: '.t_a_widget_head',
        placeholder: 't_a_widget_placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        opacity: 0.8,
        containment: 'document',
        start: function(e, ui) {
			// ui.item.id => Id of widget
			// self => source container
			var widgetId = $(ui.item).attr('id');
			Tomato.Core.Layout.Container.currentDraggedWidget = self.getWidget(widgetId);
			
            $(ui.helper).addClass('t_widget_dragging');
        },
        over: function(e, ui) {
        	// Make the widget is suitable in target container
        	$(ui.item).css({width: $(e.target).width() + 'px'});
        },
        receive: function(e, ui) {
        	// e.target.id => Id of target container
        	// ui.item.id => Id of widget
        	// ui.sender.id => Id of source container
        	// self => target container
        	
        	// Add widget to target container
        	if (Tomato.Core.Layout.Container.currentDraggedWidget != null) {
        		Tomato.Core.Layout.Container.currentDraggedWidget.setContainer(self);
	        	self.addWidget(Tomato.Core.Layout.Container.currentDraggedWidget);
        	}
        },
        stop: function(e, ui) {
        	// ui.item.id => Id of widget
        	// self => source container
        	
        	// Remove the widget from source container
        	// if user dragged widget to other container
        	if (Tomato.Core.Layout.Container.currentDraggedWidget != null
        			&& Tomato.Core.Layout.Container.currentDraggedWidget.getContainer().getId() != self._id) {
        		self.removeWidget(Tomato.Core.Layout.Container.currentDraggedWidget);
        	}
            //$(ui.item).css({width: ''}).removeClass('t_widget_dragging');
        }
    });
	// Full-row containers are sortable
	if (this._numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
		$('#' + this._parent.getId()).sortable({
			items: '.t_a_widget_container.grid_12',
			handle: '.t_a_widget_container_head'
			//placeholder: 't_a_widget_container_placeholder',
			//forcePlaceholderSize: true
		});
	}
};

/**
 * Make container resizable
 */
Tomato.Core.Layout.Container.prototype.resizable = function() {
	if (this._numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
		return;
	}
	var self = this;
	$('#' + this._id).resizable({
		// 60 is width of one-column container
		minWidth: 60,
		// 940 is width of 12-columns container
		maxWidth: 940,
		handles: 'e',
//		maxHeight: Tomato.Core.Layout.Container.ROW_HEIGHT,
		ghost: false,
		grid: [80, 0],
		containment: 'parent',
		start: function(e, ui) {
		},
		resize: function(e, ui) {
			// ui.helper.id, ui.element.id => Id of container
			$('#' + self._id).removeClass('grid_' + self._numColumns);
			var numColumns = Math.round((ui.size.width + 20) / 80);
			self._numColumns = numColumns;
			$('#' + self._id).addClass('grid_' + numColumns);
			
			// Update number of columns for sibling containers
			self._parent.updatePositionType();
		},
		stop: function(e, ui) {
			// Update number of columns for container
			var numColumns = Math.round((ui.size.width + 20) / 80);
			self._numColumns = numColumns;
		}
	});
};

/**
 * Update the position of all sibling containers when we resize/remove a container
 */
Tomato.Core.Layout.Container.prototype.updatePositionType = function() {
	var total = 0;
	var child = $('#' + this._id).children('.t_a_widget_container');
	
	$('#' + this._id).children('.clearfix').remove();
	var childColumns = 0;
	
	for (var i = 0; i < child.length; i++) {
		childColumns = Math.round(($(child[i]).width() + 20) / 80);
		$('#' + $(child[i]).attr('id')).find('.t_a_widget_container_head:first h3').html(sprintf(Tomato.Core.Layout.Lang.getLang('CONTAINER_COLS'), childColumns));
		
		total = total + childColumns;
		if (total == this._numColumns && childColumns < this._numColumns) {
			total = 0;
			$('#' + $(child[i]).attr('id')).removeClass('alpha').addClass('omega').after('<div class="clearfix"></div>');
		} else if (total == childColumns || childColumns == this._numColumns || total > this._numColumns) {
			if (total > this._numColumns) {
				total = total - this._numColumns;
			}
			$('#' + $(child[i]).attr('id')).removeClass('omega').addClass('alpha').before('<div class="clearfix"></div>');
		} else if (i > 0 && total > 0 && total < this._numColumns) {
			$('#' + $(child[i]).attr('id')).removeClass('omega').removeClass('alpha');
		}
	}
};

Tomato.Core.Layout.Container.prototype.getChildTotalColumns = function() {
	var total = 0, numColumns = 0;
	$('#' + this._id).children('.t_a_widget_container').each(function() {
		numColumns = Math.round(($(this).width() + 20) / 80);
		total += numColumns;
	});
	return total;
};

/**
 * Append the container
 * 
 * @param int numColumns Number of columns of new container which will be appended to current container
 * @return Tomato.Core.Layout.Container The child container has just been added
 */
Tomato.Core.Layout.Container.prototype.append = function(numColumns) {
	// Make sure the input is integer
	numColumns = parseInt(numColumns);
	
	var totalColumns = this.getChildTotalColumns();
	if (numColumns != Tomato.Core.Layout.Container.TOTAL_COLUMNS
			&& totalColumns > Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
		return null;
	}
	totalColumns += numColumns;
	
	// Only allow user to drop the container which sum of 
	// its columns and container columns is not greater than 12
	var addableColumns = this._numColumns - totalColumns;
//	$('li.t_a_widget_container_draggable').each(function() {
//		var arr = $(this).attr('id').split('_');
//		if (parseInt(arr[1]) > addableColumns && addableColumns > 0) {
//			$(this).addClass('t_a_widget_container_undraggable').draggable('disable');
//		} else {
//			$(this).removeClass('t_a_widget_container_undraggable').draggable('enable');
//		}
//	});
	
	var id = this._id + '_' + this._childCount + '_' + numColumns;
	var div = document.createElement('div');
	$(div).attr('id', id);
	
	//var bgColor = this._generateChildColor();
	var bgColor = '#E1E1E1';
	$(div).addClass('grid_' + numColumns).addClass('t_a_widget_container').css('margin-bottom', '10px').css('background', bgColor);
	
	// Have to append div container before creating container
	// to make the child container is droppable
	$('#' + this._id).append(div);
	var childContainer = new Tomato.Core.Layout.Container(id, this);
	childContainer.setBaseUrl(this._baseUrl);
	childContainer.setNumColumns(numColumns);
	childContainer.setBgColor(bgColor);
	childContainer.sortable();
	childContainer.resizable();
	
	this._child[this._childCount] = childContainer;
	
	this.addChildContainer(childContainer);

	/**
	 * Add remove button
	 */
	var self = this;
	$('<div class="t_a_widget_container_head"><h3>' + sprintf(Tomato.Core.Layout.Lang.getLang('CONTAINER_COLS'), numColumns) + '</h3></div>').css('cursor', 'move').appendTo($(div));
	$('<a href="javascript: void(0)" class="t_a_widget_container_remove">REMOVE</a>').mousedown(function(e) {
        e.stopPropagation();
    }).click(function() {
    	if (confirm(Tomato.Core.Layout.Lang.getLang('CONTAINER_REMOVE_CONFIRM'))) {
    		$(div).remove();
    		self.updatePositionType();
    		self.removeChildContainer(childContainer);
    	}
    }).appendTo($(div).find('.t_a_widget_container_head'));
	
	/**
	 * Add clone button for container
	 */
	$('<a href="javascript: void(0)" class="t_a_widget_container_clone">CLONE</a>').mousedown(function(e) {
        e.stopPropagation();
    }).click(function() {
    	childContainer.append(numColumns);
    }).appendTo($(div).find('.t_a_widget_container_head'));
	
	var minHeight = Tomato.Core.Layout.Container.ROW_HEIGHT + 'px';
	$(div).css('min-height', minHeight).css('height', 'auto !important');//.css('height', minHeight);
	
	var position = null;
	if (/*totalColumns == numColumns || */ numColumns == this._numColumns || this._childCount == 0) {
		// We are adding new row
		$(div).addClass('alpha');
		if (numColumns != this._numColumns) {
			position = 'first';
		}
		
		// TODO: Increase container height, if not we still can add columns container 
		// (by clicking on the [+] button on the GUI), but can't drag it
		minHeight = parseInt($('#' + this._id).css('min-height'));
		if (numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
			minHeight += Tomato.Core.Layout.Container.ROW_HEIGHT + 10;
			$('#' + this._id).css('min-height', minHeight).css('height', 'auto !important');//.css('height', minHeight);
		} else {
			var p = null;
			$('#' + id).parents('.t_a_widget_container').each(function(i) {
				// 25 is height of container head section
				if (p == null || parseInt($(p).css('min-height')) + 25 > parseInt($(this).css('min-height'))) {
					minHeight = parseInt($(this).css('min-height')) + 25;
					$(this).css('min-height', minHeight).css('height', 'auto !important');//.css('height', minHeight);
				}
				p = this;
			});
		}
	}
	if (numColumns == this._numColumns || numColumns == totalColumns && this._childCount > 0) {
//		$('#' + this._id).append($('<div class="t_g_clear" style="height: 1px"></div>'));
//		$('#' + this._id).append($('<div class="t_container_row" style="clear: both; height: 140px"></div>'));
	}
	
	if (totalColumns == this._numColumns) {
		$(div).addClass('omega');
		if (numColumns != this._numColumns) {
			position = 'last';
		}
	}
	this._childCount++;
	
	childContainer.setPosition(position);
	
	return childContainer;
};

/**
 * Generate background color for child container
 * 
 * @return string
 */
Tomato.Core.Layout.Container.prototype._generateChildColor = function() {
	var bg = this._bgColor;
	
	// Convert from Hex to RGB
	var rgb = Tomato.Core.ColorConverter.hexToRgb(bg);
	
	// RGB to HSV
    var hsv = Tomato.Core.ColorConverter.rgbToHsv(rgb[0], rgb[1], rgb[2]);
    var h = hsv[0], s = hsv[1], v = hsv[2];
    
    // Generate new color by keep value of H, S and increase value of V
    v = v + 25;
    if (v > 100) {
    	v = 25;
    }
    
    // Convert from HSV to RGB
    rgb = Tomato.Core.ColorConverter.hsvToRgb(h, s, v);
    
    // and RGB to Hex
    var hex = Tomato.Core.ColorConverter.rgbToHex(rgb[0], rgb[1], rgb[2]);
	return hex;
};

Tomato.Core.Layout.Container.prototype.addChildContainer = function(container) {
	this._childContainers[container.getId() + ''] = container;
};
Tomato.Core.Layout.Container.prototype.getChildContainer = function(id) {
	return this._childContainers[id + ''];
};
Tomato.Core.Layout.Container.prototype.removeChildContainer = function(container) {
	delete this._childContainers[container.getId() + ''];
};

/**
 * Add widget
 * 
 * @param Tomato.Core.Layout.Widget widget
 */
Tomato.Core.Layout.Container.prototype.addWidget = function(widget) {
	widget.setContainer(this);
	this._widgets[widget.getId() + ''] = widget;
};

/**
 * Get widget in container by its id
 * 
 * @param string id Id of widget
 * @return Tomato.Core.Layout.Widget
 */
Tomato.Core.Layout.Container.prototype.getWidget = function(id) {
	return this._widgets[id + ''];
};

/**
 * Remove a widget from container
 * 
 * @param Tomato.Core.Layout.Widget widget The widget will be removed
 */
Tomato.Core.Layout.Container.prototype.removeWidget = function(widget) {
	delete this._widgets[widget.getId() + ''];
};

/**
 * Increase height of container some pixel
 * 
 * @param int added
 */
Tomato.Core.Layout.Container.prototype.incHeight = function(added) {
	var height = parseInt($('#' + this._id).height()) + added;
	$('#' + this._id).css('height', height + 'px');
};

/**
 * Generate widget Id
 * 
 * @return string Id for new widget
 */
Tomato.Core.Layout.Container.prototype.generateWidgetId = function() {
	this._widgetUuid++;
	return this._id + '_widget_' + this._widgetUuid;
};

Tomato.Core.Layout.Container.prototype.generateTabId = function() {
	return this._id + '_tabs_' + this._tabUuid;
};

/**
 * Save handler
 */
Tomato.Core.Layout.Container.prototype.save = function() {
	var out = {
		isRoot: (this._parent == null) ? 1 : 0,
		cols: this._numColumns,
		containers: new Array(),
		widgets: new Array()
	};
	if (this._position != null) {
		out.position = this._position;
	}
	
	var self = this;
	// Don't loop through the list of child containers as follow: 
	// for (var i in this._child) {
	//     out.containers[i] = this._child[i].save();
	// } 
	// because we need to keep the order of child containers
	// (in case user drag and drop container)
	$('#' + this._id).children('.t_a_widget_container').each(function(i) {
		var containerId = $(this).attr('id');
		var container = self.getChildContainer(containerId);
		if (container != null) {
			out.containers[i] = container.save();
		}
	});

	$('#' + this._id).children('.t_a_widget').each(function(i) {
		var widgetId = $(this).attr('id');
		var widget = self.getWidget(widgetId);
		if (widget != null) {
			out.widgets[i] = widget.save();
		}
	});
	return out;
};

/**
 * Load child containers and widgets
 * 
 * @param object data Child containers and widgets serialized
 */
Tomato.Core.Layout.Container.prototype.load = function(data) {
	for (var i in data.containers) {
		var numColumns = data.containers[i].cols;
		var childContainer = this.append(numColumns);
		if (childContainer != null) {
			childContainer.load(data.containers[i]);
		}
	}
	var widgetId, widget;
	for (i in data.widgets) {
		widgetId = this.generateWidgetId();
		// TODO: Make a factory or reflection
		switch (data.widgets[i].cls) {
			case 'Tomato.Core.Layout.DefaultOutput':
				widget = new Tomato.Core.Layout.DefaultOutput(widgetId, this);	
				break;
			case 'Tomato.Core.Layout.Widget':
			default:
				widget = new Tomato.Core.Layout.Widget(widgetId, this);
				break;
		}
		this.addWidget(widget);

		widget.setModule(data.widgets[i].module);
		widget.setName(data.widgets[i].name);
		widget.setTitle(data.widgets[i].title);
		widget.render(data.widgets[i].params);
	}
};

/** 
 * Toggle preview/config mode for container.
 * In case, user want to preview a container without performing preview action for
 * each widgets belong to it.
 * 
 * @param string mode Can be PREVIEW or CONFIG
 */
Tomato.Core.Layout.Container.prototype.toggleMode = function(mode) {
	switch (mode) {
		case 'PREVIEW':
			$('#' + this._id).children('.t_a_widget_container_head').hide();
			$('#' + this._id).css({backgroundColor: ''});
			break;
		case 'CONFIG':
			$('#' + this._id).children('.t_a_widget_container_head').show();
			// Restore background color
			$('#' + this._id).css({backgroundColor: this._bgColor});
			break;
	}
	for (var i in this._child) {
		this._child[i].toggleMode(mode);
	}
	var self = this;
	$('#' + this._id).children('.t_a_widget').each(function(i) {
		var widgetId = $(this).attr('id');
		var widget = self.getWidget(widgetId);
		if (widget != null) {
			widget.toggleMode(mode);
		}
	});
};

/* ========== Tomato.Core.Layout.Tabs ======================================= */

/**
 * This class represent a tab container
 */
Tomato.Core.Layout.Tabs = function(id, container) {
	this._id = id;
	this._container = container;
	this._defaultTitle = "Tab";
	// Array of tabs
	this._tabs = new Array();
};

/**
 * Getters/Setters
 */
Tomato.Core.Layout.Tabs.prototype.getId = function() { return this._id; };
Tomato.Core.Layout.Tabs.prototype.setDefaultTitle = function(title) { this._defaultTitle = title; };
Tomato.Core.Layout.Tabs.prototype.addTab = function(name) {
	
};
Tomato.Core.Layout.Tabs.prototype.removeTab = function(name) {
	
};

/**
 * Render a tabs container
 */
Tomato.Core.Layout.Tabs.prototype.render = function() {
	$('<div id="' + this._id + '" class="t_tab_container"><ul><li><a href="#"><span>' + this._defaultTitle + '</span></a></li><li><a href="#tTab2"><span>' + this._defaultTitle + '</span></a></li></ul><div class="t_g_top" id="tTab1"></div><div class="t_g_top" id="tTab2"></div></div>')
		.appendTo($('#' + this._container.getId()));
	$('#' + this._id).tabs();
};

/* ========== Tomato.Core.Layout.Widget ===================================== */

/**
 * This class represent a widget. Each widget belong to a container.
 */

/**
 * Create new widget
 * 
 * @param string id Id of the DIV element that contain widget
 * @param Tomato.Core.Layout.Container container Widget container
 */
Tomato.Core.Layout.Widget = function(id, container) {
	this._id = id;
	this._container = container;
	this._module = null;
	this._name = null;
	this._title = null;
	
	this._resources = {};
	this._class = 'Tomato.Core.Layout.Widget';
	
	/**
	 * Mode of widget. It can take one of two values: 
	 * CONFIG if user are performing config widget (default mode)
	 * or PREVIEW if user are previewing widget
	 */
	this._mode = 'CONFIG';
};

/**
 * DO NOT CHANGE THESE VALUES
 * They define values for some special parameters
 */
Tomato.Core.Layout.Widget.CACHE_LIFETIME_PARAM 	= '___cacheLifetime';
Tomato.Core.Layout.Widget.LOAD_AJAX_PARAM 		= '___loadAjax';
Tomato.Core.Layout.Widget.PREVIEW_MODE_PARAM 	= '___widgetPreviewMode';

/**
 * Getters/Setters
 */
Tomato.Core.Layout.Widget.prototype.getId = function() { return this._id; };
Tomato.Core.Layout.Widget.prototype.setModule = function(module) { this._module = module; };
Tomato.Core.Layout.Widget.prototype.setName = function(name) { this._name = name; };
Tomato.Core.Layout.Widget.prototype.setTitle = function(title) { this._title = title; };
Tomato.Core.Layout.Widget.prototype.getContainer = function() { return this._container; };
Tomato.Core.Layout.Widget.prototype.setContainer = function(container) { this._container = container; };

/**
 * Render widget
 * 
 * @param object params The object contain config data. 
 * Will be used when loading a widget has been configured
 */
Tomato.Core.Layout.Widget.prototype.render = function(params) {
	var div = document.createElement('div');
	$(div).attr('id', this._id).addClass('t_a_widget').addClass('clearfix');
	
	// Append the container
	$('#' + this._container.getId()).append(div);
	
	var self = this;
	// Load widget config
	$(div).html('').addClass('t_a_loading');//.fadeOut('slow');
	
	var data = { mod: this._module, name: this._name, act: 'config' };
	if (params != null) {
		data.params = $.toJSON(params);
	}
	
	var baseUrl = this._container.getBaseUrl();
	baseUrl = baseUrl.replace(/\/+$/,"");
	
	$.ajaxq('core_layout', {
		url: baseUrl + '/core/widget/ajax/',
		data: data,
		success: function(response) {
			response = $.evalJSON(response);
			
			self._resources.css = response.css;
			for (var i in response.css) {
				if ($('head').find('link[href="' + response.css[i] + '"]').length == 0) {
					$('<link rel="stylesheet" type="text/css" href="' + response.css[i] + '" />').appendTo('head');
				}
			}
			self._resources.javascript = response.javascript;
			for (i in response.javascript) {
				if ($('body').find('script[src="' + response.javascript[i] + '"]').length == 0) {
					$('<script type="text/javascript" src="' + response.javascript[i] + '"></script>').prependTo('body');
				}
			}
			
			$('<div class="t_a_widget_head"><h3>' + self._title + '</h3></div><div class="t_a_widget_content"><div class="t_a_widget_config">' + response.content 
						+ '<br />' + Tomato.Core.Layout.Lang.getLang('WIDGET_CACHE') + ':<br /><input type="text" style="width: 100px" name="' + Tomato.Core.Layout.Widget.CACHE_LIFETIME_PARAM + '" class="t_widget_input" />'
						+ '<br /><input type="checkbox" name="' + Tomato.Core.Layout.Widget.LOAD_AJAX_PARAM + '" class="t_widget_input" /> ' + Tomato.Core.Layout.Lang.getLang('WIDGET_LOAD_AJAX')
						+ '</div><div class="t_a_widget_preview" id ="' + $(div).attr('id') + '_preview" style="display: none"></div></div><div class="t_a_widget_bottom"><a href="javascript: void(0)" class="t_g_button">' + Tomato.Core.Layout.Lang.getLang('WIDGET_PREVIEW') + '</a></div>')
				.css('cursor', 'move').appendTo($(div));
			$(div).removeClass('t_a_loading');//.fadeIn('slow');
			
			/**
			 * Remove button
			 */
			$('<a href="javascript: void(0)" class="t_a_widget_remove">CLOSE</a>').mousedown(function(e) {
                e.stopPropagation();
            }).click(function() {
            	if (confirm(Tomato.Core.Layout.Lang.getLang('WIDGET_REMOVE_CONFIRM'))) {
            		$(this).parents('.t_a_widget').animate({
            			opacity: 0
            		}, function() {
            			$(this).wrap('<div/>').parent().slideUp(function() {
            				$(this).remove();
            				self._container.removeWidget(self);
            			});
            		});
            	}
            }).appendTo($(div).find('.t_a_widget_head'));
			
			/**
			 * Clone button
			 */
			$('<a href="javascript: void(0)" class="t_a_widget_clone">CLONE</a>').mousedown(function(e) {
                e.stopPropagation();
            }).click(function() {
            	var widgetId = self._container.generateWidgetId();
            	
            	// TODO: Use clone() method from jQuery, so we have not load data for widget again
//            	var clone = $(div).clone(true).attr('id', widgetId);
//            	console.log('=== Cloning widget: srcId=' + self._id + '==> desId=' + widgetId);
//            	$(clone).find('div.t_a_widget_preview:first').attr('id', widgetId + '_preview');
//            	$(clone).appendTo($(div).parent());
            	
            	var widget = new Tomato.Core.Layout.Widget(widgetId, self._container);
				widget.setModule(self._module);
				widget.setName(self._name);
				widget.setTitle(self._title);
				self._container.addWidget(widget);
				
				widget.render();
            }).appendTo($(div).find('.t_a_widget_head'));
			
			/**
			 * Collapse button
			 */
			$('<a href="javascript: void(0)" class="t_a_widget_collapse">COLLAPSE</a>').mousedown(function (e) {
                e.stopPropagation();  
            }).toggle(function() {
            	$(this).css({backgroundPosition: '-28px 0'}).parents('.t_a_widget').find('.t_a_widget_content, .t_a_widget_bottom').show();
            	return false;
            }, function() {
            	$(this).css({backgroundPosition: ''}).parents('.t_a_widget').find('.t_a_widget_content, .t_a_widget_bottom').hide();
            	return false;
            }).prependTo($(div).find('.t_a_widget_head'));
			
			$(div).find('div.t_a_widget_bottom a').toggle(function() {
				self.toggleMode('PREVIEW');
			}, function() {
				self.toggleMode('CONFIG');
			});
			
			// Increase container height
//			self._container.incHeight($(div).height());
			
			// Init data for widget if any
			if (params != null) {
				var data = {};
				for (var paramName in params) {
					data = params[paramName];
					$('#' + self._id).find('.t_widget_input[name="' + paramName + '"]').each(function() {
						if (($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') && data.value != '') {
							$(this).attr('checked', true);
						} else {
							$(this).val(data.value);
						}
					});
					if (data.type != undefined && data.type == 'global') {
						$('#' + self._id).find('.t_widget_input_global[type="checkbox"][name="global_' + paramName + '"]').attr('checked', 'checked');
					}
				}
			}
		}
	});
};

/**
 * Preview widget
 */
Tomato.Core.Layout.Widget.prototype.preview = function() {
	this._mode = 'PREVIEW';	
	var params = {};
	
	// Add a param named '__widgetPreviewMode' to indicate that
	// we are previewing widget in backend, not frontend
	params[Tomato.Core.Layout.Widget.PREVIEW_MODE_PARAM] = true;
	
	params.container = $('#' + this._id).find('.t_a_widget_preview:first').attr('id');
	$('#' + this._id).find('.t_widget_input, .t_widget_input_for_preview').each(function() {
		if ($(this).attr('checked') == true || 
				($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio')) { 
			params[$(this).attr('name')] = $(this).attr('value');
		}
	});
	params = $.toJSON(params);
	
	$('#' + this._id).find('div.t_a_widget_content').show().addClass('t_a_loading');
	$('#' + this._id).find('div.t_a_widget_config:first').hide();
	$('#' + this._id).find('div.t_a_widget_preview:first').show().html('');
	var self = this;
	var baseUrl = this._container.getBaseUrl();
	baseUrl = baseUrl.replace(/\/+$/,"");
	$.ajaxq('core_layout', {
		url: baseUrl + '/core/widget/ajax/',
		data: { mod: this._module, name: this._name, params: params },
		success: function(response) {
			response = $.evalJSON(response);
			for (var i in response.css) {
				if ($('head').find('link[href="' + response.css[i] + '"]').length == 0) {
					$('<link rel="stylesheet" type="text/css" href="' + response.css[i] + '" />').appendTo('head');
				}
			}
			for (i in response.javascript) {
				if ($('head').find('script[src="' + response.javascript[i] + '"]').length == 0) {
					$('<script type="text/javascript" src="' + response.javascript[i] + '"></script>').appendTo('head');//.prependTo('body');
				}
			}
			$('#' + self._id).find('div.t_a_widget_content').css('background-color', '#C5C5C5').removeClass('t_a_loading');
			$('#' + self._id).find('div.t_a_widget_preview:first').show().html(response.content);
		}
	});
};

/**
 * Save handler
 */
Tomato.Core.Layout.Widget.prototype.save = function() {
	var out = {
		cls: this._class,
		module: this._module,
		name: this._name,
		title: this._title,
		resources: this._resources
	};
	var params = {};
	var self = this, v;
	$('#' + self._id).find('.t_widget_input').each(function() {
		if ($(this).attr('name') == Tomato.Core.Layout.Widget.LOAD_AJAX_PARAM) {
			v = ($(this).attr('checked') == true) ? 1 : '';
		} else {
			v = $(this).attr('value');
		}
		params[$(this).attr('name')] = {
			value: v,
			type: ''
		};
		// Allow user to set param value will be taken from request
		if ($('#' + self._id).find('.t_widget_input_global[type="checkbox"][checked][name="global_' + $(this).attr('name') + '"]').length > 0) {
			params[$(this).attr('name')].type = 'global';
		}
	});
	out.params = params;
	return out;
};

/**
 * Toggle preview/config mode for widget
 * 
 * @param string mode New mode, can be PREVIEW or CONFIG
 */
Tomato.Core.Layout.Widget.prototype.toggleMode = function(mode) {
	switch (mode) {
		case 'PREVIEW':
			if (this._mode == 'PREVIEW') {
				// The widget is currently in the preview mode, do nothing
			} else {
				// Because of using clone() method above, we have to get id as follow
				// The method this.preview() will not work
//				var widgetId = $(this).parents('.t_a_widget').attr('id');
//				console.log("preview widget id=" + widgetId);
//				this._container.getWidget(widgetId).preview();
				this.preview();
				$('#' + this._id).find('div.t_a_widget_bottom a').html(Tomato.Core.Layout.Lang.getLang('WIDGET_BACK'));
			}
			break;
		case 'CONFIG':
			$('#' + this._id).find('div.t_a_widget_bottom a').html(Tomato.Core.Layout.Lang.getLang('WIDGET_PREVIEW'));
			$('#' + this._id).find('div.t_a_widget_config:first').show();
			$('#' + this._id).find('div.t_a_widget_preview:first').hide();
			$('#' + this._id).find('div.t_a_widget_content').removeClass('t_a_loading');
			
			this._mode = 'CONFIG';
			break;
	}
};

/* ========== Tomato.Core.Layout.DefaultOutput ============================== */

Tomato.Core.Layout.DefaultOutput = function(id, container) {
	this._id = id;
	this._container = container;
	this._class = 'Tomato.Core.Layout.DefaultOutput';
};
Tomato.Core.Layout.DefaultOutput.prototype = new Tomato.Core.Layout.Widget();
Tomato.Core.Layout.DefaultOutput.prototype.render = function() {
	var div = document.createElement('div');
	$(div).attr('id', this._id).addClass('t_a_widget')
		.addClass('t_g_output').css('height', '140px')
		.addClass('clearfix');
	$('<div class="t_a_widget_head"><h3>' + Tomato.Core.Layout.Lang.getLang('DEFAULT_OUTPUT') + '</h3></div>').css('cursor', 'move').appendTo($(div));
	$('#' + this._container.getId()).append(div);
};
