/*
 * jQuery treeTable Plugin VERSION
 * http://ludo.cubicphuse.nl/jquery-plugins/treeTable/doc/
 *
 * Copyright 2011, Ludo van den Boom
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */
(function($){
	var PLUGING = 'wtTreeTable'; // The Plugin Name
	var defaultPaddingLeft;
	var persistStore;
	var defaults = {
		childPrefix : "child-of-",
		clickableNodeNames : false,
		expandable : true,
		indent : 40,
		initialState : "collapsed",
		onNodeShow : null,
		onNodeHide : null,
		onExpand : null,
		onCollapse : null,
		treeColumn : 0,
		persist : false,
		persistStoreName : 'treeTable',
		stringExpand : "Expand",
		stringCollapse : "Collapse"
	};
	
	// Public Methods
	var methods = {
		'init': function(opts) {
			var options = $(this).data(PLUGING);
			if(options){
				return $(this);
			}
			
			options = $.extend({}, defaults, opts);
			if (options.persist) {
				persistStore = new Persist.Store(options.persistStoreName);
			}
			$(this).data(PLUGING, options);
			
			return this.addClass("treeTable").find("tbody tr").each(function() {
				$(this).data(PLUGING, options);
			}).each(function() {
				var isRootNode = ($(this)[0].className.search(options.childPrefix) == -1);
				
				// To optimize performance of indentation, I retrieve the padding-left value of the first root node.
				// This way I only have to call +css+ once.
				if (isRootNode && isNaN(defaultPaddingLeft)) {
					defaultPaddingLeft = parseInt($($(this).children("td")[options.treeColumn]).css('padding-left'), 10);
				}
				
				// Set child nodes to initial state if we're in expandable mode.
				if (!isRootNode && options.expandable && options.initialState == "collapsed") {
					$(this).addClass('ui-helper-hidden');
				}
				
				// If we're not in expandable mode, initialize all nodes.
				// If we're in expandable mode, only initialize root nodes.
				if (!options.expandable || isRootNode) {
					initialize($(this));
				}
			});
		},
		
		// Expand all nodes
		'expandAll': function() {
			$(this).find("tbody>tr").each(function() {
				$(this)[PLUGING]('expand');
			});
		},
		
		// Collapse all nodes
		'collapseAll': function() {
			$(this).find("tbody>tr").each(function() {
				$(this)[PLUGING]('collapse');
			});
		},
		
		// Recursively hide all node's children in a tree
		'collapse': function() {
			var options = $(this).data(PLUGING);
			
			return this.each(function() {
				if($.isFunction(options.onCollapse))
					options.onCollapse.call(this);			
				
				$(this).removeClass("expanded").addClass("collapsed");

				if (options.persist) {
					persistNodeState($(this));
				}

				childrenOf($(this)).each(function() {
					if (!$(this).hasClass("collapsed")) {
						$(this)[PLUGING]('collapse');
					}

					$(this).addClass('ui-helper-hidden');

					if ($.isFunction(options.onNodeHide)) {
						options.onNodeHide.call(this);
					}
				});
			});
		},
		
		// Recursively show all node's children in a tree
		'expand': function() {
			
			return $(this).each(function() {
				var options = $(this).data(PLUGING);
				if($.isFunction(options.onExpand))
					options.onExpand.call(this);
				
				$(this).removeClass("collapsed").addClass("expanded");

				if(options.persist) {
					persistNodeState($(this));
				}
				childrenOf($(this)).each(function() {
					
					initialize($(this));
					if($(this).is(".expanded.parent")) {
						$(this)[PLUGING]('expand');
					}
					$(this).removeClass('ui-helper-hidden');

					if($.isFunction(options.onNodeShow)) {
						options.onNodeShow.call(this);
					}
				});
			});
		},
		
		// Reveal a node by expanding all ancestors
		'reveal': function() {
			$(ancestorsOf($(this)).reverse()).each(function() {
				initialize($(this));
				$(this)[PLUGING]('expand').show();
			});
			return this;
		},
		
		'move': function(destination) {
			var node = $(this);
			var parent = parentOf(node);
			
			if($(destination).hasClass('parent')) {
				$(this)[PLUGING]('appendBranchTo', destination);
				$(destination)[PLUGING]('expand');
			}
			else {
				$(this)[PLUGING]('insertTo', destination);
			}
		},
		
		// Add an entire branch to +destination+
		'appendBranchTo': function(destination, prepend) {
			var node = $(this);
			var parent = parentOf(node);
			var options = node.data(PLUGING);
			var ancestorNames = $.map(ancestorsOf($(destination)), function(a) {
				return a.id;
			});

			// Conditions:
			// 1: +node+ should not be inserted in a location in a branch if this would result in +node+ being an ancestor of itself.
			// 2: +node+ should not have a parent OR the destination should not be the 
			//		same as +node+'s current parent (this last condition prevents +node+ from being moved to the same location where it already is).
			// 3: +node+ should not be inserted as a child of +node+ itself.
			if ($.inArray(node[0].id, ancestorNames) == -1
				&& (!parent || (destination.id != parent[0].id))
				&& destination.id != node[0].id) 
			{
				indent(node, ancestorsOf(node).length * options.indent * -1); // Remove indentation

				if (parent) {
					node.removeClass(options.childPrefix + parent[0].id);
				}

				node.addClass(options.childPrefix + $(destination).attr('id'));
				if(!prepend)
					move(node, destination); // Recursively move nodes to new location
				indent(node, ancestorsOf(node).length * options.indent);
				if(childrenOf($(destination)).length>0)
					addExpander($(destination));
				
				if(parent && childrenOf(parent).length==0) {
					removeExpander(parent);
				}
			}

			return this;
		},
		
		// Move to the after destination which is not parent
		'insertTo': function(destination) {
			var node = $(this);
			var parent = parentOf(node);
			var options = node.data(PLUGING);
			var ancestorNames = $.map(ancestorsOf($(destination)), function(a) {
				return a.id;
			});
			
			// Conditions:
			// 1: +node+ should not be inserted in a location in a branch if this would result in +node+ being an ancestor of itself.
			// 2: +node+ should not have a parent OR the destination should not be the 
			//		same as +node+'s current parent (this last condition prevents +node+ from being moved to the same location where it already is).
			// 3: +node+ should not be inserted as a child of +node+ itself.
			if ($.inArray(node[0].id, ancestorNames) == -1
				&& (!parent || (destination.id != parent[0].id))
				&& destination.id != node[0].id) 
			{
				$(destination).after(node);
				indent(node, ancestorsOf(node).length * options.indent * -1);
				
				if(parent) {
					node.removeClass(options.childPrefix + parent[0].id);
				}
				if(parentOf(node))
					node.removeClass(options.childPrefix + parentOf(node)[0].id);
				if(parentOf($(destination))) {
					node.addClass(options.childPrefix + parentOf($(destination))[0].id);
				}
				
				indent(node, ancestorsOf(node).length * options.indent);
				move(node, destination);

				if(childrenOf($(destination)).length>0)
					addExpander($(destination));
				if(parent && childrenOf(parent).length==0) {
					removeExpander(parent);
				}
			}

			return this;
		},
		
		'insertAsRoot': function() {
			var node = $(this);
			var parent = parentOf(node);
			var options = node.data(PLUGING);
			if(!parent) return this;		// node is root
			var ancestors = ancestorsOf(node);
			
			ancestor = $(ancestors[ancestors.length-1]);
			ancestor.before(node);
			indent(node, ancestorsOf(node).length * options.indent * -1);
			move(node);
			node.removeClass(options.childPrefix + parent[0].id);

			if(parent && childrenOf(parent).length==0) {
				removeExpander(parent);
			}
			return this;
		},
		
		// Add reverse() function from JS Arrays
		'reverse': function() {
			return this.pushStack(this.get().reverse(), arguments);
		},
		
		// Toggle an entire branch
		'toggleBranch': function() {
			var node = $(this);
			if (node.hasClass("collapsed")) {
				node[PLUGING]('expand');
			} else {
				node[PLUGING]('collapse');
			}
			return this;
		},
		
		'outdent': function() {
			var node = $(this);
			var ancestor = ancestorsOf(node);
			
			if(!ancestor.length)
				return;
			if(ancestor.length==1)
				node[PLUGING]('insertAsRoot');
			else
				node[PLUGING]('move', ancestor[1]);
			node[PLUGING]('down');
		},
		
		'indent': function() {
			var node = $(this);
			var ancestor = ancestorsOf(node);
			var prev = prevOf(node);

			if(prev) {
				node[PLUGING]('appendBranchTo', prev[0], true);
				parentOf(node)[PLUGING]('expand');
			}
		},
		
		'up': function() {
			var node = $(this);
			var prev = prevOf(node);
			if(!prev) {
				if(parentOf(node)) {
					node[PLUGING]('outdent');
				}
			}
			else {
				moveBefore(node, prev);
			}
			return this;
		},
		
		'down': function() {
			var node = $(this);
			var next = nextOf(node);
			if(next) {
				var children = childrenOf(next);
				moveBefore(next, node);
			}
			return this;
		},
		
		'insert': function(id, tpl) {
			var node = $(this);
			var options = node.data(PLUGING);
			var tpl = $(tpl).attr('id', id).data(PLUGING, options);
			if(node.get(0).tagName.toLowerCase() == "table"){
				node.find('tbody').append(tpl);
				initialize(tpl);
				return tpl;
			}
			else{
				node.after(tpl);
				initialize(tpl);
				tpl[PLUGING]('insertTo', node);
				return tpl;
			}
		},
		
		'delete': function() {
			var node = $(this);
			var parent = parentOf(node);
			var del_sn = del(node);
			if(parent && childrenOf(parent).length == 0) {
				removeExpander(parent);
			}
			return del_sn;
		},
		
		'childrenOf': function() {
			return childrenOf($(this));
		},

		'parentOf': function() {
			return parentOf($(this));
		},

		'ancestorsOf': function() {
			return ancestorsOf($(this));
		},
		
		'destroy': function(){
			$(this).unbind('.'+PLUGING).removeData(PLUGING);
		}
	};

	// Private functions
	function removeExpander(node) {
		var options = node.data(PLUGING);
		node.removeClass("parent");
		node.find('td:eq('+options.treeColumn+') a.expander').remove();
	}

	function addExpander(node) {
		var options = node.data(PLUGING);
		if(node.find('td:eq('+options.treeColumn+')>a.expander').length)
			return;

		$('<a href="#" title="' + options.stringExpand + '"class="expander"></a>')
			.click(function(){
				node[PLUGING]('toggleBranch');
			})
			.prependTo(node.find('td:eq('+options.treeColumn+')'));
		node.addClass('parent');
		node[PLUGING]('expand');
	}

	function ancestorsOf(node) {
		var ancestors = [];
		while (node = parentOf(node)) {
			ancestors[ancestors.length] = node[0];
		}
		return ancestors;
	}

	function childrenOf(node) {
		var options = $(node).data(PLUGING);
		return $(node).siblings("tr." + options.childPrefix + node[0].id);
	}
	
	function getPaddingLeft(node) {
		var paddingLeft = parseInt(node[0].style.paddingLeft, 10);
		return (isNaN(paddingLeft)) ? (isNaN(defaultPaddingLeft) ? 0 : defaultPaddingLeft) : paddingLeft;
	}

	function indent(node, value) {
		var options = $(node).data(PLUGING);
		var cell = $(node.children("td")[options.treeColumn]);
		cell[0].style.paddingLeft = getPaddingLeft(cell) + value + "px";
		childrenOf(node).each(function() {
			indent($(this), value);
		});
	}

	function initialize(node) {
		if($(node).hasClass('initialized'))
			return;
		var options = $(node).data(PLUGING);
		
		node.addClass("initialized");
		
		var childNodes = childrenOf(node);
		
		if (!node.hasClass("parent") && childNodes.length > 0) {
			node.addClass("parent");
		}
		
		if (node.hasClass("parent")) {
			
			var cell = $(node.children("td")[options.treeColumn]);
			var padding = getPaddingLeft(cell) + options.indent;
			childNodes.each(function() {
				$(this).children("td")[options.treeColumn].style.paddingLeft = padding + "px";
			});
			
			if (options.expandable) {
				var newLink = '<a href="#" title="' + options.stringExpand + '" class="expander"></a>';

				if (options.clickableNodeNames) {
					cell.wrapInner(newLink);
				} else if(!node.find('a.expander:first').length){
					$(newLink).click(function(){
						node[PLUGING]('toggleBranch');
					}).prependTo(cell); 
				}
				
				$(cell[0].firstChild).click(function(e) {
					if (e.target.className != 'expander') {
						node[PLUGING]('toggleBranch');
					}
					return false;
				}).mousedown(function() {
					return false;
				});
				$(cell[0].firstChild).keydown(function(e) {
					if (e.keyCode == 13) {
						node[PLUGING]('toggleBranch');
						return false;
					}
				});

				if (options.clickableNodeNames) {
					cell[0].style.cursor = "pointer";
					$(cell).click(function(e) {
						// Don't double-toggle if the click is on the existing expander icon
						if (e.target.className != 'expander') {
							node[PLUGING]('toggleBranch');
						}
					});
				}
				
				if(options.persist){
					node[PLUGING](getPersistedNodeState(node)?'expand':'collapse');
				}

				// Check for a class set explicitly by the user, otherwise set the default class
				if (!(node.hasClass("expanded") || node.hasClass("collapsed"))) {
					node.addClass(options.initialState);
				}
				
				if(node.hasClass("expanded")) {
					node[PLUGING]('expand');
				}
				else if(node.hasClass("collapsed")){
					node[PLUGING]('collapse');
				}
			}
		}
	}

	function move(node, destination) {
		if(destination){
			node.insertAfter(destination);
		}
			
		childrenOf(node)[PLUGING]('reverse').each(function() {
			move($(this), node[0]);
		});
	}

	function del(node) {
		var del_sn_arr = new Array();
		childrenOf(node).each(function() {
			del_sn_arr = $.merge(del_sn_arr, del($(this)));
		});
		node.remove();
		del_sn_arr.push(node.attr('sn'));
		return del_sn_arr;
	}
	
	function moveBefore(node, destination) {
		if(destination)
			node.insertBefore(destination);
		childrenOf(node)[PLUGING]('reverse').each(function() {
			move($(this), node[0]);
		});
	}

	function parentOf(node) {
		var options = $(node[0]).data(PLUGING);
		var classNames = node[0].className.split(' ');
		for ( var key = 0; key < classNames.length; key++) {
			if (classNames[key].match(options.childPrefix)) {
				return $(node).siblings("#" + classNames[key].substring(options.childPrefix.length));
			}
		}
		return null;
	}

	// saving state functions, not critical, so will not generate alerts on error
	function persistNodeState(node) {
		if (node.hasClass('expanded')) {
			try {
				persistStore.set(node.attr('id'), '1');
			} catch (err) {

			}
		} else {
			try {
				persistStore.remove(node.attr('id'));
			} catch (err) {

			}
		}
	}

	function getPersistedNodeState(node) {
		try {
			return persistStore.get(node.attr('id')) == '1';
		} catch (err) {
			return false;
		}
	}

	function prevOf(node) {
		var parent = parentOf(node);
		var o = null;

		$(node).prevAll().each(function(){
			var p = parentOf($(this));
			if((!parent && !p) || (parent && p && parent[0].id==p[0].id)) {
				o = $(this);
				return false;
			}
		});
		return o;
	}

	function nextOf(node) {
		var parent = parentOf(node);
		var o = null;

		$(node).nextAll().each(function(){
			var p = parentOf($(this));
			if((!parent && !p) || (parent && p && parent[0].id==p[0].id)) {
				o = $(this);
				return false;
			}
		});
		return o;
	}
	/* Method calling logic */
	$.fn[PLUGING] = function(method) {
		if (methods[method]) {
	 		return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || ! method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' +  method + ' does not exist on '+PLUGING);
		}
	};
})(jQuery);