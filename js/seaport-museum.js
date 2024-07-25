/**
 * Seaport Museum entry point.
 *
 * @package GenesisSample\JS
 * @author  StudioPress

 */
const seaportMuseum = (function ($) {
  /**
   * Adjust site inner margin top to compensate for sticky header height.
   *
   * @since 2.6.0
   */
  let searchClick = function () {
    const cutoffWidth = 768;

    let windowWidth = $( window ).width();
    let searchInput, searchForm, searchValue
    let isMobile = false

    //desktop and tablet wide experience
    if (windowWidth >= cutoffWidth) {
      searchInput = $('input.search-field');
      searchForm = $('#search-form');

      searchValue = jQuery.trim(searchInput.val());

      if (searchValue === '') {
        if (searchInput.data('active') === 1) {
          searchInput.data('active', 0);
        } else {
          searchInput.data('active', 1);
          searchInput.focus();
        }
      } else {
        searchForm.submit();
      }
    } else {
      //mobile experience uses an alternate input
      searchInput = $('input.search-field-mobile');
      searchForm = $('#search-form-mobile');
 
      let searchValue = jQuery.trim(searchInput.val());
      let visible = $('div.search-container-mobile').hasClass("search-container-mobile-show");

      console.log("visible", visible);

      if (searchValue === '') {
        if (searchInput.data('active') === 1) {
          searchInput.data('active', 0);
        } else {
          searchInput.data('active', 1);
          searchInput.focus();
        }
      } else {
        searchForm.submit();
      }

      if (visible) {
        hideMobileSearch()
      } else {
        showMobileSearch()
        searchInput.focus();
        return;
      }
      //setTimeout("jQuery('div.search-container-mobile').focus()", 50);
    }

    let val = jQuery.trim(searchInput.val());

    if (val === '') {
        if (searchInput.data('active') === 1) {
          searchInput.data('active', 0);
        } else {
          searchInput.data('active', 1);
          searchInput.focus();
        }
    } else {
      searchForm.submit();
    }
  };

  let showMobileSearch = function() {
    console.log("showMobileSearch");
    $('div.search-container-mobile').removeClass('search-container-mobile-hide').addClass('search-container-mobile-show')
  }

  let hideMobileSearch = function() {
    console.log("hideMobileSearch")
    $('div.search-container-mobile').addClass('search-container-mobile-hide').removeClass('search-container-mobile-show')
  }

  let setHideMobileSearch = function () {
    $('div.search-container-mobile').on('focusout', function() {
      hideMobileSearch()
    })
  }

  let initializeSidebar = function () {
    //https://abouolia.github.io/sticky-sidebar/#installation

    //min width to trigger the floating aside menu
    //only at > 960 do we show the sidebar as a floating side, smaller screens it is inline before the rest of the content
    let minWidth = 960;

    if ($(window).width() > minWidth) {
      //window is wider, so add the sidebar scrolling logic if it's not already initialized
      if (($('.archive-wrapper').length) && (typeof sidebarNav === 'boolean')) {

        sidebarNav = new StickySidebar('.sidebar', {
          topSpacing: 185,
          bottomSpacing: 40,
          containerSelector: '.archive-wrapper',
          innerWrapperSelector: '.sidebar__inner'
        });
      }
    } else {
      //window is narrower, so remove sidebar scrolling logic
      //this resets the sidebar menu to remain in document flow
      if (typeof sidebarNav === 'object') {
        sidebarNav.destroy();

      }

      //reset sidebar state
      sidebarNav = false;

    }

  };
  let _redrawInner = function (html, timer, duration) {
    clearInterval(timer);

    $('div.site-inner').html(html);

    $('.block-post-grid--post').hide();

    let delay = 0;

    $('.block-post-grid--post').each(function (i) {

      delay = (i * duration) + duration;

      $(this).delay(delay).fadeIn(duration * 2);

    });

    setTimeout(seaportMuseum.initializeSidebar, delay + duration);
  };

  let scrollContentTop = function () {
    let top = $('div.site-inner').offset().top - $('header').height() - 10;

    if ($(window).scrollTop() > top) {

      $('html, body').animate({
        scrollTop: (top)
      }, 500);

    }
  };
  'use strict';

  /**
   * Adjust site inner margin top to compensate for sticky header height.
   *
   * @since 2.6.0
   */
  let headerUpdateTimer,

      pageAtTop = true,

      sidebarNav = false,

      /**
       * Change Header Class on page scroll
       */
      updateNavClass = function () {
        clearTimeout('headerUpdateTimer');

        headerUpdateTimer = setTimeout('seaportMuseum.swapHeaderClass()', 200);
      },

      swapHeaderClass = function () {

        if (pageAtTop) {
          $('header').removeClass('nav-scrolled').addClass('nav-top');
          $('.nav-actions').removeClass('nav-scrolled').addClass('nav-top');
          $('.action-link.button').removeClass('nav-scrolled').addClass('nav-top');
        } else {
          $('header').addClass('nav-scrolled').removeClass('nav-top');
          $('.nav-actions').addClass('nav-scrolled').removeClass('nav-top');
          $('.action-link.button').addClass('nav-scrolled').removeClass('nav-top');
        }
      },

      detectScroll = function () {

        //initializeSidebar();

        if ($(document).width() < 961) {
          pageAtTop = true;
          updateNavClass();

          return;
        }

        let currentStatus = ($(document).scrollTop().valueOf() === 0);

        if (currentStatus !== pageAtTop) {
          pageAtTop = ($(document).scrollTop().valueOf() < 400);
          updateNavClass();
        }

      },

      /**
       * Internal functions to execute on full page load.
       *
       * @since 1.0.0
       */
      load = function () {

        $('#search-submit').click(function () {
          searchClick();
        });

        setHideMobileSearch();

        swapHeaderClass();

        initializeSidebar();

      },

      setEventGridClick = function () {

        if (jQuery('.block-post-grid--post').length === 0) return;

        jQuery('.block-post-grid--post').each(function () {

          //set the click event on the grid
          jQuery(this).on('click', function () {

            const url = jQuery(this).data('url');

            console.log(url);

            //are we hovering? if so, don't do the click, let the buttons work instead
            if (jQuery(this).is(':hover')) return true;

            window.location = url;
          });

        });

      },

      categoryNavigation = function (url) {

        history.pushState(null, null, url)

        scrollContentTop();

        let redraw = false, duration = 50;

        let count = $('.block-post-grid--post').length;

        setTimeout(function () {
          redraw = true;

        }, (count + 1) * duration);

        $('.block-post-grid--post').each(function (i) {
          $(this).delay((count - i) * duration).fadeOut(duration * 2);
        });

        url = url + '?body';

        jQuery.get(url, function (data) {

          let timer = setInterval(function () {

            if (redraw) {
              _redrawInner(data, timer, duration);

              //destroy sideBar, it has to be recreated again
              if (typeof sidebarNav === 'object') {
                sidebarNav.destroy();
              }

              //reset sidebar state
              sidebarNav = false;
            }
          }, 100);

        })

      };

  // Expose the load and ready functions.
  return {
    load: load,
    detectScroll: detectScroll,
    swapHeaderClass: swapHeaderClass,
    setEventGridClick: setEventGridClick,
    initializeSidebar: initializeSidebar,
    categoryNavigation: categoryNavigation,
    scrollContentTop: scrollContentTop
  };

}(jQuery));

jQuery(window).on('load', function () {
  seaportMuseum.setEventGridClick();
  seaportMuseum.load();
});

jQuery(window).scroll(seaportMuseum.detectScroll);

seaportMuseum.detectScroll();

/* Sticky Sidebar, copied from original file */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
      (factory((global.StickySidebar = {})));
}(this, (function (exports) { 'use strict';
  
  var commonjsGlobal = typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};
  
  
  
  function unwrapExports (x) {
    return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
  }
  
  function createCommonjsModule(fn, module) {
    return module = { exports: {} }, fn(module, module.exports), module.exports;
  }
  
  var stickySidebar = createCommonjsModule(function (module, exports) {
    (function (global, factory) {
      if (typeof undefined === "function" && undefined.amd) {
        undefined(['exports'], factory);
      } else {
        factory(exports);
      }
    })(commonjsGlobal, function (exports) {
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      
      function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
          throw new TypeError("Cannot call a class as a function");
        }
      }
      
      var _createClass = function () {
        function defineProperties(target, props) {
          for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
          }
        }
        
        return function (Constructor, protoProps, staticProps) {
          if (protoProps) defineProperties(Constructor.prototype, protoProps);
          if (staticProps) defineProperties(Constructor, staticProps);
          return Constructor;
        };
      }();
      
      /**
       * Sticky Sidebar JavaScript Plugin.
       * @version 3.3.4
       * @author Ahmed Bouhuolia <a.bouhuolia@gmail.com>
       * @license The MIT License (MIT)
       */
      var StickySidebar = function () {
        
        // ---------------------------------
        // # Define Constants
        // ---------------------------------
        //
        var EVENT_KEY = '.stickySidebar';
        var DEFAULTS = {
          /**
           * Additional top spacing of the element when it becomes sticky.
           * @type {Numeric|Function}
           */
          topSpacing: 0,
          
          /**
           * Additional bottom spacing of the element when it becomes sticky.
           * @type {Numeric|Function}
           */
          bottomSpacing: 0,
          
          /**
           * Container sidebar selector to know what the beginning and end of sticky element.
           * @type {String|False}
           */
          containerSelector: false,
          
          /**
           * Inner wrapper selector.
           * @type {String}
           */
          innerWrapperSelector: '.inner-wrapper-sticky',
          
          /**
           * The name of CSS class to apply to elements when they have become stuck.
           * @type {String|False}
           */
          stickyClass: 'is-affixed',
          
          /**
           * Detect when sidebar and its container change height so re-calculate their dimensions.
           * @type {Boolean}
           */
          resizeSensor: true,
          
          /**
           * The sidebar returns to its normal position if its width below this value.
           * @type {Numeric}
           */
          minWidth: false
        };
        
        // ---------------------------------
        // # Class Definition
        // ---------------------------------
        //
        /**
         * Sticky Sidebar Class.
         * @public
         */
        
        var StickySidebar = function () {
          
          /**
           * Sticky Sidebar Constructor.
           * @constructor
           * @param {HTMLElement|String} sidebar - The sidebar element or sidebar selector.
           * @param {Object} options - The options of sticky sidebar.
           */
          function StickySidebar(sidebar) {
            var _this = this;
            
            var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
            
            _classCallCheck(this, StickySidebar);
            
            this.options = StickySidebar.extend(DEFAULTS, options);
            
            // Sidebar element query if there's no one, throw error.
            this.sidebar = 'string' === typeof sidebar ? document.querySelector(sidebar) : sidebar;
            if ('undefined' === typeof this.sidebar) throw new Error("There is no specific sidebar element.");
            
            this.sidebarInner = false;
            this.container = this.sidebar.parentElement;
            
            // Current Affix Type of sidebar element.
            this.affixedType = 'STATIC';
            this.direction = 'down';
            this.support = {
              transform: false,
              transform3d: false
            };
            
            this._initialized = false;
            this._reStyle = false;
            this._breakpoint = false;
            
            // Dimensions of sidebar, container and screen viewport.
            this.dimensions = {
              translateY: 0,
              maxTranslateY: 0,
              topSpacing: 0,
              lastTopSpacing: 0,
              bottomSpacing: 0,
              lastBottomSpacing: 0,
              sidebarHeight: 0,
              sidebarWidth: 0,
              containerTop: 0,
              containerHeight: 0,
              viewportHeight: 0,
              viewportTop: 0,
              lastViewportTop: 0
            };
            
            // Bind event handlers for referencability.
            ['handleEvent'].forEach(function (method) {
              _this[method] = _this[method].bind(_this);
            });
            
            // Initialize sticky sidebar for first time.
            this.initialize();
          }
          
          /**
           * Initializes the sticky sidebar by adding inner wrapper, define its container,
           * min-width breakpoint, calculating dimensions, adding helper classes and inline style.
           * @private
           */
          
          
          _createClass(StickySidebar, [{
            key: 'initialize',
            value: function initialize() {
              var _this2 = this;
              
              this._setSupportFeatures();
              
              // Get sticky sidebar inner wrapper, if not found, will create one.
              if (this.options.innerWrapperSelector) {
                this.sidebarInner = this.sidebar.querySelector(this.options.innerWrapperSelector);
                
                if (null === this.sidebarInner) this.sidebarInner = false;
              }
              
              if (!this.sidebarInner) {
                var wrapper = document.createElement('div');
                wrapper.setAttribute('class', 'inner-wrapper-sticky');
                this.sidebar.appendChild(wrapper);
                
                while (this.sidebar.firstChild != wrapper) {
                  wrapper.appendChild(this.sidebar.firstChild);
                }this.sidebarInner = this.sidebar.querySelector('.inner-wrapper-sticky');
              }
              
              // Container wrapper of the sidebar.
              if (this.options.containerSelector) {
                var containers = document.querySelectorAll(this.options.containerSelector);
                containers = Array.prototype.slice.call(containers);
                
                containers.forEach(function (container, item) {
                  if (!container.contains(_this2.sidebar)) return;
                  _this2.container = container;
                });
                
                if (!containers.length) throw new Error("The container does not contains on the sidebar.");
              }
              
              // If top/bottom spacing is not function parse value to integer.
              if ('function' !== typeof this.options.topSpacing) this.options.topSpacing = parseInt(this.options.topSpacing) || 0;
              
              if ('function' !== typeof this.options.bottomSpacing) this.options.bottomSpacing = parseInt(this.options.bottomSpacing) || 0;
              
              // Breakdown sticky sidebar if screen width below `options.minWidth`.
              this._widthBreakpoint();
              
              // Calculate dimensions of sidebar, container and viewport.
              this.calcDimensions();
              
              // Affix sidebar in proper position.
              this.stickyPosition();
              
              // Bind all events.
              this.bindEvents();
              
              // Inform other properties the sticky sidebar is initialized.
              this._initialized = true;
            }
          }, {
            key: 'bindEvents',
            value: function bindEvents() {
              window.addEventListener('resize', this, { passive: true, capture: false });
              window.addEventListener('scroll', this, { passive: true, capture: false });
              
              this.sidebar.addEventListener('update' + EVENT_KEY, this);
              
              if (this.options.resizeSensor && 'undefined' !== typeof ResizeSensor) {
                new ResizeSensor(this.sidebarInner, this.handleEvent);
                new ResizeSensor(this.container, this.handleEvent);
              }
            }
          }, {
            key: 'handleEvent',
            value: function handleEvent(event) {
              this.updateSticky(event);
            }
          }, {
            key: 'calcDimensions',
            value: function calcDimensions() {
              if (this._breakpoint) return;
              var dims = this.dimensions;
              
              // Container of sticky sidebar dimensions.
              dims.containerTop = StickySidebar.offsetRelative(this.container).top;
              dims.containerHeight = this.container.clientHeight;
              dims.containerBottom = dims.containerTop + dims.containerHeight;
              
              // Sidebar dimensions.
              dims.sidebarHeight = this.sidebarInner.offsetHeight;
              dims.sidebarWidth = this.sidebarInner.offsetWidth;
              
              // Screen viewport dimensions.
              dims.viewportHeight = window.innerHeight;
              
              // Maximum sidebar translate Y.
              dims.maxTranslateY = dims.containerHeight - dims.sidebarHeight;
              
              this._calcDimensionsWithScroll();
            }
          }, {
            key: '_calcDimensionsWithScroll',
            value: function _calcDimensionsWithScroll() {
              var dims = this.dimensions;
              
              dims.sidebarLeft = StickySidebar.offsetRelative(this.sidebar).left;
              
              dims.viewportTop = document.documentElement.scrollTop || document.body.scrollTop;
              dims.viewportBottom = dims.viewportTop + dims.viewportHeight;
              dims.viewportLeft = document.documentElement.scrollLeft || document.body.scrollLeft;
              
              dims.topSpacing = this.options.topSpacing;
              dims.bottomSpacing = this.options.bottomSpacing;
              
              if ('function' === typeof dims.topSpacing) dims.topSpacing = parseInt(dims.topSpacing(this.sidebar)) || 0;
              
              if ('function' === typeof dims.bottomSpacing) dims.bottomSpacing = parseInt(dims.bottomSpacing(this.sidebar)) || 0;
              
              if ('VIEWPORT-TOP' === this.affixedType) {
                // Adjust translate Y in the case decrease top spacing value.
                if (dims.topSpacing < dims.lastTopSpacing) {
                  dims.translateY += dims.lastTopSpacing - dims.topSpacing;
                  this._reStyle = true;
                }
              } else if ('VIEWPORT-BOTTOM' === this.affixedType) {
                // Adjust translate Y in the case decrease bottom spacing value.
                if (dims.bottomSpacing < dims.lastBottomSpacing) {
                  dims.translateY += dims.lastBottomSpacing - dims.bottomSpacing;
                  this._reStyle = true;
                }
              }
              
              dims.lastTopSpacing = dims.topSpacing;
              dims.lastBottomSpacing = dims.bottomSpacing;
            }
          }, {
            key: 'isSidebarFitsViewport',
            value: function isSidebarFitsViewport() {
              var dims = this.dimensions;
              var offset = this.scrollDirection === 'down' ? dims.lastBottomSpacing : dims.lastTopSpacing;
              return this.dimensions.sidebarHeight + offset < this.dimensions.viewportHeight;
            }
          }, {
            key: 'observeScrollDir',
            value: function observeScrollDir() {
              var dims = this.dimensions;
              if (dims.lastViewportTop === dims.viewportTop) return;
              
              var furthest = 'down' === this.direction ? Math.min : Math.max;
              
              // If the browser is scrolling not in the same direction.
              if (dims.viewportTop === furthest(dims.viewportTop, dims.lastViewportTop)) this.direction = 'down' === this.direction ? 'up' : 'down';
            }
          }, {
            key: 'getAffixType',
            value: function getAffixType() {
              this._calcDimensionsWithScroll();
              var dims = this.dimensions;
              var colliderTop = dims.viewportTop + dims.topSpacing;
              var affixType = this.affixedType;
              
              if (colliderTop <= dims.containerTop || dims.containerHeight <= dims.sidebarHeight) {
                dims.translateY = 0;
                affixType = 'STATIC';
              } else {
                affixType = 'up' === this.direction ? this._getAffixTypeScrollingUp() : this._getAffixTypeScrollingDown();
              }
              
              // Make sure the translate Y is not bigger than container height.
              dims.translateY = Math.max(0, dims.translateY);
              dims.translateY = Math.min(dims.containerHeight, dims.translateY);
              dims.translateY = Math.round(dims.translateY);
              
              dims.lastViewportTop = dims.viewportTop;
              return affixType;
            }
          }, {
            key: '_getAffixTypeScrollingDown',
            value: function _getAffixTypeScrollingDown() {
              var dims = this.dimensions;
              var sidebarBottom = dims.sidebarHeight + dims.containerTop;
              var colliderTop = dims.viewportTop + dims.topSpacing;
              var colliderBottom = dims.viewportBottom - dims.bottomSpacing;
              var affixType = this.affixedType;
              
              if (this.isSidebarFitsViewport()) {
                if (dims.sidebarHeight + colliderTop >= dims.containerBottom) {
                  dims.translateY = dims.containerBottom - sidebarBottom;
                  affixType = 'CONTAINER-BOTTOM';
                } else if (colliderTop >= dims.containerTop) {
                  dims.translateY = colliderTop - dims.containerTop;
                  affixType = 'VIEWPORT-TOP';
                }
              } else {
                if (dims.containerBottom <= colliderBottom) {
                  dims.translateY = dims.containerBottom - sidebarBottom;
                  affixType = 'CONTAINER-BOTTOM';
                } else if (sidebarBottom + dims.translateY <= colliderBottom) {
                  dims.translateY = colliderBottom - sidebarBottom;
                  affixType = 'VIEWPORT-BOTTOM';
                } else if (dims.containerTop + dims.translateY <= colliderTop && 0 !== dims.translateY && dims.maxTranslateY !== dims.translateY) {
                  affixType = 'VIEWPORT-UNBOTTOM';
                }
              }
              
              return affixType;
            }
          }, {
            key: '_getAffixTypeScrollingUp',
            value: function _getAffixTypeScrollingUp() {
              var dims = this.dimensions;
              var sidebarBottom = dims.sidebarHeight + dims.containerTop;
              var colliderTop = dims.viewportTop + dims.topSpacing;
              var colliderBottom = dims.viewportBottom - dims.bottomSpacing;
              var affixType = this.affixedType;
              
              if (colliderTop <= dims.translateY + dims.containerTop) {
                dims.translateY = colliderTop - dims.containerTop;
                affixType = 'VIEWPORT-TOP';
              } else if (dims.containerBottom <= colliderBottom) {
                dims.translateY = dims.containerBottom - sidebarBottom;
                affixType = 'CONTAINER-BOTTOM';
              } else if (!this.isSidebarFitsViewport()) {
                
                if (dims.containerTop <= colliderTop && 0 !== dims.translateY && dims.maxTranslateY !== dims.translateY) {
                  affixType = 'VIEWPORT-UNBOTTOM';
                }
              }
              
              return affixType;
            }
          }, {
            key: '_getStyle',
            value: function _getStyle(affixType) {
              if ('undefined' === typeof affixType) return;
              
              var style = { inner: {}, outer: {} };
              var dims = this.dimensions;
              
              switch (affixType) {
                case 'VIEWPORT-TOP':
                  style.inner = { position: 'fixed', top: dims.topSpacing,
                    left: dims.sidebarLeft - dims.viewportLeft, width: dims.sidebarWidth };
                  break;
                case 'VIEWPORT-BOTTOM':
                  style.inner = { position: 'fixed', top: 'auto', left: dims.sidebarLeft,
                    bottom: dims.bottomSpacing, width: dims.sidebarWidth };
                  break;
                case 'CONTAINER-BOTTOM':
                case 'VIEWPORT-UNBOTTOM':
                  var translate = this._getTranslate(0, dims.translateY + 'px');
                  
                  if (translate) style.inner = { transform: translate };else style.inner = { position: 'absolute', top: dims.translateY, width: dims.sidebarWidth };
                  break;
              }
              
              switch (affixType) {
                case 'VIEWPORT-TOP':
                case 'VIEWPORT-BOTTOM':
                case 'VIEWPORT-UNBOTTOM':
                case 'CONTAINER-BOTTOM':
                  style.outer = { height: dims.sidebarHeight, position: 'relative' };
                  break;
              }
              
              style.outer = StickySidebar.extend({ height: '', position: '' }, style.outer);
              style.inner = StickySidebar.extend({ position: 'relative', top: '', left: '',
                bottom: '', width: '', transform: '' }, style.inner);
              
              return style;
            }
          }, {
            key: 'stickyPosition',
            value: function stickyPosition(force) {
              if (this._breakpoint) return;
              
              force = this._reStyle || force || false;
              
              var offsetTop = this.options.topSpacing;
              var offsetBottom = this.options.bottomSpacing;
              
              var affixType = this.getAffixType();
              var style = this._getStyle(affixType);
              
              if ((this.affixedType != affixType || force) && affixType) {
                var affixEvent = 'affix.' + affixType.toLowerCase().replace('viewport-', '') + EVENT_KEY;
                StickySidebar.eventTrigger(this.sidebar, affixEvent);
                
                if ('STATIC' === affixType) StickySidebar.removeClass(this.sidebar, this.options.stickyClass);else StickySidebar.addClass(this.sidebar, this.options.stickyClass);
                
                for (var key in style.outer) {
                  var unit = 'number' === typeof style.outer[key] ? 'px' : '';
                  this.sidebar.style[key] = style.outer[key] + unit;
                }
                
                for (var _key in style.inner) {
                  var _unit = 'number' === typeof style.inner[_key] ? 'px' : '';
                  this.sidebarInner.style[_key] = style.inner[_key] + _unit;
                }
                
                var affixedEvent = 'affixed.' + affixType.toLowerCase().replace('viewport-', '') + EVENT_KEY;
                StickySidebar.eventTrigger(this.sidebar, affixedEvent);
              } else {
                if (this._initialized) this.sidebarInner.style.left = style.inner.left;
              }
              
              this.affixedType = affixType;
            }
          }, {
            key: '_widthBreakpoint',
            value: function _widthBreakpoint() {
              
              if (window.innerWidth <= this.options.minWidth) {
                this._breakpoint = true;
                this.affixedType = 'STATIC';
                
                this.sidebar.removeAttribute('style');
                StickySidebar.removeClass(this.sidebar, this.options.stickyClass);
                this.sidebarInner.removeAttribute('style');
              } else {
                this._breakpoint = false;
              }
            }
          }, {
            key: 'updateSticky',
            value: function updateSticky() {
              var _this3 = this;
              
              var event = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
              
              if (this._running) return;
              this._running = true;
              
              (function (eventType) {
                requestAnimationFrame(function () {
                  switch (eventType) {
                    // When browser is scrolling and re-calculate just dimensions
                    // within scroll.
                    case 'scroll':
                      _this3._calcDimensionsWithScroll();
                      _this3.observeScrollDir();
                      _this3.stickyPosition();
                      break;
                    
                    // When browser is resizing or there's no event, observe width
                    // breakpoint and re-calculate dimensions.
                    case 'resize':
                    default:
                      _this3._widthBreakpoint();
                      _this3.calcDimensions();
                      _this3.stickyPosition(true);
                      break;
                  }
                  _this3._running = false;
                });
              })(event.type);
            }
          }, {
            key: '_setSupportFeatures',
            value: function _setSupportFeatures() {
              var support = this.support;
              
              support.transform = StickySidebar.supportTransform();
              support.transform3d = StickySidebar.supportTransform(true);
            }
          }, {
            key: '_getTranslate',
            value: function _getTranslate() {
              var y = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
              var x = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
              var z = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
              
              if (this.support.transform3d) return 'translate3d(' + y + ', ' + x + ', ' + z + ')';else if (this.support.translate) return 'translate(' + y + ', ' + x + ')';else return false;
            }
          }, {
            key: 'destroy',
            value: function destroy() {
              window.removeEventListener('resize', this, { capture: false });
              window.removeEventListener('scroll', this, { capture: false });
              
              this.sidebar.classList.remove(this.options.stickyClass);
              this.sidebar.style.minHeight = '';
              
              this.sidebar.removeEventListener('update' + EVENT_KEY, this);
              
              var styleReset = { inner: {}, outer: {} };
              
              styleReset.inner = { position: '', top: '', left: '', bottom: '', width: '', transform: '' };
              styleReset.outer = { height: '', position: '' };
              
              for (var key in styleReset.outer) {
                this.sidebar.style[key] = styleReset.outer[key];
              }for (var _key2 in styleReset.inner) {
                this.sidebarInner.style[_key2] = styleReset.inner[_key2];
              }if (this.options.resizeSensor && 'undefined' !== typeof ResizeSensor) {
                ResizeSensor.detach(this.sidebarInner, this.handleEvent);
                ResizeSensor.detach(this.container, this.handleEvent);
              }
            }
          }], [{
            key: 'supportTransform',
            value: function supportTransform(transform3d) {
              var result = false,
                property = transform3d ? 'perspective' : 'transform',
                upper = property.charAt(0).toUpperCase() + property.slice(1),
                prefixes = ['Webkit', 'Moz', 'O', 'ms'],
                support = document.createElement('support'),
                style = support.style;
              
              (property + ' ' + prefixes.join(upper + ' ') + upper).split(' ').forEach(function (property, i) {
                if (style[property] !== undefined) {
                  result = property;
                  return false;
                }
              });
              return result;
            }
          }, {
            key: 'eventTrigger',
            value: function eventTrigger(element, eventName, data) {
              try {
                var event = new CustomEvent(eventName, { detail: data });
              } catch (e) {
                var event = document.createEvent('CustomEvent');
                event.initCustomEvent(eventName, true, true, data);
              }
              element.dispatchEvent(event);
            }
          }, {
            key: 'extend',
            value: function extend(defaults, options) {
              var results = {};
              for (var key in defaults) {
                if ('undefined' !== typeof options[key]) results[key] = options[key];else results[key] = defaults[key];
              }
              return results;
            }
          }, {
            key: 'offsetRelative',
            value: function offsetRelative(element) {
              var result = { left: 0, top: 0 };
              
              do {
                var offsetTop = element.offsetTop;
                var offsetLeft = element.offsetLeft;
                
                if (!isNaN(offsetTop)) result.top += offsetTop;
                
                if (!isNaN(offsetLeft)) result.left += offsetLeft;
                
                element = 'BODY' === element.tagName ? element.parentElement : element.offsetParent;
              } while (element);
              return result;
            }
          }, {
            key: 'addClass',
            value: function addClass(element, className) {
              if (!StickySidebar.hasClass(element, className)) {
                if (element.classList) element.classList.add(className);else element.className += ' ' + className;
              }
            }
          }, {
            key: 'removeClass',
            value: function removeClass(element, className) {
              if (StickySidebar.hasClass(element, className)) {
                if (element.classList) element.classList.remove(className);else element.className = element.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
              }
            }
          }, {
            key: 'hasClass',
            value: function hasClass(element, className) {
              if (element.classList) return element.classList.contains(className);else return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
            }
          }, {
            key: 'defaults',
            get: function () {
              return DEFAULTS;
            }
          }]);
          
          return StickySidebar;
        }();
        
        return StickySidebar;
      }();
      
      exports.default = StickySidebar;
      
      
      // Global
      // -------------------------
      window.StickySidebar = StickySidebar;
    });
  });
  
  var stickySidebar$1 = unwrapExports(stickySidebar);
  
  exports['default'] = stickySidebar$1;
  exports.__moduleExports = stickySidebar;
  
  Object.defineProperty(exports, '__esModule', { value: true });
  
})));

/* End of sticky sidebar */



